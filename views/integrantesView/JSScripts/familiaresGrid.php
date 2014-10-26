<?php
require_once "../../commonFamiliaresGrid.php";
$params["postData"]["method"] = "getIntegrantesFamiliares";
$params["sortname"] = "nombre";
$params["CRUD"] = array("add" => true, "edit" => true, "del" => true, "view" => true);
$view = new buildView("familiares", $params, "familiares");
?>
