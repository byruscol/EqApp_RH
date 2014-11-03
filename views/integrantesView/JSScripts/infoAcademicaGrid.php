<?php
require_once "../../commonInfoAcademicaGrid.php";
$params["sortname"] = "fechaTerminacion";
$params["CRUD"] = array("add" => true, "edit" => true, "del" => true, "view" => false, "files" => true,"excel"=>true);
$params["fileActions"] = array(
                                array(
                                    "idFile" => "file",
                                    "url" => $pluginURL."edit.php?controller=files",
                                    "parentRelationShip" => "fileInfoAcademica",
                                    "oper" => "add"
                                )
                            );
$view = new buildView("infoAcademica", $params, "infoAcademica");
?>
