<?php
class Common {

    protected function getUserDetails()
{
    $headers = getallheaders();

    if (!isset($headers['x-auth-user'])) {
        return [
            "username" => "Unknown User",
            "user_id" => null,
            "role" => null
        ];
    }

    $username = $headers['x-auth-user'];

    $stmt = $this->executeQuery(
        "SELECT id, role FROM user_tbl WHERE username = ?",
        [$username]
    );

    if ($stmt['code'] == 200 && !empty($stmt['data'])) {
        $user = $stmt['data'][0];
        return [
            "username" => $username,
            "user_id" => $user['id'],
            "role" => $user['role']
        ];
    }

    return [
        "username" => "Unknown User",
        "user_id" => null,
        "role" => null
    ];
}

    
    protected function logger($user = null, $userid = null, $role = null, $method, $action) {
        $user = $user ?? $this->getUserDetails()['username'];
        $userid = $userid ?? $this->getUserDetails()['user_id'];
        $role = $role ?? $this->getUserDetails()['role'];

        $filename = date("Y-m-d") . ".log";
        $datetime = date("Y-m-d H:i:s");
        $logMessage = "$datetime, $method, $user, $userid, $role, $action" . PHP_EOL;

        error_log($logMessage, 3, "./logs/$filename");
    }



    private function generateInsertString($tablename, $body) {
        $keys = array_keys($body);
        $fields = implode(",", $keys);
        $parameter_array = array_fill(0, count($keys), "?");
        $parameters = implode(',', $parameter_array);
        $sql = "INSERT INTO $tablename($fields) VALUES ($parameters)";
        return $sql;
    }

    protected function getDataByTable($tableName, $condition, \PDO $pdo) {
        $sqlString = "SELECT * FROM $tableName WHERE $condition";
        $data = [];
        $errmsg = "";
        $code = 0;

        try {
            if ($result = $pdo->query($sqlString)->fetchAll()) {
                $data = $result;
                $code = 200;
                return ["code" => $code, "data" => $data];
            } else {
                $errmsg = "No data found";
                $code = 404;
            }
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            $code = 403;
        }

        return ["code" => $code, "errmsg" => $errmsg];
    }

    protected function getDataBySQL($sqlString, \PDO $pdo) {
        $data = [];
        $errmsg = "";
        $code = 0;

        try {
            if ($result = $pdo->query($sqlString)->fetchAll()) {
                $data = $result;
                $code = 200;
                return ["code" => $code, "data" => $data];
            } else {
                $errmsg = "No data found";
                $code = 404;
            }
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            $code = 403;
        }

        return ["code" => $code, "errmsg" => $errmsg];
    }

    protected function generateResponse($data, $remark, $message, $statusCode) {
        $status = [
            "remark" => $remark,
            "message" => $message
        ];

        http_response_code($statusCode);

        return [
            "payload" => $data,
            "status" => $status,
            "prepared_by" => "Team NaN",
            "date_generated" => date("Y-m-d H:i:s")
        ];
    }

    protected function postData($tableName, $body, \PDO $pdo) {
        $values = array_values($body);
        $errmsg = "";
        $code = 0;

        try {
            $sqlString = $this->generateInsertString($tableName, $body);
            $sql = $pdo->prepare($sqlString);
            $sql->execute($values);
            $code = 200;

            return ["data" => null, "code" => $code];
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            $code = 400;
        }

        return ["errmsg" => $errmsg, "code" => $code];
    }

    protected function executeQuery($sql, $params = []) {
        $data = [];
        $errmsg = "";
        $code = 0;
    
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
    
            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetchAll();
                $code = 200; 
            } else {
                $errmsg = "No data found";
                $code = 404;
            }
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            $code = 400;
        }
    
        return [
            "code" => $code,
            "data" => $data,
            "errmsg" => $errmsg
        ];
    }

    protected function getPledgesForUser($campaign_id = null, $status = null, $refund_status = null, $pledges_id = null, $isCampaignOwner = false, $isAdmin = false) {
        try {
            $userDetails = $this->getUserDetails();
            $user_id = $userDetails['user_id'];
            $role = $userDetails['role'];

            if ($isAdmin) {
                if ($role !== 'admin') {
                    return $this->generateResponse(
                        null, 
                        "failed", 
                        "Only admins can retrieve all pledges.", 
                        403
                    );
                }
                $condition = "1=1";
                $params = [];
            } elseif ($isCampaignOwner) {
                if ($role !== 'campaign_owner') {
                    return $this->generateResponse(
                        null, 
                        "failed", 
                        "You are not authorized to view these pledges.", 
                        403
                    );
                }

                $campaignQuery = "SELECT id FROM campaigns_tbl WHERE user_id = ?";
                $campaignsResult = $this->executeQuery($campaignQuery, [$user_id]);

                if ($campaignsResult['code'] !== 200 || empty($campaignsResult['data'])) {
                    return $this->generateResponse(
                        null, 
                        "failed", 
                        "No campaigns found for the user.", 
                        404);
                }

                $campaignIds = array_column($campaignsResult['data'], 'id');

                $condition = "campaign_id IN (" . implode(',', array_fill(0, count($campaignIds), '?')) . ")";
                $params = $campaignIds;
            } else {
                $condition = "user_id = ?";
                $params = [$user_id];
            }

            if ($pledges_id !== null) {
                $condition .= " AND id = ?";
                $params[] = $pledges_id;
            }

            if ($campaign_id !== null) {
                $condition .= " AND campaign_id = ?";
                $params[] = $campaign_id;
            }

            if ($status !== null) {
                $condition .= " AND payment_status = ?";
                $params[] = $status;
            }

            if ($refund_status !== null) {
                $condition .= " AND refund_status = ?";
                $params[] = $refund_status;
            }

            $result = $this->executeQuery(
                "SELECT * FROM pledges_tbl WHERE $condition", 
                $params
            );

            if ($result['code'] === 200) {
                return $this->generateResponse(
                    $result['data'], 
                    "success", 
                    "Successfully retrieved pledges.", 
                    200
                );
            }

            return $this->generateResponse(
                null, 
                "failed", 
                $result['errmsg'], 
                $result['code']
            );
        } catch (\PDOException $e) {
            $this->logger(null, null, null, "GET", $e->getMessage());
            return $this->generateResponse(null, "failed", $e->getMessage(), 400);
        }
    }

    protected function getCampaignsForUser($campaign_id = null, $user_id_filter = null, $status = null, $isCampaignOwner = false, $regular_user = true) {
        try {

            $userDetails = $this->getUserDetails();
            $user_id = $userDetails['user_id'];
            $role = $userDetails['role'];

            $condition = "1=1";
            $params = [];

            if ($role !== 'admin'){
                $condition = "status != 'archived'";
                $params = [];
            }

            if (!$regular_user) {
                if ($isCampaignOwner) {
                    $condition = "status != 'archived' AND user_id = ?";
                    $params = [$user_id];
                } else {
                    // Default: Unauthorized
                    return $this->generateResponse(
                        null, 
                        "failed", 
                        "You are not authorized to view these campaigns.", 
                        403
                    );
                }
            }

            if ($campaign_id !== null) {
                $condition .= " AND id = ?";
                $params[] = $campaign_id;
            }
            if ($status !== null) {
                $condition .= " AND status = ?";
                $params[] = $status;
            }
            if ($user_id_filter !== null) {
                $condition .= " AND user_id = ?";
                $params[] = $user_id_filter;
            }

            $result = $this->executeQuery(
                "SELECT * FROM campaigns_tbl WHERE $condition", 
                $params
            );

            if ($result['code'] === 200) {
                $message = "Successfully retrieved all campaigns.";
                return $this->generateResponse(
                    $result['data'], "success", 
                    $message, 
                    200
                );
            }

            return $this->generateResponse(
                null, 
                "failed", 
                $result['errmsg'], 
                $result['code']
            );
        } catch (\PDOException $e) {
            $this->logger(null, null, null, "GET", $e->getMessage());
            return $this->generateResponse(null, "failed", $e->getMessage(), 400);
        }
    }




    
}
?>
