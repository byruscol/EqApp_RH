<?php
require_once "../../commonFamiliaresGrid.php";
$params["postData"]["method"] = "getIntegrantesFamiliares";
$params["sortname"] = "nombre";
$params["CRUD"] = array("add" => true, "edit" => true, "del" => true, "view" => true, "files" => true,"excel"=>true);
$params["fileActions"] = array(
                                array(
                                    "idFile" => "foto",
                                    "url" => $pluginURL."edit.php?controller=files",
                                    "parentRelationShip" => "fotoFamiliar",
                                    "oper" => "add"
                                )
                            );
$view = new buildView("familiares", $params, "familiares");
?>
