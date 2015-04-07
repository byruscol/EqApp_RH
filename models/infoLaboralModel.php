<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');
class infoLaboral extends DBManagerModel{
   
    public function getList($params = array()){
        $entity = $this->entity();
        if(!array_key_exists('filter', $params))
                $params["filter"] = 0;

        if( !$this->isRhAdmin) 
                $params["filter"] = $this->currentIntegrante;
        
        $start = $params["limit"] * $params["page"] - $params["limit"];
        $query = "SELECT i.`infoLaboralId`, `empresa`, `fechaIngreso`, `fechaRetiro`,
                         PERIOD_DIFF(DATE_FORMAT(if(`fechaRetiro` = '0000-00-00', NOW(),`fechaRetiro`),'%Y%m'),DATE_FORMAT(`fechaIngreso`,'%Y%m')) tiempo,
                         actual, `cargo`, `tipoActividad`, `areaDesarrollo`,`integranteId`, f.ext soporte, f.fileId, '' file
                    FROM ".$entity["tableName"]." i
			LEFT JOIN ".$this->pluginPrefix."filesInfoLaboral fi ON fi.infoLaboralId = i.infoLaboralId 
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
        if( !$this->isRhAdmin) 
                $_POST["integranteId"] = $this->currentIntegrante;
        else
            $_POST["integranteId"] = $_POST["parentId"];
        
        $_POST["fechaIngreso"] = $this->formatDate($_POST["fechaIngreso"]);
        $_POST["fechaRetiro"] = (empty($_POST["fechaRetiro"]))? "NULL": $this->formatDate($_POST["fechaRetiro"]);
        $this->addRecord($this->entity(), $_POST, array("date_entered" => date("Y-m-d H:i:s"), "created_by" => $this->currentUser->ID));
        echo json_encode(array("parentId" => $this->LastId));
    }
    
    public function edit(){
        $entityObj = $this->entity();
        
        $_POST["fechaIngreso"] = $this->formatDate($_POST["fechaIngreso"]);
        $_POST["fechaRetiro"] = (empty($_POST["fechaRetiro"]))? "NULL": $this->formatDate($_POST["fechaRetiro"]);
        $this->updateRecord($entityObj, $_POST, array("infoLaboralId" => $_POST["infoLaboralId"]));
        echo json_encode(array("parentId" => $_POST["infoLaboralId"]));
    }
    
    public function del(){
        $this->delRecord($this->entity(), array("infoLaboralId" => $_POST["id"]));
    }

    public function detail($params = array()){
        $entity = $this->entity();
        if( !$this->isRhAdmin) 
                $params["filter"] = $this->currentIntegrante;
        $query = "SELECT `infoLaboralId`, `empresa`, `fechaIngreso`, `fechaRetiro`,
                         PERIOD_DIFF(DATE_FORMAT(if(`fechaRetiro` IS NULL, NOW(),`fechaRetiro`),'%Y%m'),DATE_FORMAT(`fechaIngreso`,'%Y%m')) tiempo,
                         `cargo`, `tipoActividad`, `areaDesarrollo` 
                    FROM ".$entity["tableName"]."
                    WHERE `integranteId` = ". $params["filter"];
        $this->queryType = "row";
        return $this->getDataGrid($query);
    }
    
    public function entity($CRUD = array())
    {
  
        $data = array(
                        "tableName" => $this->pluginPrefix."infoLaboral"
                        ,"entityConfig" => $CRUD
                        ,"atributes" => array(
                            "infoLaboralId" => array("type" => "int", "PK" => 0, "required" => false, "readOnly" => true, "autoIncrement" => true)
                            ,"empresa" => array("type" => "varchar", "required" => true)
                            ,"fechaIngreso" => array("type" => "date", "required" => true)
                            ,"fechaRetiro" => array("type" => "date", "required" => false)
                            ,"tiempo" => array("type" => "tinyint", "isTableCol" => false, "readOnly" => true)
                            ,"actual" => array("type" => "enum", "required" => true)
                            ,"cargo" => array("type" => "varchar", "hidden" => false, "edithidden" => true, "required" => true)
                            ,"tipoActividad" => array("type" => "enum", "required" => true)
                            ,"areaDesarrollo" => array("type" => "enum", "required" => true)
                            ,"integranteId" => array("type" => "int", "readOnly" => true, "update" => false,"required" => false, "hidden" => true)
                            ,"soporte" => array("type" => "varchar", "required" => false, "readOnly" => true, "hidden" => false, "isTableCol" => false, "downloadFile" => array("show" => true, "cellIcon" => 9, "rowObjectId" => 9, "view" => "files"))
                            ,"fileId" => array("type" => "int", "hidden" => true, "required" => false, "readOnly" => true, "hidden" => true, "isTableCol" => false)
                            ,"file" => array("type" => "file", "validateAttr" => array("size" => 200, "units" => "MB", "factor" => 1024), "required" => false,"hidden" => true, "edithidden" => true, "isTableCol" => false)
                            ,"parentId" => array("type" => "int","required" => false, "hidden" => true, "readOnly" => true, "isTableCol" => false)  
                            
                        )
                    );
            return $data;
    }
}
?>
