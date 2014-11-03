<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once dirname(__FILE__)."/../pluginConfig.php";
require_once $pluginPath."/helpers/Grid.php";
require_once "class.buildView.php";
header('Content-type: text/javascript');
$postData = array();
if((isset($_GET["view"]) && !empty($_GET["view"])) && 
    (isset($_GET["rowid"]) && !empty($_GET["rowid"])))
{
    $postData["parent"] = $_GET["view"];
    $postData["filter"] = $_GET["rowid"];
}
$params = array("numRows" => 10, "postData" => $postData);
?>
