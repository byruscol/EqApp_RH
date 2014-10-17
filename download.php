<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once "pluginConfig.php";
require_once 'controllers/mainController.php';

$controlerId = (!empty($_GET["controller"]))?$_GET["controller"]:"basic";
$controller = new mainController($controlerId, false);

$controller->downLoadFile($_GET["id"]);
?>