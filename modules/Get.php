<?php
include_once "Common.php";

class Get extends Common {

    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getLogs($date){
        $filename = "./logs/" . $date . ".log";
        
        $logs = array();
        try{
            $file = new SplFileObject($filename);
            while(!$file->eof()){
                array_push($logs, $file->fgets());
            }
            $remarks = "success";
            $message = "Successfully retrieved logs.";
        }
        catch(Exception $e){
            $remarks = "failed";
            $message = $e->getMessage();
        }
        

        return $this->generateResponse(array("logs"=>$logs), $remarks, $message, 200);
    }
   
    public function getPledges($campaign_id = null, $status = null, $refund_status = null, $pledges_id = null) {
        return $this->getPledgesForUser($campaign_id, $status, $refund_status, $pledges_id, false, true);
    }

    public function getMyPledges($campaign_id = null, $status = null, $refund_status = null, $pledges_id = null) {
        return $this->getPledgesForUser($campaign_id, $status, $refund_status, $pledges_id, false, false);
    }

    public function getMyCampaignsPledges($campaign_id = null, $status = null, $refund_status = null, $pledges_id = null) {
        return $this->getPledgesForUser($campaign_id, $status, $refund_status, $pledges_id, true, false);
    }

    public function getCampaigns($campaign_id = null, $user_id = null, $status = null) {
        return $this->getCampaignsForUser($campaign_id, $user_id, $status, false, true);
    }
    
    public function getMyCampaigns($campaign_id = null, $status = null) {
        return $this->getCampaignsForUser($campaign_id, null, $status, true, false);
    }
    
    public function getRefundRequests() {
        try {
            if ($this->getUserDetails()['role'] !== 'admin') {
                return $this->generateResponse(
                    null, 
                    "failed", 
                    "Unauthorized access. 
                    Only admins can view refund requests.", 
                    403);
            }
    
            $result = $this->executeQuery(
                "SELECT * FROM pledges_tbl WHERE refund_status = 'pending'"
            );
    
            if ($result['code'] === 200) {
                return $this->generateResponse(
                    $result['data'], 
                    "success", 
                    "Successfully retrieved refund requests.", 
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
    
    public function getPaymentRequests() {
        try {
            if ($this->getUserDetails()['role'] !== 'admin') {
                return $this->generateResponse(
                    null, 
                    "failed", 
                    "Unauthorized access. Only admins can view payment requests.", 
                    403
                );
            }
    
            $result = $this->executeQuery(
                "SELECT * FROM pledges_tbl WHERE payment_status = 'pending'"
            );
    
            if ($result['code'] === 200) {
                return $this->generateResponse(
                    $result['data'], 
                    "success", 
                    "Successfully retrieved payment requests.", 
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