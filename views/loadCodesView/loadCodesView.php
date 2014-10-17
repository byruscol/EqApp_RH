<div class="row-fluid">
    <div class="span11">
        <div class="jqGrid">
            <div class="wrap">
                <div id="icon-tools" class="icon32"></div>
                <h2><?php echo $resource->getWord("loadCodes"); ?></h2>
            </div>
            <div class="span12">
                <form id="formLoadCodes" class="form-horizontal">
                    <div class="span12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><a id="addCode" class="btn btn-info" href="#"><?php echo $resource->getWord("addCode"); ?></a></div>
                            <div id="loadCodes" class="panel-body">
                                <div class="added">
                                    <input type="text" name="code[]" maxlength="7" required="true" pattern=".{7}" id="code_1" placeholder="<?php echo $resource->getWord("code7characters"); ?>"/><a href="#" class="eliminar">&times;</a>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="span12"></div>
                    <div class="span12">
                        <div class="col-xs-5">
                        <label class="" for="captcha"><?php echo $resource->getWord("msjEnterCaptch"); ?></label>
                        <div id="captcha-wrap">
                            <input class="input" id="captcha" name="captcha" type="text" placeholder="<?php echo $resource->getWord("verificationCode");?>" size="15" /> <img src="<?php echo $pluginURL?>/images/refresh.jpg" alt="refresh captcha" width="25" id="refresh-captcha" style="cursor:pointer;" /> <img src="<?php echo $pluginURL?>helpers/newCaptcha.php" alt="" id="captcha" />
                        </div>
                        
                        <div class="span12" id="failCode" style="display:none;">
                            <div class="span4 alert alert-danger" role="alert">
                                <?php echo $resource->getWord("verificationCodeFail");?>
                            </div>
                        </div>
                        </div>
                        <div class="col-xs-7" id="results">
                        </div>
                    </div>
                    
                    <div class="span12">
                        <button type="submit" id="loadCodes" class="btn btn-success" ><?php echo $resource->getWord("loadCodes"); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="loading"><p><?php echo $resource->getWord("LoadingCodes"); ?></p></div>
<script>
    jQuery(function () {
        
        jQuery("#loading").dialog({
            closeOnEscape: false,
            autoOpen: false,
            modal: true,
            width: 200,
            height: 100            
         });
   });
</script>