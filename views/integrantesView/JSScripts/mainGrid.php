<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once "../../../helpers/Grid.php";
require_once "../../class.buildView.php";
header('Content-type: text/javascript');
global $pluginURL;
$params = array("numRows" => 10
                , "sortname" => "apellido"
                , "CRUD" => array("add" => true, "edit" => true, "del" => true, "view" => true, "files" => true, "excel"=>true)
                , "fileActions" => array(
                                        array(
                                            "idFile" => "foto",
                                            "url" => $pluginURL."edit.php?controller=files",
                                            "parentRelationShip" => "fotoIntegrantes",
                                            "oper" => "add"
                                        )
                                    )
                , "actions" => array(
                                        array("type" => "onSelectRow"
                                                  ,"function" => 'function(id) {
                                                                    if(id != null) {
                                                                            var params = {action:"action"
                                                                                          , filter: id
                                                                                          , id: "integrantesDetail"
                                                                                          };
                                                                            reSetformData("integrantesDetailForm")
                                                                            jQuery("#integrantesDetailForm").find("#integranteId").val(id);
                                                                            enableElements(jQuery("#integrantesDetail").children());
                                                                            getFormData("integrantesDetail", params);
                                                                            
                                                                            var params = {action:"action"
                                                                                          , filter: id
                                                                                          , id: "integrantesTalentos"
                                                                                          };
                                                                                          
                                                                            reSetformData("integrantesTalentosForm")
                                                                            jQuery("#integrantesTalentosForm").find("#integranteId").val(id);
                                                                            enableElements(jQuery("#integrantesTalentos").children());
                                                                            getFormData("integrantesTalentos", params);
                                                    
                                                                            var postDataObj = jQuery("#familiares").jqGrid("getGridParam","postData");
                                                                            postDataObj["filter"] = id;
                                                                            postDataObj["parent"] = "'.$_GET["view"].'";
                                                                            jQuery("#familiares").jqGrid("setGridParam",{postData: postDataObj})
                                                                                            .trigger("reloadGrid");
                                                                                            
                                                                            postDataObj = jQuery("#hobies").jqGrid("getGridParam","postData");
                                                                            postDataObj["filter"] = id;
                                                                            postDataObj["parent"] = "'.$_GET["view"].'";
                                                                            jQuery("#hobies").jqGrid("setGridParam",{postData: postDataObj})
                                                                                            .trigger("reloadGrid");


                                                                            postDataObj = jQuery("#infoLaboral").jqGrid("getGridParam","postData");
                                                                            postDataObj["filter"] = id;
                                                                            postDataObj["parent"] = "'.$_GET["view"].'";
                                                                            jQuery("#infoLaboral").jqGrid("setGridParam",{postData: postDataObj})
                                                                                            .trigger("reloadGrid");
                                                                                        
                                                                            postDataObj = jQuery("#infoAcademica").jqGrid("getGridParam","postData");
                                                                            postDataObj["filter"] = id;
                                                                            postDataObj["parent"] = "'.$_GET["view"].'";
                                                                            jQuery("#infoAcademica").jqGrid("setGridParam",{postData: postDataObj})
                                                                                            .trigger("reloadGrid");
                                                                            
                                                                            postDataObj = jQuery("#infoIdiomas").jqGrid("getGridParam","postData");
                                                                            postDataObj["filter"] = id;
                                                                            postDataObj["parent"] = "'.$_GET["view"].'";
                                                                            jQuery("#infoIdiomas").jqGrid("setGridParam",{postData: postDataObj})
                                                                                            .trigger("reloadGrid");
                                                                                            
                                                                            postDataObj = jQuery("#redesSociales").jqGrid("getGridParam","postData");
                                                                            postDataObj["filter"] = id;
                                                                            postDataObj["parent"] = "'.$_GET["view"].'";
                                                                            jQuery("#redesSociales").jqGrid("setGridParam",{postData: postDataObj})
                                                                                            .trigger("reloadGrid");
                                                                    }
                                                                }'
                                                )
                                    )
            );
$view = new buildView($_GET["view"], $params, "integrantes");
?>
