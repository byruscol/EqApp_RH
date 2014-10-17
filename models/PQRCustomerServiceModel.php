<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');
class PQRCustomerService extends DBManagerModel{
	
    public function getList($params = array()){
        $entity = $this->entity();
        if(!array_key_exists('filter', $params))
                $params["filter"] = 0;

        $start = $params["limit"] * $params["page"] - $params["limit"];
        $query = "SELECT n.`taskId`, `name`, `status`, `priority`, `date_entered`, `display_name` AS `created_by`
                                         , `assigned_user_id`, `date_start`,  `date_due`
                                         , `Expired` ExpiredStatus, `description`, `taskType` 
                          FROM  `".$entity["tableName"]."` n
                          JOIN ".$this->wpPrefix."users u ON u.ID = n.created_by
                          JOIN ".$this->pluginPrefix."taskTypes t ON t.taskTypeId = n.taskTypeId
                          LEFT JOIN timetasksstatus tts On tts.taskId = n.taskId
                          WHERE  `deleted` = 0 AND n.`taskId` IN ( ". $params["filter"] ." )";
        if(array_key_exists('where', $params)){
            if (is_array( $params["where"]->rules )){
                $countRules = count($params["where"]->rules);
                for($i = 0; $i < $countRules; $i++){
                    if($params["where"]->rules[$i]->field == "created_by")
                        $params["where"]->rules[$i]->field = "display_name";
                    if($params["where"]->rules[$i]->field == "ExpiredStatus")
                        $params["where"]->rules[$i]->field = "Expired";
                }
            }
            
           $query .= " AND (". $this->buildWhere($params["where"]) .")";
        }
        //echo $query;
        return $this->getDataGrid($query, $start, $params["limit"] , $params["sidx"], $params["sord"] );
    }

    public function getNonConformitiesTasks($params = array()){
        $query = "SELECT  `taskId`
                  FROM  `".$this->pluginPrefix."nonConformities_tasks`
                  WHERE  `nonConformityId` = " . $params["filter"];

        $responce = $this->getDataGrid($query);

        foreach ( $responce["data"] as $k => $v ){
                $DataArray[] = $responce["data"][$k]->taskId;
        }

        $params["filter"] = implode(",", $DataArray);

        $data = $this->getList($params);
        return $data;
    }
    
    public function getActionRequetsTasks($params = array()){
        $query = "SELECT  `taskId`
                  FROM  `".$this->pluginPrefix."actionRequests_tasks`
                  WHERE  `actionRequestId` = " . $params["filter"];

        $responce = $this->getDataGrid($query);

        foreach ( $responce["data"] as $k => $v ){
                $DataArray[] = $responce["data"][$k]->taskId;
        }

        $params["filter"] = implode(",", $DataArray);

        $data = $this->getList($params);
        return $data;
    }

    public function getMyTasks($params = array()){
        $entity = $this->entity();
        $query = "SELECT  `taskId`
                  FROM  `".$entity["tableName"]."`
                  WHERE  `assigned_user_id` = " . $this->currentUser->ID;

        $responce = $this->getDataGrid($query);

        foreach ( $responce["data"] as $k => $v ){
                $DataArray[] = $responce["data"][$k]->taskId;
        }

        $params["filter"] = implode(",", $DataArray);

        $data = $this->getList($params);
        return $data;
    }
    
    public function getChart($params = array()){
        
        switch ($params["queryId"])
        {
            case "QPieChart": 
                $query = "SELECT s.status, COUNT( 1 ) Q
                        FROM `".$this->pluginPrefix."nonConformities` n
                            LEFT JOIN `".$this->pluginPrefix."status` s ON s.statusid = n.`estadonc`
                        WHERE n.deleted = 0 AND clasificacion_nc_c = 1
                        GROUP BY `estadonc";
                break;
            case "PPieChart": 
                $query = "SELECT s.status, COUNT( 1 ) Q
                            FROM `".$this->pluginPrefix."nonConformities` n
                                LEFT JOIN `".$this->pluginPrefix."status` s ON s.statusid = n.`estadonc`
                            WHERE n.deleted = 0 AND clasificacion_nc_c = 3
                            GROUP BY `estadonc";
                break;
            case "RPieChart": 
                $query = "SELECT s.status, COUNT( 1 ) Q
                            FROM `".$this->pluginPrefix."nonConformities` n
                                LEFT JOIN `".$this->pluginPrefix."status` s ON s.statusid = n.`estadonc`
                            WHERE n.deleted = 0 AND clasificacion_nc_c = 2
                            GROUP BY `estadonc";
                break;
            case "PQRBarSourceChart":
                $query = "SELECT `source`, COUNT( 1 ) Q
                            FROM `".$this->pluginPrefix."nonConformities` n
                            JOIN `".$this->pluginPrefix."sources` s ON s.sourceId = n.fuentenc
                            WHERE deleted =0
                            GROUP BY `fuentenc` ";
                break;
            /*case "PQRBarCalsifStutsChart"; 
                $query = "SELECT s.status, c.classification, COUNT( 1 ) Q
                            FROM `".$this->pluginPrefix."nonConformities` n
                                JOIN `".$this->pluginPrefix."status` s ON s.statusid = n.`estadonc`
                                JOIN `".$this->pluginPrefix."classifications` c ON c.classificationId = n.clasificacion_nc_c
                            WHERE n.deleted =0
                            GROUP BY classificationId, `estadonc`
                            ORDER BY `estadonc` , classificationId";
                break;*/
        }
        return $this->getDataGrid($query);
    }
    
    public function add(){
        $entityObj = $this->entity();
        $relEntity = $entityObj["relationship"][$_POST["parentRelationShip"]];
        $taskType = $entityObj["relationship"][$_POST["taskType"]];
        
        $this->addRecord($entityObj, $_POST, array("date_entered" => date("Y-m-d H:i:s"), "created_by" => $this->currentUser->ID, "taskTypeId" => $taskType));
        $this->addRecord($relEntity, array($relEntity["parent"]["Id"] => $_POST["parentId"],"taskId" => $this->LastId), array());
    }
    public function edit(){
        $this->updateRecord($this->entity(), $_POST, array("taskId" => $_POST["taskId"]), array("columnValidateEdit" => "assigned_user_id"));
    }
    public function del(){
        $this->delRecord($this->entity(), array("taskId" => $_POST["id"]), array("columnValidateEdit" => "assigned_user_id"));
    }
    public function detail($params = array()){
        $entity = $this->entity();
        $query = "SELECT n.`taskId`, `taskType`, `name`, s.`status`, p.priority `priority`, `date_entered`, u.`display_name` AS `created_by`
                                         , u.`display_name` as `assigned_user_id`, `date_start`,  `date_due`, `Expired` ExpiredStatus, `description` 
                  FROM  `".$entity["tableName"]."` n
                  JOIN ".$this->wpPrefix."users u ON u.ID = n.created_by
                  JOIN ".$this->pluginPrefix."taskTypes t ON t.taskTypeId = n.taskTypeId
                  LEFT JOIN ".$this->pluginPrefix."status s ON s.statusid = n.status
                  LEFT JOIN ".$this->pluginPrefix."priorities p ON p.priorityId = n.priority
                  LEFT JOIN ".$this->wpPrefix."users ua ON ua.ID = n.assigned_user_id
                  LEFT JOIN timetasksstatus tts On tts.taskId = n.taskId
                  WHERE n.taskId = " . $params["filter"];
        $this->queryType = "row";
        return $this->getDataGrid($query);
    }    
    public function entity($CRUD = array())
    {
        $data = array(
                        "tableName" => $this->pluginPrefix."tasks"
                        ,"columnValidateEdit" => "assigned_user_id"
                        ,"entityConfig" => $CRUD
                        ,"atributes" => array(
                            "taskId" => array("type" => "int", "PK" => 0, "required" => false, "readOnly" => true, "autoIncrement" => true )
                            ,"name" => array("type" => "varchar", "required" => true)
                            ,"status" => array("type" => "int", "required" => true, "references" => array("table" => $this->pluginPrefix."status", "id" => "statusId", "text" => "status"))
                            ,"priority" => array("type" => "int", "required" => true, "references" => array("table" => $this->pluginPrefix."priorities", "id" => "priorityId", "text" => "priority"))
                            ,"date_entered" => array("type" => "datetime", "required" => false, "readOnly" => true )
                            ,"created_by" => array("type" => "varchar", "required" => false, "readOnly" => true, "update" => false)
                            ,"assigned_user_id" => array("type" => "int", "required" => true, "references" => array("table" => $this->wpPrefix."users", "id" => "ID", "text" => "display_name"))
                            ,"date_start" => array("type" => "datetime", "required" => true)
                            ,"date_due" => array("type" => "datetime", "required" => true)
                            ,"ExpiredStatus" => array("type" => "varchar","required" => false, "isTableCol" => false, "readOnly" => true)
                            ,"description" => array("type" => "varchar", "required" => true, "text" => true, "hidden" => true)
                            ,"taskType" => array("type" => "varchar","required" => false, "hidden" => true, "isTableCol" => false)
                            ,"parentId" => array("type" => "int","required" => false, "hidden" => true, "isTableCol" => false)
                            ,"parentRelationShip" => array("type" => "varchar","required" => false, "hidden" => true, "isTableCol" => false)
                        )
                        ,"relationship" => array(
                            "nonConformity" => array(
                                    "tableName" => $this->pluginPrefix."nonConformities_tasks"
                                    ,"taskType" => 2
                                    ,"parent" => array("tableName" => $this->pluginPrefix."nonConformities", "Id" => "nonConformityId")
                                    ,"atributes" => array(
                                        "nonConformityId" => array("type" => "int", "PK" => 0)
                                        ,"taskId" => array("type" => "int", "PK" => 0)
                                    )
                                )
                            ,"request" => array(
                                    "tableName" => $this->pluginPrefix."nonConformities_tasks"
                                    ,"taskType" => 1
                                    ,"parent" => array("tableName" => $this->pluginPrefix."nonConformities", "Id" => "nonConformityId")
                                    ,"atributes" => array(
                                        "nonConformityId" => array("type" => "int", "PK" => 0)
                                        ,"taskId" => array("type" => "int", "PK" => 0)
                                    )
                                )
                            ,"actionRequest" => array(
                                    "tableName" => $this->pluginPrefix."actionRequests_tasks"
                                    ,"taskType" => 2
                                    ,"parent" => array("tableName" => $this->pluginPrefix."actionRequests", "Id" => "actionRequestId")
                                    ,"atributes" => array(
                                        "actionRequestId" => array("type" => "int", "PK" => 0)
                                        ,"taskId" => array("type" => "int", "PK" => 0)
                                    )
                                )
                        )
                );
        return $data;
    }
}
?>
