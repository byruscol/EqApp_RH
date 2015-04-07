<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');
class integrantesTalentos extends DBManagerModel{
   
    public function getList($params = array()){
        $entity = $this->entity();
        if(!array_key_exists('filter', $params))
                $params["filter"] = 0;
        
        if( !$this->isRhAdmin) 
                $params["filter"] = $this->currentIntegrante;
        
        $start = $params["limit"] * $params["page"] - $params["limit"];
        $query = "SELECT `integranteId`,
                            `talento1`,
                            `talento2`,
                            `talento3`,
                            `talento4`,
                            `talento5`,
                            `visa`,
                            `paisVisa`,
                            `pasionTrabajo`,
                            `menosPasionTrabajo`,
                            `otrasActividadesLaborales`,
                            `diaFavorito`,
                            `diaNoFavorito`,
                            `tipoVivienda`,
                            `poseeVehiculo`,
                            `tipoVehiculo`,
                            `claseVehiculo`,
                            `medioTransporte`,
                            `tiempoTrayecto`,
                            `tipoAlimentacion`
                        FROM `".$this->pluginPrefix."integrantesTalentos`
                        WHERE `integranteId` = ". $params["filter"];

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
        
        $data = $this->getDataGrid($query, $start, $params["limit"] , $params["sidx"], $params["sord"] );
        $data["customResponce"] = true;
        return $data;
    }
    
    public function add(){
        if( !$this->isRhAdmin) 
                $_POST["integranteId"] = $this->currentIntegrante;
        
        $this->addRecord($this->entity(), $_POST, array("date_entered" => date("Y-m-d H:i:s"), "created_by" => $this->currentUser->ID));
    }
    
    public function edit(){
        if( !$this->isRhAdmin) 
                $_POST["integranteId"] = $this->currentIntegrante;
        $this->updateRecord($this->entity(), $_POST, array("integranteId" => $_POST["integranteId"]));
    }
    
    public function del(){}

    public function detail($params = array()){}
    
    public function entity($CRUD = array())
    {
        $data = array(
                        "tableName" => $this->pluginPrefix."integrantesTalentos"
                        ,"entityConfig" => $CRUD
                        ,"formConfig" => array("cols" => 3, "fieldCheckOper" => "integranteId")
                        ,"atributes" => array(
                            "integranteId" => array("type" => "int", "PK" => 0, "required" => false, "hidden" => true,  "readOnly" => true)
                            ,"talento1" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."talentos", "id" => "talentoId", "text" => "talento"))
                            ,"talento2" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."talentos", "id" => "talentoId", "text" => "talento"))
                            ,"talento3" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."talentos", "id" => "talentoId", "text" => "talento"))
                            ,"talento4" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."talentos", "id" => "talentoId", "text" => "talento"))
                            ,"talento5" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."talentos", "id" => "talentoId", "text" => "talento"))
                            ,"visa" => array("type" => "enum", "required" => true)
                            ,"paisVisa" => array("type" => "varchar", "required" => true)
                            ,"pasionTrabajo" => array("type" => "varchar", "required" => true)
                            ,"menosPasionTrabajo" => array("type" => "varchar", "required" => true)
                            ,"otrasActividadesLaborales" => array("type" => "varchar", "required" => true)
                            ,"diaFavorito" => array("type" => "enum", "required" => true)
                            ,"diaNoFavorito" => array("type" => "enum", "required" => true)
                            ,"tipoVivienda" => array("type" => "enum", "required" => true)
                            ,"poseeVehiculo" => array("type" => "enum", "required" => true)
                            ,"tipoVehiculo" => array("type" => "enum", "required" => true)
                            ,"claseVehiculo" => array("type" => "enum", "required" => true)
                            ,"medioTransporte" => array("type" => "enum", "required" => true)
                            ,"tiempoTrayecto" => array("type" => "int", "required" => false)
                            ,"tipoAlimentacion" => array("type" => "enum", "required" => true)
                            )
                    );
            return $data;
    }
}
?>
