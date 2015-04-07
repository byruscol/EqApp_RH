<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');
class familiares extends DBManagerModel{
   
    public function getList($params = array()){
        $entity = $this->entity();
        if(!array_key_exists('filter', $params))
                $params["filter"] = 0;

        $start = $params["limit"] * $params["page"] - $params["limit"];
        $query = "SELECT familiarId, nombre, apellido, genero, fechaNacimiento"
                        . ", DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), fechaNacimiento)), '%Y')+0 edad, tipo"
                        . ", estadoCivil, ocupacion,epsId "
                    . "FROM ".$entity["tableName"]."
                       WHERE  deleted = 0 AND `familiarId` IN ( ". $params["filter"] ." )";

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
    
    public function getIntegrantesFamiliares($params = array()){
        $entity = $this->entity();
        $DataArray= array();
        $currentUserRoles = (array)$this->currentUser->roles;
        if( !$this->isRhAdmin)   
            $params["filter"] = $this->currentIntegrante;
        
        $query = "SELECT  `familiarId`
                  FROM  `".$entity["tableName"]."` n
                  WHERE  `integranteId` = " . $params["filter"];

        $responce = $this->getDataGrid($query);
        
        foreach ( $responce["data"] as $k => $v ){
                $DataArray[] = $responce["data"][$k]->familiarId;
        }

        $params["parentRelationShip"] = "integrantes";
        $params["parent"] = $params["filter"];
        $params["filter"] = implode(",", $DataArray);

        $data = $this->getList($params);
        return $data;
    }

    public function add(){
        if( !$this->isRhAdmin) 
                $_POST["integranteId"] = $this->currentIntegrante;
        else
            $_POST["integranteId"] = $_POST["parentId"];
        
        $_POST["fechaNacimiento"] = $this->formatDate($_POST["fechaNacimiento"]);
        $this->addRecord($this->entity(), $_POST, array("date_entered" => date("Y-m-d H:i:s"), "created_by" => $this->currentUser->ID));
        echo json_encode(array("parentId" => $this->LastId));
    }
    
    public function edit(){
        $entityObj = $this->entity();
        $_POST["fechaNacimiento"] = $this->formatDate($_POST["fechaNacimiento"]);
        $this->updateRecord($entityObj, $_POST, array("familiarId" => $_POST["familiarId"]));
        echo json_encode(array("parentId" => $_POST["familiarId"]));
    }
    
    public function del(){
        $this->delRecord($this->entity(), array("familiarId" => $_POST["id"]));
    }

    public function detail($params = array()){
        $entity = $this->entity();
        
        $query = "SELECT familiarId, nombre, apellido, genero, fechaNacimiento"
                        . ", DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), fechaNacimiento)), '%Y')+0 edad, tipo"
                        . ", estadoCivil, ocupacion, eps epsId, '' foto "
                    . " FROM ".$entity["tableName"]." n
                    LEFT JOIN ".$this->pluginPrefix."epss s ON s.epsId = n.epsId
                    WHERE n.`familiarId` = " . $params["filter"];
        $this->queryType = "row";
        return $this->getDataGrid($query);
    }
    
    public function entity($CRUD = array())
    {
  
        $data = array(
                        "tableName" => $this->pluginPrefix."familiares"
                        ,"entityConfig" => $CRUD
                        ,"atributes" => array(
                            "familiarId" => array("type" => "int", "PK" => 0, "required" => false, "readOnly" => true, "autoIncrement" => true)
                            ,"nombre" => array("type" => "varchar", "required" => true)
                            ,"apellido" => array("type" => "varchar", "required" => false)
                            ,"genero" => array("type" => "enum", "required" => true)
                            ,"fechaNacimiento" => array("type" => "date", "hidden" => true, "edithidden" => true, "required" => true)
                            ,"edad" => array("type" => "tinyint", "isTableCol" => false, "readOnly" => true)
                            ,"tipo" => array("type" => "enum", "required" => true)
                            ,"estadoCivil" => array("type" => "enum", "required" => true)
                            ,"ocupacion" => array("type" => "enum", "required" => true)
                            ,"epsId" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."epss", "id" => "epsId", "text" => "eps"))
                            ,"parentId" => array("type" => "int","required" => false, "hidden" => true, "isTableCol" => false)
                            ,"integranteId" => array("type" => "int", "update" => false,"required" => false, "hidden" => true)
                            ,"foto" => array("type" => "file", "validateAttr" => array("size" => 50, "units" => "MB", "factor" => 1024), "required" => false,"hidden" => true, "edithidden" => true, "isTableCol" => false)
                        )
                    );
            return $data;
    }
}
?>
