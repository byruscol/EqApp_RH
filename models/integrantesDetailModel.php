<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');
class integrantesDetail extends DBManagerModel{
   
    public function getList($params = array()){
        $entity = $this->entity();
        if(!array_key_exists('filter', $params))
                $params["filter"] = 0;
 
        $start = $params["limit"] * $params["page"] - $params["limit"];
        $query = "SELECT `integranteId`,
                            `fondoCesantiasId`,
                            `epsid`,
                            `afpId`,
                            `arl`,
                            `cajaCompensacionId`,
                            `gustoCaja`,
                            `planComplementario`,
                            `planComplementarioDesc`,
                            departamentoId departamento,
                            `ciudadSedeId`,
                            `unidadId`,
                            `reintegrado`,
                            `cuantasVecesReintegrado`,
                            `tipoContratacion`,
                            `estrato`,
                            `alerigia`,
                            `alergias`,
                            `fuma`,
                            `toma`,
                            `tallaCamisa`,
                            `tallaPantalon`,
                            `tallaZapatos`,
                            `tipoCelular`,
                            `tipoLineaCelular`
                        FROM `".$this->pluginPrefix."integrantesDetails` i
                            JOIN ".$this->pluginPrefix."ciudades c ON c.ciudadId = i.ciudadSedeId
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
                    WHERE `infoAcademicaId` = ". $params["filter"];
        $this->queryType = "row";
        return $this->getDataGrid($query);
    }
    
    public function getCities($params){
        $query = "SELECT ciudadId, ciudad 
                  FROM ".$this->pluginPrefix."ciudades c
                  WHERE `departamentoId` = ". $params["filter"];
        
        $cities = $this->getDataGrid($query, NULL, NULL , "ciudad", "ASC");
        $responce = array("metaData" => array("key" => "ciudadId", "value" => "ciudad"), "data" => $cities["data"]);
        return $cities;
    }
    
    public function entity($CRUD = array())
    {
        $data = array(
                        "tableName" => $this->pluginPrefix."integrantesDetails"
                        ,"entityConfig" => $CRUD
                        ,"atributes" => array(
                            "integranteId" => array("type" => "int", "PK" => 0, "required" => false, "readOnly" => true, "autoIncrement" => true)
                            ,"fondoCesantiasId" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."fondoCesantias", "id" => "fondoCesantiasId", "text" => "fondoCesantias"))
                            ,"epsid" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."epss", "id" => "epsid", "text" => "eps"))
                            ,"afpId" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."afps", "id" => "afpId", "text" => "afp"))
                            ,"arl" => array("type" => "varchar", "required" => true)
                            ,"cajaCompensacionId" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."cajaCompensacion", "id" => "cajaCompensacionId", "text" => "cajaCompensacion"))
                            ,"gustoCaja" => array("type" => "enum", "required" => true)
                            ,"planComplementario" => array("type" => "enum", "required" => true)
                            ,"planComplementarioDesc" => array("type" => "text", "required" => false)
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
                                                                                                                . "var dropdown = jQuery('#ciudadSedeId');"
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
                            ,"ciudadSedeId" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."ciudades", "id" => "ciudadId", "text" => "ciudad"))
                            ,"unidadId" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."unidades", "id" => "unidadId", "text" => "unidad"))
                            ,"reintegrado" => array("type" => "enum", "required" => true, "hidden" => true, "edithidden" => true)
                            ,"cuantasVecesReintegrado" => array("type" => "int","required" => false, "hidden" => true, "edithidden" => true)
                            ,"tipoContratacion" => array("type" => "enum", "required" => true)
                            ,"estrato" => array("type" => "enum", "required" => true)
                            ,"alergia" => array("type" => "enum", "required" => true, "hidden" => true, "edithidden" => true)
                            ,"alergias" => array("type" => "varchar", "required" => false, "hidden" => true, "edithidden" => true)
                            ,"fuma" => array("type" => "enum", "required" => true, "hidden" => true, "edithidden" => true)
                            ,"toma" => array("type" => "enum", "required" => true, "hidden" => true, "edithidden" => true)
                            ,"tallaCamisa" => array("type" => "enum", "required" => true, "hidden" => true, "edithidden" => true)
                            ,"tallaPantalon" => array("type" => "int", "required" => true, "hidden" => true, "edithidden" => true)
                            ,"tallaZapatos" => array("type" => "varchar", "required" => true, "hidden" => true, "edithidden" => true)
                            ,"tipoCelular" => array("type" => "enum", "required" => true, "hidden" => true, "edithidden" => true)
                            ,"tipoLineaCelular" => array("type" => "enum", "required" => true, "hidden" => true, "edithidden" => true)
                            ,"parentId" => array("type" => "int","required" => false, "hidden" => true, "isTableCol" => false)
                            
                            )
                    );
            return $data;
    }
}
?>