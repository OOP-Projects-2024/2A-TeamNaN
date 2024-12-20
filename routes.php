<?php

// Import required files
require_once "./config/database.php";
require_once "./modules/Get.php";
require_once "./modules/Post.php";
require_once "./modules/Patch.php";
require_once "./modules/Delete.php";
require_once "./modules/Auth.php";
require_once "./modules/Crypt.php";

$db = new Connection();
$pdo = $db->connect();


$post = new Post($pdo);
$patch = new Patch($pdo);
$get = new Get($pdo);
$delete = new Delete($pdo);
$auth = new Authentication($pdo);
$crypt = new Crypt();

if (isset($_REQUEST['request'])) {
    $request = explode("/", $_REQUEST['request']);
} else {
    echo "URL does not exist.";
    exit;
}



switch ($_SERVER['REQUEST_METHOD']) {

    case "GET":
        if ($auth->isAuthorized()) {
            switch ($request[0]) {
                case "campaigns": 
                    if (isset($request[1])) {
                        if ($request[1] === "by_user" && isset($request[2])) {
                            $dataString = json_encode($get->getCampaigns(null, $request[2], null));

                        } elseif ($request[1] === "status" && isset($request[2])) {
                            $dataString = json_encode($get->getCampaigns(null, null, $request[2]));

                        } elseif (isset($request[1])) {
                            $dataString = json_encode($get->getCampaigns($request[1]));
                            
                        } else {
                            http_response_code(400);
                            echo "Invalid campaigns endpoint.";
                            exit;
                        }
                } else {
                    $dataString = json_encode($get->getCampaigns());
                }
                echo $dataString;
                break;
                case "mycampaigns":
                    if (isset($request[1])) {
                       if ($request[1] === "status" && isset($request[2])) {
                            $dataString = json_encode($get->getMyCampaigns(null, $request[2]));

                        } elseif (isset($request[1])) {
                            $dataString = json_encode($get->getMyCampaigns($request[1]));

                        } else {
                            http_response_code(400);
                            echo "Invalid campaigns endpoint.";
                            exit;
                        }
                    } else {
                        $dataString = json_encode($get->getMyCampaigns());
                    }
                    echo $dataString;
                    break;
                
    
                case "pledges":
                    if (isset($request[1])) {
                        if ($request[1] === "payment_status" && isset($request[2])) {
                            $dataString = json_encode($get->getPledges(null, $request[2], null, null));

                        } elseif ($request[1] === "refund_status" && isset($request[2])) {
                            $dataString = json_encode($get->getPledges(null, null, $request[2]), null);

                        } elseif ($request[1] === "by_campaign" && isset($request[2])) {
        
                            $dataString = json_encode($get->getPledges($request[2], null, null, null));
                        } elseif (isset($request[1])) {
                            $dataString = json_encode($get->getPledges(null, null, null, $request[1]));
                            
                        } else {
                            http_response_code(400);
                            echo "Invalid pledges endpoint.";
                            exit;
                        }
                    } else {
                        $dataString = json_encode($get->getPledges());
                    }
                    echo $dataString;
                    break;

                case "pledgestomycampaign":
                    if (isset($request[1])) {
                        if ($request[1] === "payment_status" && isset($request[2])) {
                            $dataString = json_encode($get->getMyCampaignsPledges(null, $request[2], null, null));

                        } elseif ($request[1] === "refund_status" && isset($request[2])) {
                            $dataString = json_encode($get->getMyCampaignsPledges(null, null, $request[2]), null);

                        } elseif ($request[1] === "by_campaign" && isset($request[2])) {
        
                            $dataString = json_encode($get->getMyCampaignsPledges($request[2], null, null, null));
                        } elseif (isset($request[1])) {
                            $dataString = json_encode($get->getMyCampaignsPledges(null, null, null, $request[1]));
                            
                        } else {
                            http_response_code(400);
                            echo "Invalid pledges endpoint.";
                            exit;
                        }
                    } else {
                
                        $dataString = json_encode($get->getMyCampaignsPledges());
                    }
                    echo $dataString;
                    break;
    

                case "mypledges":
                    if (isset($request[1])) {
                        if ($request[1] === "payment_status" && isset($request[2])) {
                            $dataString = json_encode($get->getMyPledges(null, $request[2], null, null));

                        } elseif ($request[1] === "refund_status" && isset($request[2])) {
                            $dataString = json_encode($get->getMyPledges(null, null, $request[2]), null);

                        } elseif ($request[1] === "by_campaign" && isset($request[2])) {
    
                            $dataString = json_encode($get->getMyPledges($request[2], null, null, null));
                        } elseif (isset($request[1])) {
                            $dataString = json_encode($get->getMyPledges(null, null, null, $request[1]));
                            
                        } else {
                            http_response_code(400);
                            echo "Invalid pledges endpoint.";
                            exit;
                        }
                    } else {

                        $dataString = json_encode($get->getMyPledges());
                    }
                    echo $dataString;
                    break;
    
                case "refund_requests":
                        $dataString = json_encode($get->getRefundRequests());
                        echo $dataString;
                    break;
    
                case "payment_requests":
                        $dataString = json_encode($get->getPaymentRequests());
                        echo $dataString;
                    break;
    
                case "logs":
                    echo json_encode($get->getLogs($request[1] ?? date("Y-m-d")));
                    break;
    
                default:
                    http_response_code(400);
                    echo "Invalid endpoint.";
                    break;
            }
        } else {
            http_response_code(401);
            echo "Unauthorized access.";
        }
        break;
    

    case "POST":
        case "POST":
            $body = json_decode(file_get_contents("php://input"), true);
        
            switch ($request[0]) {
                case "login":
                    echo json_encode($auth->login($body));
                    break;
        
                case "register":
                    echo json_encode($auth->addAccount($body));
                    break;
        
                default:

                    if ($auth->isAuthorized()) {
                        switch ($request[0]) {
                            case "decrypt":
                                echo $crypt->decryptData($body);
                                break;
        
                            case "postcampaign":
                                echo json_encode($post->createCampaign($body));
                                break;
        
                            case "postpledge":
                                echo json_encode($post->createPledge($body));
                                break;
        
                            default:
                                http_response_code(401);
                                echo "Invalid endpoint.";
                                break;
                        }
                    } else {
                        http_response_code(401);
                        echo "Unauthorized access.";
                    }
                    break;
            }
            break;
        
    case "PATCH":
        $body = json_decode(file_get_contents("php://input"), true);
        if ($auth->isAuthorized()) {
            switch ($request[0]) {

                case "updatecampaign":
                    echo json_encode($patch->patchCampaign($body, $request[1]));
                    break;

                case "removecampaign":
                    echo json_encode($patch->requestRemoveCampaign($request[1]));
                    break;

                case "approveremovecampaign":
                    echo json_encode($patch->approveRemoveCampaign($request[1]));
                    break;

                case "refund":
                    echo json_encode($patch->requestRefund($body));
                    break;

                case "valrefund":
                    echo json_encode($patch->validateRefund($body));
                    break;

                case "valpayment":
                    echo json_encode($patch->validatePayment($body));
                    break;

                default:
                    http_response_code(401);
                    echo "Invalid endpoint.";
                    break;
            }
        } else {
            http_response_code(401);
            echo "Unauthorized access.";
        }

        break;

    case "DELETE":
        if ($auth->isAuthorized()) {
            switch ($request[0]) {
                case "delcampaign":
                    echo json_encode($delete->deleteCampaign($request[1]));
                    break;

                case "delpledge":
                    echo json_encode($delete->deletePledge($request[1]));
                    break;

                default:
                    http_response_code(401);
                    echo "Invalid endpoint.";
                    break;
            }
            
        } else {
            http_response_code(401);
            echo "Unauthorized access.";
        }

        break;

    default:
        http_response_code(400);
        echo "Invalid Request Method.";
        break;
}

$pdo = null;
