<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');
class integrantes extends DBManagerModel{
   
    public function getList($params = array()){
        $entity = $this->entity();
        $start = $params["limit"] * $params["page"] - $params["limit"];
        $query = "SELECT `integranteId`, `identificacion`, `nombre`, `apellido`
                        , `genero`, `rhId` RH, `fechaNacimiento`, telefono, celular
                        ,  email, emailPersonal, `direccion`, departamentoId departamento
                        , `ciudadRecidenciaId` ciudadRecidencia, `localidad`
                        , `barrio` 
                  FROM ".$entity["tableName"]." i
                       JOIN ".$this->pluginPrefix."ciudades c ON c.ciudadId = i.ciudadRecidenciaId
                  WHERE `deleted` = 0";
        
        if(array_key_exists('where', $params))
            $query .= " AND (". $this->buildWhere($params["where"]) .")";
        
        return $this->getDataGrid($query, $start, $params["limit"] , $params["sidx"], $params["sord"]);
    }

    public function getCities($params){
        $query = "SELECT ciudadId, ciudad 
                  FROM ".$this->pluginPrefix."ciudades c
                  WHERE `departamentoId` = ". $params["filter"];
        
        $cities = $this->getDataGrid($query, NULL, NULL , "ciudad", "ASC");
        
        //$responce = array(/*"dataKeys" => array("key" => "ciudadId", "value" => "ciudad"),*/ "data" => $cities["data"]);
        echo $responce = "{'metaData':'key'}";
        //echo json_encode($responce);
    }
    
    public function add(){
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
                        , c.`classification` `clasificacion_nc_c` , m.`management` `gestion`, customerType `tipo_cliente_c`
                    FROM ".$entity["tableName"]." n
                    LEFT JOIN ".$this->pluginPrefix."status s ON s.statusid = n.estadonc
                    LEFT JOIN ".$this->wpPrefix."users u ON u.ID = n.assigned_user_id
                    LEFT JOIN ".$this->pluginPrefix."sources sc ON sc.sourceId = n.fuentenc
                    LEFT JOIN ".$this->pluginPrefix."generalities g ON g.generalityId = n.generalidadnc
                    LEFT JOIN ".$this->pluginPrefix."offices o ON o.officeId = n.sedenc
                    LEFT JOIN ".$this->pluginPrefix."classifications c ON c.classificationId = clasificacion_nc_c
                    LEFT JOIN ".$this->pluginPrefix."managements m ON m.managementId = n.gestion
                    LEFT JOIN ".$this->pluginPrefix."customerTypes ct ON ct.customerTypeId = n.tipo_cliente_c
                    WHERE n.`integranteId` = " . $params["filter"];
        $this->queryType = "row";
        return $this->getDataGrid($query);
    }
    
    public function entity($CRUD = array())
    {
            $data = array(
                            "tableName" => $this->pluginPrefix."integrantes"
                            ,"columnValidateEdit" => "assigned_user_id"
                            ,"entityConfig" => $CRUD
                            ,"atributes" => array(
                                "identificacion" => array("type" => "varchar", "required" => true)
                                ,"nombre" => array("type" => "varchar", "required" => true)
                                ,"apellido" => array("type" => "varchar", "required" => true)
                                ,"genero" => array("type" => "enum", "hidden" => true, "edithidden" => true, "required" => true)
                                ,"RH" => array("type" => "tinyint", "hidden" => true, "edithidden" => true, "required" => true, "references" => array("table" => $this->pluginPrefix."rh", "id" => "rhId", "text" => "rh"))
                                ,"fechaNacimiento" => array("type" => "date", "hidden" => true, "edithidden" => true, "hidden" => true, "edithidden" => true, "required" => true)
                                ,"telefono" => array("type" => "varchar", "required" => true)
                                ,"celular" => array("type" => "varchar", "required" => true)
                                ,"email" => array("type" => "email", "required" => true)
                                ,"emailPersonal" => array("type" => "email", "hidden" => true, "edithidden" => true)
                                ,"direccion" => array("type" => "varchar", "hidden" => true, "edithidden" => true, "required" => true)
                                ,"departamento" => array("type" => "tinyint", "hidden" => true, "edithidden" => true, "required" => true, "references" => array("table" => $this->pluginPrefix."departamentos", "id" => "departamentoId", "text" => "departamento"))
                                ,"ciudadRecidencia" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."ciudades", "id" => "ciudadId", "text" => "ciudad", "cascadeDep" => array("id" => "departamentoId", "value" => "departamentoId")))
                                ,"localidad" => array("type" => "varchar", "hidden" => true, "edithidden" => true, "required" => true)
                                ,"barrio" => array("type" => "varchar", "hidden" => true, "edithidden" => true, "required" => true)
                                ,"integranteId" => array("type" => "int", "PK" => 0, "required" => false, "hidden" => true,  "autoIncrement" => true, "toolTip" => array("type" => "cell", "cell" => 2) )
                            )
                    );
            return $data;
    }
}
?>