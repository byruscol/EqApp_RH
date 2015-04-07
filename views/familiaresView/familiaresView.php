<?php
require_once $pluginPath . "/helpers/Details.php";
$details = new Details($viewFile);
global $controller;

if($details->isRhAdmin)
    $integrante = (empty($_GET["Id"]))? 0 : $_GET["Id"];
else
    $integrante = $details->currentIntegrante;
if($integrante != 0)
    $familiares = $controller->model->getIntegrantesFamiliares(array("filter" => $integrante)); 
else
    $familiares = $familiares["totalRows"] = 0;


?>
<div class="row-fluid">
    <div class="span11">
        <h2><?php echo $resource->getWord("familiares"); ?></h2>
        <div class="wrap">
            <?php if($details->isRhAdmin):?>
            <div id="remote">
                <label for="inputEmail"><?php echo $resource->getWord("integrante"); ?></label>
                <input type="text" name="q" id="query" class="form-control input-normal" value="<?php echo $_GET["name"]?>"/> 
            </div>
            <?php endif;?>
        </div>
        
    </div>
    
            <div class="span12">
                <table id="familia"  class="table table-bordered table-condensed">
                    <?php for($i = 0; $i < $familiares["totalRows"]; $i++){
                        $_GET["task"] = "Details";
                        $_GET["rowid"] = $familiares["data"][$i]->familiarId; 
                        $details = new Details($viewFile);
                    ?>
                    <tr>
                        <td align="center"><?php $details->getPicture(array("table" => 'fotosFamiliares', "Id" => "familiarId"));?></td>
                        <td><?php $details->renderDetail();?></td>
                    </tr>
                    <?php }?>
                </table> 
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
            height: 100/*,
            open: function(event, ui) { jQuery(".ui-dialog-titlebar-close").hide(); jQuery(".ui-dialog-titlebar").hide();}*/
         });
         
        options = { serviceUrl:'admin-ajax.php'
                    , minChars:2
                    , type: "POST"
                    , delimiter: /(,|;)\s*/
                    , maxHeight:400
                    , deferRequestBy: 200
                    , params: { action:'action', id:'integrantes', method : 'getIntegrantesFromName' }
                    , onSelect: function(value, data){ 
                                        location.href="admin.php?page=familiares&Id="+value.data+"&name="+value.value;
                                    }
                };
        
	var a = jQuery('#query').autocomplete(options);
      
   });
</script>