<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once $pluginPath . "/helpers/Details.php";
$details = new Details($viewFile);
global $controller;
$integrante = (empty($_GET["rowid"]))? 0 : $_GET["rowid"];
if($integrante != 0)
    $familiares = $controller->model->getIntegrantesFamiliares(array("filter" => $integrante)); 
else
    $familiares = $familiares["totalRows"] = 0;
$viewFileFamiliar = $pluginPath . "/views/familiaresView/familiaresView.php";
$template = $pluginPath . "/views/familiaresView/familiaresDetail.php";
?>
<br/>
<div class="row-fluid">
    <div class="span11">
        <div class="jqGrid">
            <div class="wrap">
                <div id="icon-tools" class="icon32"></div>
                <h2><?php echo $resource->getWord("integrante"); ?></h2>
            </div>
            <div class="span12">
                <table  class="table table-bordered table-condensed">
                    <tr>
                        <td align="center"><?php $details->getPicture(array("table" => 'fotosIntegrantes', "Id" => "IntegranteId"));?></td>
                        <td rowspan="2"><?php $details->renderDetail();?></td>
                    </tr>
                    <tr>
                        <td><?php $details->setPictureForm('fotoIntegrantes');?></td>
                    </tr>
                </table>
                <div class="wrap">
                    <h2><?php echo $resource->getWord("familia"); ?></h2>
                </div>
                <div class="span12">
                    <table id="familia"  class="table table-bordered table-condensed">
                        <?php 
                        $totalRows = count($familiares["data"]);
                        for($i = 0; $i < $totalRows; $i++){
                            $_GET["page"] = "familiares";
                            $_GET["task"] = "Details";
                            $_GET["rowid"] = $familiares["data"][$i]->familiarId; 
                            $details = new Details($viewFileFamiliar);
                        ?>
                        <tr>
                            <td align="center"><?php $details->getPicture(array("table" => 'fotosFamiliares', "Id" => "familiarId"));?></td>
                            <td><?php $details->renderDetail($template);?></td>
                        </tr>
                        <?php }?>
                    </table> 
                </div>
            </div>
        </div>
    </div>
</div>
    
<div id="loading"><p><?php echo $resource->getWord("LoadingFile"); ?></p></div>
<script>
    jQuery(function () {
        
        jQuery("#loading").dialog({
            closeOnEscape: false,
            autoOpen: false,
            modal: true,
            width: 200,
            height: 100,
            close: function(event, ui) {location.reload();}
        });
   });
</script>