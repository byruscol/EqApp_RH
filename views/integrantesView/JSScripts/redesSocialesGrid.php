<?php
require_once "../../commonRedesSocialesGrid.php";
$params["sortname"] = "redSocialId";
$params["CRUD"] = array("add" => true, "edit" => false , "del" => true, "view" => false, "excel"=>true);
$view = new buildView("redesSociales", $params, "redesSociales");
?>
