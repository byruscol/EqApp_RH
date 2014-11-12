<?php
require_once "../../commonInfoIntegrantesDetailGrid.php";
$params["sortname"] = "idioma";
$params["CRUD"] = array("add" => true, "edit" => true, "del" => true, "view" => false,"excel"=>true);
$view = new buildView("integrantesDetail", $params, "integrantesDetail");
?>
