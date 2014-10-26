<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once $pluginPath . "/helpers/Details.php";
$details = new Details($viewFile);
?>
<br/>
<div class="row-fluid">
    <div class="span11">
        <div class="jqGrid">
            <div class="wrap">
                <div id="icon-tools" class="icon32"></div>
                <h2><?php echo $resource->getWord("familiar"); ?></h2>
            </div>
            <div class="span12">
                <table  class="table table-bordered table-condensed">
                    <tr>
                        <td align="center"><?php $details->getPicture(array("table" => 'fotosFamiliares', "Id" => "familiarId"));?></td>
                        <td rowspan="2"><?php $details->renderDetail();?></td>
                    </tr>
                    <tr>
                        <td><?php $details->setPictureForm('fotoFamiliar');?></td>
                    </tr>
                </table>
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