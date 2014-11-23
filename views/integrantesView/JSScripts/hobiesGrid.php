<?php
require_once "../../commonHobiesGrid.php";
$params["sortname"] = "tipoHobieId";
$params["CRUD"] = array("add" => true, "edit" => false , "del" => true, "view" => false, "excel"=>true);
$view = new buildView("hobies", $params, "hobies");
?>
