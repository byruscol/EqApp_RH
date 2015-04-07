<?php
/*ini_set('display_errors',1);
error_reporting(E_ALL);
*/
require_once('DBManagerModel.php');
class files extends DBManagerModel{
	
    public function getList($params = array()){

        if(!array_key_exists('filter', $params))
                $params["filter"] = 0;

        $start = $params["limit"] * $params["page"] - $params["limit"];
        $query = "SELECT `fileId`, `name`, `fileName`, `created` as date_entered
                          , `display_name` AS created_user,`ext`, `size`, `created_by`
                          FROM  `".$this->pluginPrefix."files` n
                                JOIN ".$this->wpPrefix."users u ON u.ID = n.created_by
                          WHERE  `deleted` = 0 AND `fileId` IN ( ". $params["filter"] ." )";
        
        if(array_key_exists('where', $params)){
            if (is_array( $params["where"]->rules )){
                $countRules = count($params["where"]->rules);
                for($i = 0; $i < $countRules; $i++){
                    if($params["where"]->rules[$i]->field == "created_user")
                        $params["where"]->rules[$i]->field = "display_name";
                }
            }
            
           $query .= " AND (". $this->buildWhere($params["where"]) .")";
        }
        
        $data = $this->getDataGrid($query, $start, $params["limit"] , $params["sidx"], $params["sord"] );
        
        foreach($data["data"] as $key => $value){
            $data["data"][$key]->icon = "file.jpg";
            if(is_file($this->pluginPath."/images/".$value->ext.".jpg")){
                $data["data"][$key]->icon = $value->ext.".jpg";
            }
        }
        
        return $data;
    }

    public function getNonConformitiesFiles($params = array()){
        $DataArray= array();
        $query = "SELECT  `fileId`
                              FROM  `".$this->pluginPrefix."nonConformities_files` n
                              WHERE  `nonConformityId` = " . $params["filter"];

        $responce = $this->getDataGrid($query);

        foreach ( $responce["data"] as $k => $v ){
                $DataArray[] = $responce["data"][$k]->fileId;
        }

        $params["parentRelationShip"] = "nonConformity";
        $params["parent"] = $params["filter"];
        $params["filter"] = implode(",", $DataArray);

        $data = $this->getList($params);
        return $data;
    }

    public function add(){
        $rtnData = new stdClass();
        $rtnData->error = '';
        try{
            $entityObj = $this->entity();
            $fileFieldName = (!isset($_POST["idFile"]))? "file" : $_POST["idFile"];
            $relEntity = $entityObj["relationship"][$_POST["parentRelationShip"]];
            $target_path = $this->pluginPath."/uploadedFiles/";
            $_POST["fileName"] = $_FILES[$fileFieldName]['name'];
            $nameParts = explode(".", $_FILES[$fileFieldName]['name']);
            $_POST["ext"] = end($nameParts);
            $nameArray = array_pop($nameParts);
            $fileName = implode("_",$nameParts);
            $fileName = str_replace(array("'",".",",","*","@","?","!"), "_",$fileName);
            $_POST["size"] =  $_FILES[$fileFieldName]["size"];

            $file = $target_path.$fileName.".".$_POST["ext"];
            
            if(move_uploaded_file($_FILES[$fileFieldName]['tmp_name'], $file)) {
                $this->addRecord($entityObj, $_POST, array("mime"=> $_FILES[$fileFieldName]["type"], "created" => date("Y-m-d H:i:s"), "created_by" => $this->currentUser->ID));
                $id = $this->LastId;
                if(!empty($id)){
                    $this->addRecord($relEntity, array($relEntity["parent"]["Id"] => $_POST["parentId"],"fileId" => $id), array());
                    $this->uploadFile($id,$_POST["ext"],$file);
                    $rtnData->msg = 'success';
                }
                else{
                    unlink($file);
                    $rtnData->msg = 'fail';
                }
            } else{
                $rtnData->msg = 'fail'; 
                $rtnData->error = "There was an error uploading the file, please try again!";
            }
        }
        catch (Exception $e){
            $rtnData->msg = 'fail'; 
            $rtnData->error = $e->getMessage();
        }
        echo json_encode($rtnData);
    }
    
    public function edit(){}
    
    public function eliminate($id) {
        $this->eliminateRecord($this->entity(), array("fileId" => $id)/*, array("columnValidateEdit" => "created_by")*/);
    }
    
    public function del(){
        $this->delRecord($this->entity(), array("fileId" => $_POST["id"])/*, array("columnValidateEdit" => "created_by")*/);
    }
    
    public function detail(){}
    public function entity($CRUD = array())
    {
        $data = array(
                    "tableName" => $this->pluginPrefix."files"
                    ,"columnValidateEdit" => "created_by"
                    ,"entityConfig" => $CRUD
                    ,"atributes" => array(
                        "fileId" => array("type" => "int", "PK" => 0, "required" => false, "readOnly" => true, "autoIncrement" => true, "downloadFile" => array("show" => true, "cellIcon" => 5) )
                        ,"name" => array("type" => "varchar", "required" => true)
                        ,"fileName" => array("type" => "varchar", "required" => true)
                        ,"date_entered" => array("type" => "datetime", "required" => false, "readOnly" => true, "isTableCol" => false )
                        ,"created_user" => array("type" => "varchar", "required" => false, "readOnly" => true, "update" => false, "isTableCol" => false)
                        ,"ext" => array("type" => "varchar", "required" => false, "hidden" => true)
                        ,"mime" => array("type" => "varchar", "required" => false, "hidden" => true)
                        ,"size" => array("type" => "bigint", "required" => false, "hidden" => true)
                        ,"created_by" => array("type" => "int", "required" => false, "hidden" => true )
                        ,"icon" => array("type" => "varchar", "required" => false, "hidden" => true, "isTableCol" => false)
                    )
                    ,"relationship" => array(
                        "fotoIntegrantes" => array(
                                    "tableName" => $this->pluginPrefix."fotosIntegrantes"
                                    ,"parent" => array("tableName" => $this->pluginPrefix."integrantes", "Id" => "integranteId")
                                    ,"atributes" => array(
                                            "integranteId" => array("type" => "int", "PK" => 0)
                                            ,"fileId" => array("type" => "int", "PK" => 0)
                                       )
                                )
                        ,"fotoFamiliar" => array(
                                    "tableName" => $this->pluginPrefix."fotosFamiliares"
                                    ,"parent" => array("tableName" => $this->pluginPrefix."familiares", "Id" => "familiarId")
                                    ,"atributes" => array(
                                        "familiarId" => array("type" => "int", "PK" => 0)
                                        ,"fileId" => array("type" => "int", "PK" => 0)
                                    )
                                )
                        ,"fileInfoLaboral" => array(
                                    "tableName" => $this->pluginPrefix."filesInfoLaboral"
                                    ,"parent" => array("tableName" => $this->pluginPrefix."infoLaboral", "Id" => "infoLaboralId")
                                    ,"settings" => array("deleteAllAfterInsert" => true)
                                    ,"atributes" => array(
                                        "infoLaboralId" => array("type" => "int", "PK" => 0)
                                        ,"fileId" => array("type" => "int", "PK" => 0)
                                    )
                                )
                        ,"fileInfoAcademica" => array(
                                    "tableName" => $this->pluginPrefix."filesInfoAcademica"
                                    ,"parent" => array("tableName" => $this->pluginPrefix."infoAcademica", "Id" => "infoAcademicaId")
                                    ,"settings" => array("deleteAllAfterInsert" => true)
                                    ,"atributes" => array(
                                        "infoAcademicaId" => array("type" => "int", "PK" => 0)
                                        ,"fileId" => array("type" => "int", "PK" => 0)
                                    )
                                )
                        ,"filesInfoIdiomas" => array(
                                    "tableName" => $this->pluginPrefix."filesInfoIdiomas"
                                    ,"parent" => array("tableName" => $this->pluginPrefix."infoIdiomas", "Id" => "infoIdiomaId")
                                    ,"settings" => array("deleteAllAfterInsert" => true)
                                    ,"atributes" => array(
                                        "infoIdiomaId" => array("type" => "int", "PK" => 0)
                                        ,"fileId" => array("type" => "int", "PK" => 0)
                                    )
                                )
                    )
                );
            return $data;
    }
}
?>
