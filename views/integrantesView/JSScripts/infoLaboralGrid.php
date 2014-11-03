<?php
require_once "../../commonInfoLaboralGrid.php";
$params["sortname"] = "fechaIngreso";
$params["CRUD"] = array("add" => true, "edit" => true, "del" => true, "view" => false, "files" => true,"excel"=>true);
$params["fileActions"] = array(
                                array(
                                    "idFile" => "file",
                                    "url" => $pluginURL."edit.php?controller=files",
                                    "parentRelationShip" => "fileInfoLaboral",
                                    "oper" => "add"
                                )
                            );
$view = new buildView("infoLaboral", $params, "infoLaboral");
?>
