<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');
class request extends DBManagerModel{
   
    public function getList($params = array()){
        $entity = $this->entity();
        $start = $params["limit"] * $params["page"] - $params["limit"];
        $query = "SELECT  `nonConformityId`, `name`, `description` 
                            , `estadonc`, `assigned_user_id`, `nombre_del_clientenc`
                            , `telefononc`, `fuentenc`, `generalidadnc`, `sedenc`
                            , `gestion`, `tipo_cliente_c`
                          FROM ".$entity["tableName"]."
                          WHERE `deleted` = 0 AND clasificacion_nc_c = 3";
        
        if(array_key_exists('where', $params))
            $query .= " AND (". $this->buildWhere($params["where"]) .")";
        
        return $this->getDataGrid($query, $start, $params["limit"] , $params["sidx"], $params["sord"]);
    }

    public function add(){
        $_POST["clasificacion_nc_c"] = 3;
        $this->addRecord($this->entity(), $_POST, array("date_entered" => date("Y-m-d H:i:s"), "created_by" => $this->currentUser->ID));
    }
    public function edit(){
        $this->updateRecord($this->entity(), $_POST, array("nonConformityId" => $_POST["nonConformityId"]), array("columnValidateEdit" => "assigned_user_id"));
    }
    public function del(){
        $this->delRecord($this->entity(), array("nonConformityId" => $_POST["id"]), array("columnValidateEdit" => "assigned_user_id"));
    }

    public function detail($params = array()){
        $entity = $this->entity();
        $query = "SELECT n.`nonConformityId` , n.`name` , n.`description` , `status` `estadonc` 
                        , `display_name` `assigned_user_id` , `nombre_del_clientenc` , `telefononc` 
                        , `source` `fuentenc` , `generality` `generalidadnc` , `office` `sedenc` 
                        , m.`management` `gestion`, customerType `tipo_cliente_c`
                    FROM ".$entity["tableName"]." n
                    LEFT JOIN ".$this->pluginPrefix."status s ON s.statusid = n.estadonc
                    LEFT JOIN ".$this->wpPrefix."users u ON u.ID = n.assigned_user_id
                    LEFT JOIN ".$this->pluginPrefix."sources sc ON sc.sourceId = n.fuentenc
                    LEFT JOIN ".$this->pluginPrefix."generalities g ON g.generalityId = n.generalidadnc
                    LEFT JOIN ".$this->pluginPrefix."offices o ON o.officeId = n.sedenc
                    LEFT JOIN ".$this->pluginPrefix."managements m ON m.managementId = n.gestion
                    LEFT JOIN ".$this->pluginPrefix."customerTypes ct ON ct.customerTypeId = n.tipo_cliente_c
                    WHERE n.`nonConformityId` = " . $params["filter"];
        $this->queryType = "row";
        return $this->getDataGrid($query);
    }
    
    public function entity($CRUD = array())
    {
            $data = array(
                            "tableName" => $this->pluginPrefix."nonConformities"
                            ,"columnValidateEdit" => "assigned_user_id"
                            ,"entityConfig" => $CRUD
                            ,"atributes" => array(
                                "nonConformityId" => array("type" => "int", "PK" => 0, "required" => false, "readOnly" => true, "autoIncrement" => true, "toolTip" => array("type" => "cell", "cell" => 2) )
                                ,"name" => array("type" => "varchar", "required" => true)
                                ,"description" => array("type" => "text", "required" => true, "text" => true, "hidden" => true)
                                ,"estadonc" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."status", "id" => "statusid", "text" => "status"))
                                ,"assigned_user_id" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->wpPrefix."users", "id" => "ID", "text" => "display_name"))
                                ,"nombre_del_clientenc" => array("type" => "varchar", "required" => true)
                                ,"telefononc" => array("type" => "varchar", "required" => true)
                                ,"fuentenc" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."sources", "id" => "sourceId", "text" => "source"))
                                ,"generalidadnc" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."generalities", "id" => "generalityId", "text" => "generality"))
                                ,"sedenc" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."offices", "id" => "officeId", "text" => "office"))
                                ,"gestion" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."managements", "id" => "managementId", "text" => "management"))
                                ,"tipo_cliente_c" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."customerTypes", "id" => "customerTypeId", "text" => "customerType"))
                            )
                    );
            return $data;
    }
}
?>
