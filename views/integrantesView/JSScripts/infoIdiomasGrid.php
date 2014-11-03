<?php
require_once "../../commonInfoIdiomasGrid.php";
$params["sortname"] = "idioma";
$params["CRUD"] = array("add" => true, "edit" => true, "del" => true, "view" => false, "files" => true,"excel"=>true);
$params["fileActions"] = array(
                                array(
                                    "idFile" => "file",
                                    "url" => $pluginURL."edit.php?controller=files",
                                    "parentRelationShip" => "filesInfoIdiomas",
                                    "oper" => "add"
                                )
                            );
$view = new buildView("infoIdiomas", $params, "infoIdiomas");
?>
