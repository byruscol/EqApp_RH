<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once dirname(__FILE__)."/../pluginConfig.php";
require_once $pluginPath."/helpers/Grid.php";
require_once "class.buildView.php";
header('Content-type: text/javascript');
$postData = array();
?>
    
jQuery('#uploadFiles')
  .submit( function( e ) {
    var rowid;
    e.preventDefault();

    if(jQuery("#parentId").length > 0){
        rowid = jQuery("#parentId").val();
    }
    else{
        rowid = jQuery("#<?php echo $_GET["view"];?>").jqGrid("getGridParam", "selrow");
    }
    if(rowid){
        var form = new FormData( this );
        if(jQuery("#parentId").length == 0)
            form.append("parentId", rowid);
        jQuery.ajax( {
            url: '<?php echo $pluginURL;?>edit.php?controller=files',
            type: 'POST',
            data: form,
            processData: false,
            contentType: false,
            beforeSend: function(jqXHR, settings){
                    jQuery("#loading").dialog('open');
                },
            success: function(response, textStatus, jqXHR){
                data = jQuery.parseJSON( response );
                if (data.msg != 'success')
                {
                    alert(data.error);
                }
                else
                {
                    jQuery("#files").jqGrid().trigger("reloadGrid");
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log("A JS error has occurred.");
            },
            complete: function(jqXHR, textStatus){
                jQuery("#uploadFiles")[0].reset();
                jQuery("#loading").dialog('close');
            }
        } );
    }
    else{
        jQuery("<div>"+jQuery.jgrid.nav.alerttext+"</div>").dialog({
            height: 100,
            width: 200,
            modal: true,
            closeOnEscape: true,
            title: jQuery.jgrid.nav.alertcap
          });
    }
    
    return false;
  } );
<?php
if((isset($_GET["view"]) && !empty($_GET["view"])) && 
    (isset($_GET["rowid"]) && !empty($_GET["rowid"])))
{
    $postData["parent"] = $_GET["view"];
    $postData["filter"] = $_GET["rowid"];
}
$params = array("numRows" => 10, "postData" => $postData);
?>
