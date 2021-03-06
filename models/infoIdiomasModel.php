<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');
class infoIdiomas extends DBManagerModel{
   
    public function getList($params = array()){
        $entity = $this->entity();
        if(!array_key_exists('filter', $params))
                $params["filter"] = 0;
        
        if( !in_array( "administrator", $currentUserRoles ) && !in_array( "editor", $currentUserRoles )) 
                $params["filter"] = $this->currentIntegrante;
        
        $start = $params["limit"] * $params["page"] - $params["limit"];
        $query = "SELECT i.`infoIdiomaId`, `idioma`, `hablado`, `escrito`,
                         `escucha`,`integranteId`, f.ext soporte, f.fileId, '' file
                    FROM ".$entity["tableName"]." i
			LEFT JOIN ".$this->pluginPrefix."filesInfoIdiomas fi ON fi.infoIdiomaId = i.infoIdiomaId 
			LEFT JOIN ".$this->pluginPrefix."files f ON f.fileId = fi.fileId
                    WHERE i.`deleted` = 0 AND i.`integranteId` = ". $params["filter"];

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
        //echo $query;
        return $this->getDataGrid($query, $start, $params["limit"] , $params["sidx"], $params["sord"] );
    }
    
    public function add(){
        if( !in_array( "administrator", $currentUserRoles ) && !in_array( "editor", $currentUserRoles )) 
                $_POST["integranteId"] = $this->currentIntegrante;
        else
            $_POST["integranteId"] = $_POST["parentId"];
        
        $this->addRecord($this->entity(), $_POST, array("date_entered" => date("Y-m-d H:i:s"), "created_by" => $this->currentUser->ID));
        echo json_encode(array("parentId" => $this->LastId));
    }
    
    public function edit(){
        $entityObj = $this->entity();
        $this->updateRecord($entityObj, $_POST, array("infoIdiomaId" => $_POST["infoIdiomaId"]));
        echo json_encode(array("parentId" => $_POST["infoIdiomaId"]));
    }
    
    public function del(){
        $this->delRecord($this->entity(), array("infoIdiomaId" => $_POST["id"]));
    }

    public function detail($params = array()){
        $entity = $this->entity();
        
        $query = "SELECT i.`infoIdiomaId`, `idioma`, `hablado`, `escrito`,
                         `escucha`,`integranteId` 
                    FROM ".$entity["tableName"]."
                    WHERE `infoIdiomaId` = ". $params["filter"];
        $this->queryType = "row";
        return $this->getDataGrid($query);
    }
    
    public function entity($CRUD = array())
    {
        $data = array(
                        "tableName" => $this->pluginPrefix."infoIdiomas"
                        ,"entityConfig" => $CRUD
                        ,"atributes" => array(
                            "infoIdiomaId" => array("type" => "int", "PK" => 0, "required" => false, "readOnly" => true, "autoIncrement" => true)
                            ,"idioma" => array("type" => "varchar", "required" => true)
                            ,"hablado" => array("type" => "int", "required" => true)
                            ,"escrito" => array("type" => "int", "required" => true)
                            ,"escucha" => array("type" => "int", "required" => true)
                            ,"parentId" => array("type" => "int","required" => false, "readOnly" => true, "hidden" => true, "isTableCol" => false)
                            ,"integranteId" => array("type" => "int", "update" => false,"required" => false, "hidden" => true)
                            ,"soporte" => array("type" => "varchar", "required" => false, "readOnly" => true, "hidden" => false, "isTableCol" => false, "downloadFile" => array("show" => true, "cellIcon" => 6, "rowObjectId" => 6, "view" => "files"))
                            ,"file" => array("type" => "file", "validateAttr" => array("size" => 200, "units" => "MB", "factor" => 1024), "required" => false,"hidden" => true, "edithidden" => true, "isTableCol" => false)
                            ,"fileId" => array("type" => "int", "hidden" => true, "required" => false, "readOnly" => true, "hidden" => true, "isTableCol" => false)
                        )
                    );
            return $data;
    }
}
?>
