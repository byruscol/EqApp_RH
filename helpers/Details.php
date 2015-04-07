<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Details
 *
 * @author byrus
 */
require_once "DBManager.php"; 
if(!isset($resource)){
    require_once "resources.php";
    $resource = new resources();
}
class Details extends DBManager
{
    private $view;
    private $model;
    private $resourceDetails;
    function __construct($v, $params = array()) {
        parent::__construct();
        global $resource;
        $this->resourceDetails = $resource;
        if(!$this->isRhAdmin){
            if(!isset($_GET["rowid"]))
                $_GET["rowid"] = $this->currentIntegrante;
            $_GET["task"] = "GO";
        }
        if((isset($_GET["page"]) && !empty($_GET["page"])) && 
           (isset($_GET["task"]) && !empty($_GET["task"])) && 
           (isset($_GET["rowid"]) && !empty($_GET["rowid"])))
        {
            
            require_once $this->pluginPath."/models/".$_GET["page"]."Model.php";
            $this->pluginPath."/models/".$_GET["page"]."Model.php";
            $this->model = new $_GET["page"]();
            $this->view = $v;
        }
    }
    
    function getPicture($params){
        
        $query = "SELECT fileId 
                    FROM ".$this->pluginPrefix.$params["table"]."
                    WHERE ".$params["Id"]." = ". $_GET["rowid"]
                . " ORDER BY fileId DESC LIMIT 0,1";
        $fileId = $this->model->get($query, "row");
        
        if($fileId["totalRows"] > 0){
            $file = $this->model->rendererFile($fileId["data"]->fileId, true);
            $b64Src = "data:".$file["mime"].";base64," . base64_encode($file["data"]);
            echo '<img width = "200" src="'.$b64Src.'" alt="" />';
        }
    }
    
    function setPictureForm($parent){
        
        echo '<div class="span3">
                        <form id="uploadFiles" class="form-horizontal" enctype="multipart/form-data" method="post">
                            <fieldset>
                                <legend>'. $this->resourceDetails->getWord("uploadFile") .'</legend>
                                <div class="control-group">
                                    <div class="controls">
                                        <input type="hidden" name="oper" value="add"/>
                                        <input type="hidden" name="parentRelationShip" value="'. $parent .'"/>
                                        <input type="hidden" name="parentId" id="parentId" value="'. $_GET["rowid"].'"/>
                                    </div>
                                </div>
                                <br/>
                                <div class="control-group">
                                    <div class="controls">
                                        <input type="file" id="file" name="file" class="btn btn-default" required="true">
                                    </div>
                                </div>
                                <br/>
                                <div class="control-group">
                                    <div class="controls">
                                        <button id="submit" name="submit" class="btn btn-primary">'. $this->resourceDetails->getWord("accept") .'</button>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>';
    }
    
    function renderDetail($templateParam = null){
        if((isset($_GET["page"]) && !empty($_GET["page"])) && 
           (isset($_GET["task"]) && !empty($_GET["task"])) && 
           (isset($_GET["rowid"]) && !empty($_GET["rowid"])))
        {
            
            $fileTemplate = (empty($templateParam))?$this->pluginPath."/views/".$_GET["page"]."View/".$_GET["page"]."Detail.php": $templateParam;
            if(is_file($fileTemplate)){
                $stream = fopen($fileTemplate,"r");
                $template = stream_get_contents($stream);
                fclose($stream);
                $params = array("filter" => $_GET["rowid"]);
                $data = $this->model->detail($params);
                $entity = $this->model->entity();
                foreach($data["data"] as $key => $value){
                    $value = str_replace("\n", '<br/>',$value);
                    $template = str_replace("{".$key."-Label}", $this->resourceDetails->getWord($key), $template);
                    $template = str_replace("{".$key."}", $value, $template);
                }
                echo $template;
            }
        }
    }
}
