<?php
session_start();
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');

class loadCodes extends DBManagerModel{
	
    public function getList($params = array()){
        $entity = $this->entity();
        if(!array_key_exists('filter', $params))
                $params["filter"] = 0;

        $query = "SELECT n.`codeId`, n.`code`, IF(r.codeId IS NULL , 1, 0) isFree
                    FROM `".$this->pluginPrefix."codes` n 
                             LEFT JOIN `".$entity["tableName"]."` r ON r.codeId = n.codeId
                    WHERE n.`code` IN ( ". $params["filter"] ." )";
        //echo $query;
        return $this->getDataGrid($query);
    }
   
    public function add(){
        if($_POST["captcha"] == $_SESSION['captcha'])
        {
            global $resource;
            $entityObj = $this->entity();
            $codes = "'".(implode("','",$_POST["code"]))."'";
            $params = array("filter" => $codes);
            $data = $this->getList($params);
            $countData = count($data["data"]);
            $countPost = count($_POST["code"]);
            $return = array();
            $exists = false;

            for($i = 0; $i < $countPost; $i++){
                $exists = false;
                for($j = 0; $j < $countData; $j++){
                    if($_POST["code"][$i] == $data["data"][$j]->code && $data["data"][$j]->isFree == "0"){
                        $return[] = array("code" => $data["data"][$j]->code, "status" => "0","statusText" => $resource->getWord("failCode"));
                        $exists = true;
                        break;
                    }
                    elseif($_POST["code"][$i] == $data["data"][$j]->code){
                        $exists = true;
                        $this->LastId = 0;
                        $this->addRecord($entityObj, array("codeId" => $data["data"][$j]->codeId), array("dateTime" => date("Y-m-d H:i:s"), "UserId" => $this->currentUser->ID));
                        if(!empty($this->LastId)){
                            $this->updateRecord($entityObj["relationship"]["codes"], array("validate" => 1), array("codeId" => $data["data"][$j]->codeId));
                            $return[] = array("code" => $data["data"][$j]->code, "status" => "1","statusText" => $resource->getWord("codeLoaded"));
                        }
                        else
                        {
                            $return[] = array("code" => $data["data"][$j]->code, "status" => "0","statusText" => $resource->getWord("failCode"));
                        }
                        break;
                    }
                }

                if($j == $countData && $exists == false){
                    $exists = false;
                    $return[] = array("code" => $_POST["code"][$i], "status" => "0","statusText" => $resource->getWord("failCode") );
                }
            }
            echo json_encode($return);
        }
        else
            echo "captchaFail";
    }
    public function edit(){}
    public function del(){}
    public function detail($params = array()){}    
    public function entity($CRUD = array())
    {
        $data = array(
                    "tableName" => $this->pluginPrefix."registeredCodes"
                    ,"atributes" => array(
                        "codeRegisteredId" => array("type" => "int", "PK" => 0, "required" => false, "readOnly" => true, "autoIncrement" => true )
                        ,"codeId" => array("type" => "int", "required" => true)
                    )
                    ,"relationship" => array(
                        "codes" => array(
                                "tableName" => $this->pluginPrefix."codes"
                                ,"parent" => array("tableName" => $this->pluginPrefix."codes", "Id" => "codeId")
                                ,"atributes" => array(
                                    "codeId" => array("type" => "int", "required" => true, "PK" => 0, "autoIncrement" => true)
                                    ,"validate" => array("type" => "int", "required" => true)
                                )
                            )
                    )
                );
        return $data;
    }
}
?>
