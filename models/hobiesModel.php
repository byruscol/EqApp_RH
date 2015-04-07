<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');
class hobies extends DBManagerModel{
   
    public function getList($params = array()){
        $entity = $this->entity();
        $start = $params["limit"] * $params["page"] - $params["limit"];
        
        if( !$this->isRhAdmin) 
                $params["filter"] = $this->currentIntegrante;
        
        $query = "SELECT i.integrantesHobiesId, i.`integranteId`/*, h.tipoHobieId*/
                        , actividad /*i.`hobieId`*/
                  FROM ".$entity["tableName"]." i
			/*JOIN ".$this->pluginPrefix."hobies h ON h.hobieId = i.hobieId
                        JOIN ".$this->pluginPrefix."tipoHobies c ON c.tipoHobieId = h.tipoHobieId*/
                  WHERE deleted = 0 AND `integranteId` = ". $params["filter"];
        
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
        //echo $query;
        return $this->getDataGrid($query, $start, $params["limit"] , $params["sidx"], $params["sord"]);
    }

    public function getHobies($params){
        $query = "SELECT hobieId, hobie 
                  FROM ".$this->pluginPrefix."hobies c
                  WHERE `tipoHobieId` = ". $params["filter"];
        
        $hobie = $this->getDataGrid($query, NULL, NULL , "hobie", "ASC");
        $responce = array("metaData" => array("key" => "hobieId", "value" => "hobie"), "data" => $hobie["data"]);
        return $hobie;
    }
    
    public function add(){
        if( !$this->isRhAdmin) 
                $_POST["integranteId"] = $this->currentIntegrante;
        else
            $_POST["integranteId"] = $_POST["parentId"];
        
        $this->addRecord($this->entity(), $_POST, array("date_entered" => date("Y-m-d H:i:s"), "created_by" => $this->currentUser->ID));
    }
    public function edit(){
        $this->updateRecord($this->entity(), $_POST, array("integrantesHobiesId" => $_POST["integranteId"])/*, array("columnValidateEdit" => "assigned_user_id")*/);
    }
    public function del(){
        $this->delRecord($this->entity(), array("integrantesHobiesId" => $_POST["id"])/*, array("columnValidateEdit" => "assigned_user_id")*/);
    }

    public function detail($params = array()){}
    
    public function entity($CRUD = array())
    {
            $data = array(
                            "tableName" => $this->pluginPrefix."integrantesHobies"
                            ,"entityConfig" => $CRUD
                            ,"atributes" => array(
                                "integrantesHobiesId" => array("type" => "int", "PK" => 0, "required" => false, "readOnly" => true, "autoIncrement" => true)
                                ,"integranteId" => array("type" => "int", "required" => false, "readOnly" => true, "hidden" => true)
                                /*,"tipoHobieId" => array("type" => "tinyint", "isTableCol" => false, "required" => true, "references" => array("table" => $this->pluginPrefix."tipoHobies", "id" => "tipoHobieId", "text" => "hobie"),
                                                         "dataEvents" => array(
                                                                                array("type" => "change",
                                                                                      "fn" => "@function(e) {"
                                                                                                    . "var thisval = $(e.target).val();"
                                                                                                    . "jQuery.post("
                                                                                                        . "  'admin-ajax.php',"
                                                                                                        . " { action: 'action', id: 'hobies', method: 'getHobies', filter: thisval }"
                                                                                                    . ")"
                                                                                                    . " .done(function( msg ) {"
                                                                                                                . "var data = jQuery.parseJSON(msg);"
                                                                                                                . "var dropdown = jQuery('#hobieId');"
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
                                                        )*/
                                ,"actividad" => array("type" => "varchar", "hidden" => false, "required" => true)
                                //,"hobieId" => array("type" => "tinyint", "required" => true, "references" => array("table" => $this->pluginPrefix."hobies", "id" => "hobieId", "text" => "hobie"))
                                ,"parentId" => array("type" => "int","required" => false, "hidden" => true, "readOnly" => true,"isTableCol" => false)
                                )
                    );
            return $data;
    }
}
?>