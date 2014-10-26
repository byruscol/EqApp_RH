<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once "../../commonFilesGrid.php";
require_once "../../../helpers/Grid.php";
require_once "../../class.buildView.php";
header('Content-type: text/javascript');
$params = array("numRows" => 10
                , "sortname" => "actionRequestId"
                , "CRUD" => array("add" => true, "edit" => true, "del" => true, "view" => true)
                , "actions" => array(
                                        array("type" => "onSelectRow"
                                                  ,"function" => 'function(id) {
                                                                    if(id != null) {
                                                                            var postDataObj = jQuery("#notes").jqGrid("getGridParam","postData");
                                                                            postDataObj["filter"] = id;
                                                                            postDataObj["parent"] = "'.$_GET["view"].'";
                                                                            jQuery("#notes").jqGrid("setGridParam",{postData: postDataObj})
                                                                                            .trigger("reloadGrid");

                                                                            postDataObj = jQuery("#tasks").jqGrid("getGridParam","postData");
                                                                            postDataObj["filter"] = id;
                                                                            postDataObj["parent"] = "'.$_GET["view"].'";
                                                                            jQuery("#tasks").jqGrid("setGridParam",{postData: postDataObj})
                                                                                            .trigger("reloadGrid");
                                                                                        
                                                                            postDataObj = jQuery("#files").jqGrid("getGridParam","postData");
                                                                            postDataObj["filter"] = id;
                                                                            postDataObj["parent"] = "'.$_GET["view"].'";
                                                                            jQuery("#files").jqGrid("setGridParam",{postData: postDataObj})
                                                                                            .trigger("reloadGrid");
                                                                    }
                                                                }'
                                                )
                                    )
            );
$view = new buildView($_GET["view"], $params, "actionRequests");
?>
