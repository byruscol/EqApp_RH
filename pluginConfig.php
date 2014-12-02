<?php 
function getPahtFile($fileName){
    $CurrentDir = dirname(__FILE__);
    for($i=0;$i<10;$i++){
        if($i==0){
            $dir = $CurrentDir;
        }else{
            $dir = dirname($dir);
        }
        $file = $dir.DIRECTORY_SEPARATOR.$fileName;
        if(file_exists($file)){
            break;
        }      
    }
    return $file;
}
$pluginPath = dirname(__FILE__);
$rp = explode("wp-content", $pluginPath);
$rootPath = $rp[0];
$prot = explode("/",$_SERVER['SERVER_PROTOCOL']);
$protocol = strtolower($prot[0]);
$preFX = explode("/",$_SERVER['REQUEST_URI']);
$URLPrefix = ($preFX[1] != $_SERVER['HTTP_HOST'] && $preFX[1] != "wp-admin")? $preFX[1] : '' ;
$pluginName = "EqApp_RH";
$pluginURL = $protocol."://".$_SERVER['HTTP_HOST']."/".$URLPrefix."/wp-content/plugins/".$pluginName."/";

$prefixPlugin = "rh_";
$GeographicHierarchy = array("country" => array("table" => "countries"
						,"child" => array(
								"table" => "regions"
								,"child" => array(
										"table" => "cities"
										)
								)
						)
			);
?>
