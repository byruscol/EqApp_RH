<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');
class integrantesDetail extends DBManagerModel{
   
    public function getList($params = array()){
        $entity = $this->entity();
        if(!array_key_exists('filter', $params))
                $params["filter"] = 0;
 
        if(!$this->isRhAdmin) 
            $params["filter"] = $this->currentIntegrante;
        
        $start = $params["limit"] * $params["page"] - $params["limit"];
        $query = "SELECT `integranteId`,
                            `fondoCesantiasId`,
                            `epsId`,
                            `afpId`,
                            `arlId`,
                            `cajaCompensacionId`,
                            `gustoCaja`,
                            `planComplementario`,
                            `planComplementarioDesc`,
                            riesgoLaboralId,
                            factorRiesgo,
                            departamentoId departamentoSede,
                            `ciudadSedeId`,
                            empresa,
                            `unidadId`,
                            `reintegrado`,
                            `cuantasVecesReintegrado`,
                            `tipoContratacion`,
                            `estrato`,
                            `alergia`,
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
                        WHERE i.`integranteId` = ". $params["filter"];

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
        
        $data = $this->getDataGrid($query, $start, $params["limit"] , $params["sidx"], $params["sord"] );
        $data["customResponce"] = true;
        return $data;
    }
    
    public function add(){
        if(!$this->isRhAdmin) 
                $_POST["integranteId"] = $this->currentIntegrante;
        $this->addRecord($this->entity(), $_POST, array("date_entered" => date("Y-m-d H:i:s"), "created_by" => $this->currentUser->ID));
    }
    
    public function edit(){
        if(!$this->isRhAdmin)
                $_POST["integranteId"] = $this->currentIntegrante;
        $this->updateRecord($this->entity(), $_POST, array("integranteId" => $_POST["integranteId"]));
    }
    
    public function del(){
        if($this->isRhAdmin){
            $this->delRecord($this->entity(), array("integranteId" => $_POST["id"]));
        }
    }

    public function detail($params = array()){}
    
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
                        ,"formConfig" => array("cols" => 3, "fieldCheckOper" => "integranteId")
                        ,"atributes" => array(
                            "integranteId" => array("type" => "int", "PK" => 0, "required" => false, "hidden" => true,  "readOnly" => true)
                            ,"fondoCesantiasId" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."fondoCesantias", "id" => "fondoCesantiasId", "text" => "fondoCesantias"))
                            ,"epsId" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."epss", "id" => "epsid", "text" => "eps"))
                            ,"afpId" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."afps", "id" => "afpId", "text" => "afp"))
                            ,"arlId" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."arls", "id" => "arlId", "text" => "arl"))
                            ,"cajaCompensacionId" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."cajaCompensacion", "id" => "cajaCompensacionId", "text" => "cajaCompensacion"))
                            ,"gustoCaja" => array("type" => "enum", "required" => true)
                            ,"planComplementario" => array("type" => "enum", "required" => true)
                            ,"planComplementarioDesc" => array("type" => "varchar", "required" => false)
                            ,"riesgoLaboralId" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."riesgoLaboral", "id" => "riesgoLaboralId", "text" => "riesgoLaboral"))
                            ,"factorRiesgo" => array("type" => "int", "required" => false)
                            ,"departamentoSede" => array("type" => "tinyint", "isTableCol" => false, "required" => true, "references" => array("table" => $this->pluginPrefix."departamentos", "id" => "departamentoId", "text" => "departamento"),
                                                         "dataEvents" => array(
                                                                                array("type" => "change",
                                                                                      "fn" => 'function(e) {'
                                                                                                    . 'var thisval = jQuery(e.target).val();'
                                                                                                    . 'jQuery.post('
                                                                                                        . '  "admin-ajax.php",'
                                                                                                        . ' { action: "action", id: "integrantesDetail", method: "getCities", filter: thisval }'
                                                                                                    . ')'
                                                                                                    . ' .done(function( msg ) {'
                                                                                                                . 'var data = jQuery.parseJSON(msg);'
                                                                                                                . 'var dropdown = jQuery("#ciudadSedeId");'
                                                                                                                . 'dropdown.empty();'
                                                                                                                . 'var newOptions = {};'
                                                                                                                . 'for(xx in data.rows){'
                                                                                                                   . 'newOptions[data.rows[xx].id] = data.rows[xx].cell[1];'
                                                                                                                . '}'
                                                                                                                . 'jQuery.each(newOptions, function(key, value) {'
                                                                                                                . ' dropdown.append(jQuery("<option></option>")'
                                                                                                                . '     .attr("value", key).text(value));'
                                                                                                                . ' });'
                                                                                                        . '});'
                                                                                            . '}'
                                                                                    )
                                                                                )
                                                        )
                            ,"ciudadSedeId" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."ciudades", "id" => "ciudadId", "text" => "ciudad"))
                            ,"empresa" => array("type" => "enum", "required" => true)
                            ,"unidadId" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."unidades", "id" => "unidadId", "text" => "unidad"))
                            ,"reintegrado" => array("type" => "enum", "required" => true)
                            ,"cuantasVecesReintegrado" => array("type" => "int","required" => false)
                            ,"tipoContratacion" => array("type" => "enum", "required" => true)
                            ,"estrato" => array("type" => "enum", "required" => true)
                            ,"alergia" => array("type" => "enum", "required" => true)
                            ,"alergias" => array("type" => "varchar", "required" => false)
                            ,"fuma" => array("type" => "enum", "required" => true)
                            ,"toma" => array("type" => "enum", "required" => true)
                            ,"tallaCamisa" => array("type" => "enum", "required" => true)
                            ,"tallaPantalon" => array("type" => "int", "required" => true)
                            ,"tallaZapatos" => array("type" => "varchar", "required" => true)
                            ,"tipoCelular" => array("type" => "enum", "required" => true)
                            ,"tipoLineaCelular" => array("type" => "enum", "required" => true)
                            )
                    );
            return $data;
    }
}
?>
