<?php
require_once "../../commonFilesGrid.php";
$params["postData"]["method"] = "getIntegrantesFiles";
$params["sortname"] = "date_entered";
$params["CRUD"] = array("add" => false, "edit" => false, "del" => true, "view" => false);
$view = new buildView("files", $params, "files");
?>
