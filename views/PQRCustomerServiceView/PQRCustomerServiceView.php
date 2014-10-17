<div class="row-fluid">
    <div class="span11">
        <div class="wrap">
            <div id="icon-tools" class="icon32"></div>
            <h2><?php echo $resource->getWord("DashboardPQRCustomerService"); ?></h2>
        </div>
    </div>
    <div class="span12"></div>
    <div id="dashboard" class="span11">
        <div id="PPieChart" style="height:250px" class="col-md-4"></div>
        <div id="QPieChart" style="height:250px" class="col-md-4"></div>  
        <div id="RPieChart" style="height:250px" class="col-md-4"></div>
        <div id="PQRBarSourceChart" style="height:250px" class="col-md-6"></div>
        <div id="RPieChart" style="height:250px" class="col-md-6"></div>
    </div>
</div>    
<!--    <div class="span11">
        <div class="jqGrid">
            <div class="wrap">
                <div id="icon-tools" class="icon32"></div>
                <h2><?php echo $resource->getWord("tasks"); ?></h2>
            </div>
            <div class="row-fluid">
                <div id="taskPieChart" style="height:250px" class="col-md-5"></div>  
                <div id="taskBarChart" style="height:250px" class="col-md-7"></div>  
            </div>
            <div class="span12">
            <table id="tasks"></table>
            <div id="tasksPager"></div>
            </div>
        </div>
    </div>
    
    <div class="span12"></div>
    <div id="tabs" class="span11">
        <ul id="tasksTab" class="nav nav-tabs">
        <li class="active"><a href="#notesTab" data-toggle="tab"><?php echo $resource->getWord("notes"); ?></a></li>      
        <li><a href="#filesTab" data-toggle="tab"><?php echo $resource->getWord("files"); ?></a></li>
        </ul>
        <div id="TabContent" class="tab-content">
            <div class="tab-pane fade active" id="notesTab">
                <div class="spacer10"></div>
                <div class="ui-jqgrid ui-widget ui-corner-all clear-margin span12" dir="ltr" style="">
                    <table id="notes"></table>
                    <div id="notesPager"></div>
                </div>
            </div>
            <div class="tab-pane fade active" id="filesTab">
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
                                <input type="hidden" name="parentRelationShip" value="tasks"/>
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
            height: 100/*,
            open: function(event, ui) { jQuery(".ui-dialog-titlebar-close").hide(); jQuery(".ui-dialog-titlebar").hide();}*/
         });
      var tab = jQuery('#tasksTab li:eq(0) a').attr("href");
      jQuery(tab).css("opacity", 1);
   });
</script>-->