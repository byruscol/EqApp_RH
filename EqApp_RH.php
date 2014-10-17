<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
/*
Plugin Name: EqApp_RH
Plugin URI: http://localhost
Description: Plugin para administración de integrantes de Equitel RH
Version: 1.0
Author: Byron Otalvaro
Author URI: http://localhost
License: GPL2
*/
include_once(ABSPATH.'wp-admin/includes/plugin.php');
if(!function_exists('wp_get_current_user'))
    require_once(ABSPATH . "wp-includes/pluggable.php"); 
wp_cookie_constants();
$current_user = wp_get_current_user();

require_once "pluginConfig.php";
require_once "views/mainView.php";
require_once 'controllers/mainController.php';

if(!empty($_POST["id"])){
    $controlerId = $_POST["id"];
}elseif(!empty($_GET["controller"])){
    $controlerId = $_GET["controller"];
}elseif(!empty($_REQUEST["page"])){
    $controlerId = $_REQUEST["page"];
}

/*if(isset($_REQUEST["task"]) && !empty($_REQUEST["task"]))
    $_POST["method"] = $_REQUEST["task"];*/

if(is_plugin_active($pluginName."/".$pluginName.".php"))
    if(!isset($controller))
        $controller = new mainController($controlerId);

function js_includer_opciones() {
       include("config.php");
}
?>
