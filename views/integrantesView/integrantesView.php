<?php

require_once $pluginPath . "/helpers/Details.php";
$details = new Details($viewFile);
$user = wp_get_current_user();
$currentUserRoles = (array) $user->roles;
?>
<div class="row-fluid">
    <div class="span11">
        <?php
        if( in_array( "administrator", $currentUserRoles ) || in_array( "editor", $currentUserRoles )) :
        ?>
        <div class="jqGrid">
            <div class="wrap">
                <div id="icon-tools" class="icon32"></div>
                <h2><?php echo $resource->getWord("integrantes"); ?></h2>
            </div>
            <div class="span12">
            <table id="integrantes"></table>
            <div id="integrantesPager"></div>
            </div>
        </div>
        <?php
        else:
        ?>
        <div class="">
            <div class="wrap">
                <div id="icon-tools" class="icon32"></div>
                <h2><?php echo $resource->getWord("integrante"); ?></h2>
            </div>
            <div class="span12">
                <table  class="table table-bordered table-condensed">
                    <tr>
                        <td width="200" align="center"><?php $details->getPicture(array("table" => 'fotosIntegrantes', "Id" => "IntegranteId"));?></td>
                        <td rowspan="2"><div id="integrantes"></div></td>
                    </tr>
                    <tr>
                        <td><?php $details->setPictureForm('fotoIntegrantes');?></td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
        endif;
        ?>
    </div>
    
    <div class="span12"></div>
    <div id="tabs" class="span11">
        <ul id="nonConformityTab" class="nav nav-tabs">
        <li class="active"><a href="#integrantesDetailTab" data-toggle="tab"><?php echo $resource->getWord("integrantesDetail"); ?></a></li>
        <li><a href="#integrantesTalentosTab" data-toggle="tab"><?php echo $resource->getWord("integrantesTalentos"); ?></a></li>
        <li><a href="#hobiesTab" data-toggle="tab"><?php echo $resource->getWord("hobies"); ?></a></li>     
        <li><a href="#familiaresTab" data-toggle="tab"><?php echo $resource->getWord("familia"); ?></a></li>     
        <li><a href="#infoLaboralTab" data-toggle="tab"><?php echo $resource->getWord("laboral"); ?></a></li>  
        <li><a href="#infoAcademicaTab" data-toggle="tab"><?php echo $resource->getWord("academica"); ?></a></li>
        <li><a href="#infoIdiomasTab" data-toggle="tab"><?php echo $resource->getWord("idiomas"); ?></a></li>
        <li><a href="#redesSocialesTab" data-toggle="tab"><?php echo $resource->getWord("redesSociales"); ?></a></li>
        </ul>
        <div id="TabContent" class="tab-content">
            <div class="tab-pane fade active" id="integrantesDetailTab">
                <div class="spacer10"></div>
                <div class="ui-jqgrid ui-widget ui-corner-all clear-margin span12" dir="ltr" style="">
                    <div id="integrantesDetail"></div>
                </div>
            </div>
            <div class="tab-pane fade" id="integrantesTalentosTab">
                <div class="spacer10"></div>
                <div class="ui-jqgrid ui-widget ui-corner-all clear-margin span12" dir="ltr" style="">
                    <div id="integrantesTalentos"></div>
                </div>
            </div>
            <div class="tab-pane fade" id="hobiesTab">
                <div class="spacer10"></div>
                <div class="ui-jqgrid ui-widget ui-corner-all clear-margin span12" dir="ltr" style="">
                    <table id="hobies"></table>
                    <div id="hobiesPager"></div>
                </div>
            </div>
            <div class="tab-pane fade" id="familiaresTab">
                <div class="spacer10"></div>
                <div class="jqGrid">
                    <div class="ui-jqgrid ui-widget ui-corner-all clear-margin span12" dir="ltr" style="">
                        <table id="familiares"></table>
                        <div id="familiaresPager"></div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="infoLaboralTab">
                <div class="spacer10"></div>
                <div class="jqGrid">
                    <div class="ui-jqgrid ui-widget ui-corner-all clear-margin span12" dir="ltr" style="">
                        <table id="infoLaboral"></table>
                        <div id="infoLaboralPager"></div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="infoAcademicaTab">
                <div class="spacer10"></div>
                <div class="jqGrid">
                    <div class="ui-jqgrid ui-widget ui-corner-all clear-margin span12" dir="ltr" style="">
                        <table id="infoAcademica"></table>
                        <div id="infoAcademicaPager"></div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="infoIdiomasTab">
                <div class="spacer10"></div>
                <div class="jqGrid">
                    <div class="ui-jqgrid ui-widget ui-corner-all clear-margin span12" dir="ltr" style="">
                        <table id="infoIdiomas"></table>
                        <div id="infoIdiomasPager"></div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="redesSocialesTab">
                <div class="spacer10"></div>
                <div class="jqGrid">
                    <div class="ui-jqgrid ui-widget ui-corner-all clear-margin span12" dir="ltr" style="">
                        <table id="redesSociales"></table>
                        <div id="redesSocialesPager"></div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>
<div id="loading"><p><?php echo $resource->getWord("LoadingFile"); ?></p></div>
<script>
    jQuery(function ($) {
        
        jQuery("#loading").dialog({
            closeOnEscape: false,
            autoOpen: false,
            modal: true,
            width: 200,
            height: 100/*,
            open: function(event, ui) { jQuery(".ui-dialog-titlebar-close").hide(); jQuery(".ui-dialog-titlebar").hide();}*/
         });
      var tab = jQuery('#tabs li:eq(0) a').attr("href");
      jQuery(tab).css("opacity", 1);
      <?php
        if( in_array( "administrator", $currentUserRoles ) || in_array( "editor", $currentUserRoles )) :
      ?>
      disableElements(jQuery('#integrantesDetail').children());
      disableElements(jQuery('#integrantesTalentos').children());
      <?php else:?>
          $("#oper").val("edit");
          $("#integrantesDetailForm :input[name='oper']").val("edit"); 
          $("#integrantesTalentosForm :input[name='oper']").val("edit"); 
          $('<input>').attr({type: 'hidden',name: 'tipoIdentificacion',value: $("#tipoIdentificacion").children("option").filter(":selected").val()
            }).appendTo('#integrantesForm');
          
          $("#tipoIdentificacion").attr('disabled','disabled');
          $("#identificacion").attr('readonly','readonly');
          $('<input>').attr({type: 'hidden',name: 'activo',value: $("#activo").children("option").filter(":selected").val()
            }).appendTo('#integrantesForm');
          $("#activo").attr('disabled','disabled');
          $("#nombre").attr('readonly','readonly');
          $("#apellido").attr('readonly','readonly');
          $('<input>').attr({type: 'hidden',name: 'genero',value: $("#genero").children("option").filter(":selected").val()
            }).appendTo('#integrantesForm');
          $("#genero").attr('disabled','disabled');
          $("#email").attr('readonly','readonly');
          $("#Hijos").attr('readonly','readonly');
                    
      <?php endif;?>
   });
</script>