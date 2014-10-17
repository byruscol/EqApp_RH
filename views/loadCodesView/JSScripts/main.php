<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once dirname(__FILE__)."/../../../pluginConfig.php";
require_once $pluginPath."/helpers/resources.php";
$resource = new resources();
header('Content-type: text/javascript');
?>
jQuery(document).ready(function() {

    var MaxInputs       = 15; //Número Maximo de Campos
    var contenedor       = jQuery("#loadCodes"); //ID del contenedor
    var AddButton       = jQuery("#addCode"); //ID del Botón Agregar

    //var x = número de campos existentes en el contenedor
    var x = jQuery("#loadCodes div").length + 1;
    var FieldCount = x-1; //para el seguimiento de los campos

    jQuery(AddButton).click(function (e) {
        if(x <= MaxInputs) //max input box allowed
        {
            FieldCount++;
            //agregar campo
            jQuery(contenedor).append('<div class="added"><input type="text" pattern=".{7}" required="true" name="code[]" maxlength="7" id="code_'+ FieldCount +'" placeholder="<?php echo $resource->getWord("code7characters"); ?> "/><a href="#" class="eliminar">&times;</a></div>');
            x++; //text box increment
        }
        return false;
    });

    jQuery("body").on("click",".eliminar", function(e){ //click en eliminar campo
        if( x > 1 ) {
            jQuery(this).parent('div').remove(); //eliminar el campo
            x--;
        }
        return false;
    });
    
    jQuery('#refresh-captcha').click(function()
    {
        jQuery('img#captcha').attr("src","<?php echo $pluginURL?>/helpers/newCaptcha.php?rnd=" + Math.random());
    });
    
    jQuery(':input[name="captcha"]').focus(function(){
        jQuery('#failCode').css("display", "none");
    })
    
    function sendCodes(){
        
        return false;
    }
    
    jQuery('#formLoadCodes')
        .submit( function( e ) {
             
            var captchaRequest = jQuery.ajax({
                         type: "POST",
                         url: '<?php echo $pluginURL?>/helpers/checkCaptcha.php',
                         data: {
                            "captcha": jQuery(':input[name="captcha"]').val()
                        }
                    });
            captchaRequest.done(function(msg)
            {
                var jsonResponce = jQuery.parseJSON(msg)
                var formData = new FormData();
                formData.append("oper", "add");
                jQuery("#loadCodes input").each(function (index) {
                    formData.append("code[]", jQuery(this).val());
                });
                
                formData.append("captcha", jQuery(':input[name="captcha"]').val());
                
                if(jsonResponce.msg){
                    var submitRequest = jQuery.ajax({
                            type: 'POST',
                            processData: false,
                            contentType: false,
                            url: '<?php echo $pluginURL;?>edit.php?controller=loadCodes',
                            data: formData,
                            beforeSend: function(jqXHR, settings){
                                jQuery("#results").empty();
                                jQuery("#loading").dialog('open');
                            },
                             success: function(response, textStatus, jqXHR){
                                if(response == "captchaFail"){
                                    jQuery('#failCode').css("display", "block");
                                }
                                else{
                                    data = jQuery.parseJSON( response );
                                    dataLength = data.length, table = '<table class="table table-condensed"><tr><th><?php echo $resource->getWord("code"); ?></th><th><?php echo $resource->getWord("status"); ?></th></tr>';
                                    for(i = 0; i < dataLength; i++){
                                        status = (data[i].status == "1")? "success" : "danger";
                                        table += '<tr class="'+status+'"><td>'+data[i].code+'</td><td>'+data[i].statusText+'</td></tr>';
                                    }
                                    table += '<table>';
                                    jQuery("#results").append(table);
                                }
                            },
                            complete: function(jqXHR, textStatus){
                                jQuery("#loading").dialog('close');
                                
                                jQuery(':input[name="captcha"]').val("");
                                jQuery('img#captcha').attr("src","<?php echo $pluginURL?>/helpers/newCaptcha.php?rnd=" + Math.random());
                                jQuery(contenedor).empty();
                                jQuery(contenedor).append('<div class="added"><input type="text" pattern=".{7}" required="true" name="code[]" maxlength="7" id="code_'+ FieldCount +'" placeholder="<?php echo $resource->getWord("code7characters"); ?> "/><a href="#" class="eliminar">&times;</a></div>');
                            }
                        });
                    
                }
                else{
                    jQuery('#failCode').css("display", "block");
                }
            });

            captchaRequest.fail(function(jqXHR, textStatus)
            {
                console.log( "fail - an error occurred: (" + textStatus + ")." );
            });

            return false;
        });
});