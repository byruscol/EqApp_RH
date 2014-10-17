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
    function __construct($v) {
        parent::__construct();
        global $resource;
        $this->resourceDetails = $resource;
        if((isset($_GET["page"]) && !empty($_GET["page"])) && 
           (isset($_GET["task"]) && !empty($_GET["task"])) && 
           (isset($_GET["rowid"]) && !empty($_GET["rowid"])))
        {
            require_once $this->pluginPath."/models/".$_GET["page"]."Model.php";
            $this->model = new $_GET["page"]();
            $this->view = $v;
        }
    }
    
    function renderDetail(){
        if((isset($_GET["page"]) && !empty($_GET["page"])) && 
           (isset($_GET["task"]) && !empty($_GET["task"])) && 
           (isset($_GET["rowid"]) && !empty($_GET["rowid"])))
        {
            
            $fileTemplate = $this->pluginPath."/views/".$_GET["page"]."View/".$_GET["page"]."Detail.php";
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
