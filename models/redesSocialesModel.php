<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');
class redesSociales extends DBManagerModel{
   
    public function getList($params = array()){
        $entity = $this->entity();
        if( !$this->isRhAdmin) 
                $params["filter"] = $this->currentIntegrante;
        
        $start = $params["limit"] * $params["page"] - $params["limit"];
        $query = "SELECT `IntegrantesRedesSocialesId`,
                        `integranteId`,
                        `redSocialId`,
                        `nick`
                    FROM ".$entity["tableName"]." 
                    WHERE `deleted` = 0 AND `integranteId` = ". $params["filter"];
        
        if(array_key_exists('where', $params)){
            if (is_array( $params["where"]->rules )){
                $countRules = count($params["where"]->rules);
                for($i = 0; $i < $countRules; $i++){
                    switch($params["where"]->rules[$i]->field ){
                        case "created_by": $params["where"]->rules[$i]->field = "display_name"; break;
                        case "edad": $params["where"]->rules[$i]->field = "DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), fechaNacimiento)), '%Y')+0"; break;
                    }
                }
            }
            
           $query .= " AND (". $this->buildWhere($params["where"]) .")";
        }
        
        return $this->getDataGrid($query, $start, $params["limit"] , $params["sidx"], $params["sord"]);
    }
    
    public function add(){
        if( !$this->isRhAdmin) 
                $_POST["integranteId"] = $this->currentIntegrante;
        else
        $_POST["integranteId"] = $_POST["parentId"];
        $this->addRecord($this->entity(), $_POST, array("date_entered" => date("Y-m-d H:i:s"), "created_by" => $this->currentUser->ID));
    }
    public function edit(){
        if( !$this->isRhAdmin) 
                $_POST["integranteId"] = $this->currentIntegrante;
        $this->updateRecord($this->entity(), $_POST, array("IntegrantesRedesSocialesId" => $_POST["IntegrantesRedesSocialesId"]));
    }
    public function del(){
        if( !$this->isRhAdmin) 
                $_POST["integranteId"] = $this->currentIntegrante;
        $this->delRecord($this->entity(), array("IntegrantesRedesSocialesId" => $_POST["id"]));
    }

    public function detail($params = array()){}
    
    public function entity($CRUD = array())
    {
            $data = array(
                            "tableName" => $this->pluginPrefix."IntegrantesRedesSociales"
                            ,"entityConfig" => $CRUD
                            ,"atributes" => array(
                                "IntegrantesRedesSocialesId" => array("type" => "int", "PK" => 0, "required" => false, "readOnly" => true, "autoIncrement" => true)
                                ,"integranteId" => array("type" => "int", "required" => false, "readOnly" => true, "hidden" => true)
                                ,"redSocialId" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."redesSociales", "id" => "redSocialId", "text" => "redSocial"))
                                ,"nick" => array("type" => "varchar", "required" => true)
                                ,"parentId" => array("type" => "int","required" => false, "hidden" => true, "readOnly" => true,"isTableCol" => false)
                            )
                    );
            return $data;
    }
}
?>