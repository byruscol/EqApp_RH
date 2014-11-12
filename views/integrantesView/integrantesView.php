<div class="row-fluid">
    <div class="span11">
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
    </div>
    
    <div class="span12"></div>
    <div id="tabs" class="span11">
        <ul id="nonConformityTab" class="nav nav-tabs">
        <li class="active"><a href="#integrantesDetailTab" data-toggle="tab"><?php echo $resource->getWord("integrantesDetail"); ?></a></li>     
        <li><a href="#familiaresTab" data-toggle="tab"><?php echo $resource->getWord("familia"); ?></a></li>     
        <li><a href="#infoLaboralTab" data-toggle="tab"><?php echo $resource->getWord("laboral"); ?></a></li>  
        <li><a href="#infoAcademicaTab" data-toggle="tab"><?php echo $resource->getWord("academica"); ?></a></li>
        <li><a href="#infoIdiomasTab" data-toggle="tab"><?php echo $resource->getWord("idiomas"); ?></a></li>
        </ul>
        <div id="TabContent" class="tab-content">
            <div class="tab-pane fade active" id="integrantesDetailTab">
                <div class="spacer10"></div>
                <div class="ui-jqgrid ui-widget ui-corner-all clear-margin span12" dir="ltr" style="">
                    <div id="integrantesDetail"></div>
                </div>
            </div>
            <div class="tab-pane fade active" id="familiaresTab">
                <div class="spacer10"></div>
                <div class="ui-jqgrid ui-widget ui-corner-all clear-margin span12" dir="ltr" style="">
                    <table id="familiares"></table>
                    <div id="familiaresPager"></div>
                </div>
            </div>
            <div class="tab-pane fade active" id="infoLaboralTab">
                <div class="spacer10"></div>
                <div class="jqGrid">
                    <div class="ui-jqgrid ui-widget ui-corner-all clear-margin span12" dir="ltr" style="">
                        <table id="infoLaboral"></table>
                        <div id="infoLaboralPager"></div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade active" id="infoAcademicaTab">
                <div class="spacer10"></div>
                <div class="jqGrid">
                    <div class="ui-jqgrid ui-widget ui-corner-all clear-margin span12" dir="ltr" style="">
                        <table id="infoAcademica"></table>
                        <div id="infoAcademicaPager"></div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade active" id="infoIdiomasTab">
                <div class="spacer10"></div>
                <div class="jqGrid">
                    <div class="ui-jqgrid ui-widget ui-corner-all clear-margin span12" dir="ltr" style="">
                        <table id="infoIdiomas"></table>
                        <div id="infoIdiomasPager"></div>
                    </div>
                </div>
            </div>
            <!--<div class="tab-pane fade active" id="filesTab">
                <div class="spacer10"></div>
                <div class="span12">
                    <div class="span8">
                        <div class="jqGrid">
                            <div class="ui-jqgrid ui-widget ui-corner-all clear-margin span12" dir="ltr" style="">
                                <table id="files"></table>
                                <div id="filesPager"></div>
                            </div>
                        </div>
                    </div>
                    <div class="span3">
                        <form id="uploadFiles" class="form-horizontal" enctype="multipart/form-data" method="post">
                            <fieldset>

                            
                            <legend><?php echo $resource->getWord("uploadFile"); ?></legend>

                            
                            <div class="control-group">
                              <div class="controls">
                                <input id="name" name="name" placeholder="<?php echo $resource->getWord("fileName"); ?>" class="input-xlarge" required="true" type="text">
                                <input type="hidden" name="oper" value="add"/>
                                <input type="hidden" name="parentRelationShip" value="nonConformity"/>
                              </div>
                            </div>
                            <br/>
                            
                            <div class="control-group">
                              <div class="controls">
                                  <input type="file" id="file" name="file" class="btn btn-default" required="true">
                                </div>
                              </div>
                              <br/>
                            
                            <div class="control-group">
                              <div class="controls">
                                <button id="submit" name="submit" class="btn btn-primary"><?php echo $resource->getWord("accept"); ?></button>
                              </div>
                            </div>

                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>-->
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
      var tab = jQuery('#nonConformityTab li:eq(0) a').attr("href");
      jQuery(tab).css("opacity", 1);
   });
</script>