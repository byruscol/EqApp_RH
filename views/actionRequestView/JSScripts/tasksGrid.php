<?php
require_once "../../commonNotesGrid.php";
$params["postData"]["method"] = "getActionRequetsTasks";
$params["sortname"] = "date_entered";
$params["CRUD"] = array("add" => true, "edit" => true, "del" => true, "view" => true);
$view = new buildView("tasks", $params, "tasks");
?>