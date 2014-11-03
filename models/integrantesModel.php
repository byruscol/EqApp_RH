<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');
class integrantes extends DBManagerModel{
   
    public function getList($params = array()){
        $entity = $this->entity();
        $start = $params["limit"] * $params["page"] - $params["limit"];
        $query = "SELECT i.`integranteId`, tipoIdentificacion, `identificacion`, activo, `nombre`, `apellido`
                        , `genero`, `rhId`, `fechaNacimiento`, DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), fechaNacimiento)), '%Y')+0 edad
                        , telefono, celular,  email, emailPersonal, `direccion`, departamentoId departamento
                        , `ciudadRecidenciaId`, `localidad`
                        , `barrio`, if(h.Hijos IS NULL ,0, h.Hijos) Hijos, '' foto
                  FROM ".$entity["tableName"]." i
                       JOIN ".$this->pluginPrefix."ciudades c ON c.ciudadId = i.ciudadRecidenciaId
                       LEFT JOIN (
                            SELECT integranteId, COUNT(1) Hijos
                            FROM apps.wp_rh_familiares
                            WHERE tipo = 'Hijo'
                            GROUP BY integranteId
                        )h ON h.integranteId = i.integranteId
                  WHERE `deleted` = 0";
        
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

    public function getCities($params){
        $query = "SELECT ciudadId, ciudad 
                  FROM ".$this->pluginPrefix."ciudades c
                  WHERE `departamentoId` = ". $params["filter"];
        
        $cities = $this->getDataGrid($query, NULL, NULL , "ciudad", "ASC");
        $responce = array("metaData" => array("key" => "ciudadId", "value" => "ciudad"), "data" => $cities["data"]);
        return $cities;
    }
    
    public function getIntegrantesFromName($params){
        $entity = $this->entity();
        $query = "SELECT `integranteId`, CONCAT(`nombre`, ' ', `apellido`) nombre 
                  FROM ".$entity["tableName"]." i
                  WHERE `deleted` = 0 AND (nombre LIKE '%". $params["filter"] ."%' OR apellido LIKE '%". $params["filter"] ."%')";
         
        $responceGrid = $this->getDataGrid($query, NULL, NULL , "2", "ASC");
        
        $responce = array(
                            "data" =>  array("query" => "Unit"
                                            , "suggestions" => array()
                                        )
                            ,"customResponce" => true
                        );
        
        for($i = 0; $i < $responceGrid["totalRows"]; $i++){
            $responce["data"]["suggestions"][] = array("value" => $responceGrid["data"][$i]->nombre
                                                    ,"data" => $responceGrid["data"][$i]->integranteId
                                                  );
        }
        
        return $responce;
    }
    
    public function getIntegrantesFamiliares($params = array()){
        require "familiaresModel.php";
        $familiares = new familiares();
        return $familiares->getIntegrantesFamiliares($params);
    }
    
    public function add(){
        $this->addRecord($this->entity(), $_POST, array("date_entered" => date("Y-m-d H:i:s"), "created_by" => $this->currentUser->ID));
        echo json_encode(array("parentId" => $this->LastId));
    }
    public function edit(){
        $this->updateRecord($this->entity(), $_POST, array("integranteId" => $_POST["integranteId"])/*, array("columnValidateEdit" => "assigned_user_id")*/);
        echo json_encode(array("parentId" => $_POST["integranteId"]));
    }
    public function del(){
        $this->delRecord($this->entity(), array("integranteId" => $_POST["id"])/*, array("columnValidateEdit" => "assigned_user_id")*/);
    }

    public function detail($params = array()){
        $entity = $this->entity();
        $query = "  SELECT `integranteId`, tipoIdentificacion, `identificacion`, activo, `nombre`, `apellido`
                                    , `genero`, `rhId`, `fechaNacimiento`, DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), fechaNacimiento)), '%Y')+0 edad
                                    , telefono, celular
                                    ,  email, emailPersonal, `direccion`, d.departamento
                                    ,  c.ciudad ciudadRecidenciaId, `localidad`
                                    , `barrio` 
                    FROM ".$entity["tableName"]." i
                       JOIN ".$this->pluginPrefix."ciudades c ON c.ciudadId = i.ciudadRecidenciaId
                       JOIN ".$this->pluginPrefix."departamentos d ON d.departamentoId = c.departamentoId
                    WHERE i.`integranteId` = " . $params["filter"];
        $this->queryType = "row";
        return $this->getDataGrid($query);
    }
    
    public function entity($CRUD = array())
    {
            $data = array(
                            "tableName" => $this->pluginPrefix."integrantes"
                            //,"columnValidateEdit" => "assigned_user_id"
                            ,"entityConfig" => $CRUD
                            ,"atributes" => array(
                                "integranteId" => array("type" => "int", "PK" => 0, "required" => false, "readOnly" => true, "autoIncrement" => true, "toolTip" => array("type" => "cell", "cell" => 2) )
                                ,"tipoIdentificacion" => array("type" => "enum", "required" => true)
                                ,"identificacion" => array("type" => "varchar", "required" => true)
                                ,"activo" => array("type" => "enum", "required" => true)
                                ,"nombre" => array("type" => "varchar", "required" => true)
                                ,"apellido" => array("type" => "varchar", "required" => true)
                                ,"genero" => array("type" => "enum", "hidden" => true, "edithidden" => true, "required" => true)
                                ,"rhId" => array("type" => "tinyint", "hidden" => true, "edithidden" => true, "required" => true, "references" => array("table" => $this->pluginPrefix."rh", "id" => "rhId", "text" => "rh"))
                                ,"fechaNacimiento" => array("type" => "date", "hidden" => true, "edithidden" => true, "hidden" => true, "edithidden" => true, "required" => true)
                                ,"edad" => array("type" => "tinyint", "required" => false, "isTableCol" => false, "readOnly" => true)
                                ,"telefono" => array("type" => "varchar", "required" => true)
                                ,"celular" => array("type" => "varchar", "required" => true)
                                ,"email" => array("type" => "email", "required" => true)
                                ,"emailPersonal" => array("type" => "email", "hidden" => true, "edithidden" => true)
                                ,"direccion" => array("type" => "varchar", "hidden" => true, "edithidden" => true, "required" => true)
                                ,"departamento" => array("type" => "tinyint", "isTableCol" => false, "hidden" => true, "edithidden" => true, "required" => true, "references" => array("table" => $this->pluginPrefix."departamentos", "id" => "departamentoId", "text" => "departamento"),
                                                         "dataEvents" => array(
                                                                                array("type" => "change",
                                                                                      "fn" => "@function(e) {"
                                                                                                    . "var thisval = $(e.target).val();"
                                                                                                    . "jQuery.post("
                                                                                                        . "  'admin-ajax.php',"
                                                                                                        . " { action: 'action', id: '" . $this->view . "', method: 'getCities', filter: thisval }"
                                                                                                    . ")"
                                                                                                    . " .done(function( msg ) {"
                                                                                                                . "var data = jQuery.parseJSON(msg);"
                                                                                                                . "var dropdown = jQuery('#ciudadRecidenciaId');"
                                                                                                                . "dropdown.empty();"
                                                                                                                . "var newOptions = {};"
                                                                                                                . "for(xx in data.rows){"
                                                                                                                   . "newOptions[data.rows[xx].id] = data.rows[xx].cell[1];"
                                                                                                                . "}"
                                                                                                                . "jQuery.each(newOptions, function(key, value) {"
                                                                                                                . " dropdown.append(jQuery('<option></option>')"
                                                                                                                . "     .attr('value', key).text(value));"
                                                                                                                . " });"
                                                                                                        . "});"
                                                                                            . "}@"
                                                                                    )
                                                                                )
                                                        )
                                ,"ciudadRecidenciaId" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."ciudades", "id" => "ciudadId", "text" => "ciudad", "cascadeDep" => array("id" => "departamentoId", "value" => "departamentoId")))
                                ,"localidad" => array("type" => "varchar", "hidden" => true, "edithidden" => true, "required" => true)
                                ,"barrio" => array("type" => "varchar", "hidden" => true, "edithidden" => true, "required" => true)
                                ,"Hijos" => array("type" => "int", "required" => false,"hidden" => false, "edithidden" => true, "isTableCol" => false)
                                ,"foto" => array("type" => "file", "validateAttr" => array("size" => 50, "units" => "MB", "factor" => 1024), "required" => false,"hidden" => true, "edithidden" => true, "isTableCol" => false)
                            )
                    );
            return $data;
    }
}
?>