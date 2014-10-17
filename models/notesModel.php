<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');
class notes extends DBManagerModel{
	
    public function getList($params = array()){
        $entity = $this->entity();
        if(!array_key_exists('filter', $params))
                $params["filter"] = 0;

        $start = $params["limit"] * $params["page"] - $params["limit"];
        $query = "SELECT  `noteId`, `name`, `date_entered`, `display_name` AS created_user, `noteTypeId`,  `description`, created_by, ". $params["parent"] ." parentId, '". $params["parentRelationShip"] ."' parentRelationShip  
                          FROM  `".$entity["tableName"]."` n
                          JOIN ".$this->wpPrefix."users u ON u.ID = n.created_by
                          WHERE  deleted = 0 AND `noteId` IN ( ". $params["filter"] ." )";

        if(array_key_exists('where', $params)){
            if (is_array( $params["where"]->rules )){
                $countRules = count($params["where"]->rules);
                for($i = 0; $i < $countRules; $i++){
                    if($params["where"]->rules[$i]->field == "created_by")
                        $params["where"]->rules[$i]->field = "display_name";
                }
            }
            
           $query .= " AND (". $this->buildWhere($params["where"]) .")";
        }
        
        return $this->getDataGrid($query, $start, $params["limit"] , $params["sidx"], $params["sord"] );
    }

    public function getNonConformitiesNotes($params = array()){
        $DataArray= array();
        $query = "SELECT  `noteId`
                  FROM  `".$this->pluginPrefix."nonConformities_notes` n
                  WHERE  `nonConformityId` = " . $params["filter"];

        $responce = $this->getDataGrid($query);

        foreach ( $responce["data"] as $k => $v ){
                $DataArray[] = $responce["data"][$k]->noteId;
        }

        $params["parentRelationShip"] = "nonConformity";
        $params["parent"] = $params["filter"];
        $params["filter"] = implode(",", $DataArray);

        $data = $this->getList($params);
        return $data;
    }
    
    public function getTasksNotes($params = array()){
        $DataArray= array();
        $query = "SELECT  `noteId`
                  FROM  `".$this->pluginPrefix."tasks_notes` n
                  WHERE  `taskId` = " . $params["filter"];

        $responce = $this->getDataGrid($query);

        foreach ( $responce["data"] as $k => $v ){
                $DataArray[] = $responce["data"][$k]->noteId;
        }

        $params["parentRelationShip"] = "tasks";
        $params["parent"] = $params["filter"];
        $params["filter"] = implode(",", $DataArray);

        $data = $this->getList($params);
        return $data;
    }
    
    public function getActionRequestsNotes($params = array()){
        $DataArray= array();
        $query = "SELECT  `noteId`
                  FROM  `".$this->pluginPrefix."actionRequests_notes` n
                  WHERE  `actionRequestId` = " . $params["filter"];

        $responce = $this->getDataGrid($query);

        foreach ( $responce["data"] as $k => $v ){
                $DataArray[] = $responce["data"][$k]->noteId;
        }

        $params["parentRelationShip"] = "tasks";
        $params["parent"] = $params["filter"];
        $params["filter"] = implode(",", $DataArray);

        $data = $this->getList($params);
        return $data;
    }

    public function add(){
        $entityObj = $this->entity();
        $relEntity = $entityObj["relationship"][$_POST["parentRelationShip"]];
        
        $this->addRecord($entityObj, $_POST, array("date_entered" => date("Y-m-d H:i:s"), "created_by" => $this->currentUser->ID));
        $this->addRecord($relEntity, array($relEntity["parent"]["Id"] => $_POST["parentId"],"noteId" => $this->LastId), array());
    }
    public function edit(){
        $entityObj = $this->entity();
        $this->updateRecord($entityObj, $_POST, array("noteId" => $_POST["noteId"]), array("columnValidateEdit" => $entityObj["columnValidateEdit"]));
    }
    public function del(){
        $entityObj = $this->entity();
        $this->delRecord($entityObj, array("noteId" => $_POST["id"]), array("columnValidateEdit" => $entityObj["columnValidateEdit"]));
    }
    public function detail(){}
    public function entity($CRUD = array())
    {
        $data = array(
                        "tableName" => $this->pluginPrefix."notes"
                        ,"columnValidateEdit" => "created_by"
                        ,"entityConfig" => $CRUD
                        ,"atributes" => array(
                                        "noteId" => array("type" => "int", "PK" => 0, "required" => false, "readOnly" => true, "autoIncrement" => true )
                                        ,"name" => array("type" => "varchar", "required" => true)
                                        ,"date_entered" => array("type" => "datetime", "required" => false, "readOnly" => true )
                                        ,"created_user" => array("type" => "varchar", "required" => false, "readOnly" => true, "update" => false, "isTableCol" => false)
                                        ,"noteTypeId" => array("type" => "int", "required" => true, "references" => array("table" => $this->pluginPrefix."noteTypes", "id" => "noteTypeId", "text" => "noteType"))
                                        ,"description" => array("type" => "varchar", "required" => true, "text" => true, "hidden" => true)
                                        ,"created_by" => array("type" => "int", "required" => false, "hidden" => true )            
                                        ,"parentId" => array("type" => "int","required" => false, "hidden" => true, "isTableCol" => false)
                                        ,"parentRelationShip" => array("type" => "varchar","required" => false, "hidden" => true, "isTableCol" => false)
                                    )
                        ,"relationship" => array(
                                "nonConformity" => array(
                                        "tableName" => $this->pluginPrefix."nonConformities_notes"
                                        ,"parent" => array("tableName" => $this->pluginPrefix."nonConformities", "Id" => "nonConformityId")
                                        ,"atributes" => array(
                                            "nonConformityId" => array("type" => "int", "PK" => 0)
                                            ,"noteId" => array("type" => "int", "PK" => 0)
                                        )
                                    )
                                ,"request" => array(
                                        "tableName" => $this->pluginPrefix."nonConformities_notes"
                                        ,"parent" => array("tableName" => $this->pluginPrefix."nonConformities", "Id" => "nonConformityId")
                                        ,"atributes" => array(
                                            "nonConformityId" => array("type" => "int", "PK" => 0)
                                            ,"noteId" => array("type" => "int", "PK" => 0)
                                        )
                                    )
                                ,"tasks" => array(
                                        "tableName" => $this->pluginPrefix."tasks_notes"
                                        ,"parent" => array("tableName" => $this->pluginPrefix."tasks", "Id" => "taskId")
                                        ,"atributes" => array(
                                            "taskId" => array("type" => "int", "PK" => 0)
                                            ,"noteId" => array("type" => "int", "PK" => 0)
                                        )
                                    )
                                ,"actionRequest" => array(
                                        "tableName" => $this->pluginPrefix."actionRequests_notes"
                                        ,"parent" => array("tableName" => $this->pluginPrefix."actionRequests", "Id" => "actionRequestId")
                                        ,"atributes" => array(
                                            "actionRequestId" => array("type" => "int", "PK" => 0)
                                            ,"noteId" => array("type" => "int", "PK" => 0)
                                        )
                                    )
                            )
                    );
        return $data;
    }
}
?>
