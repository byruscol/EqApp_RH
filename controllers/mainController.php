<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

class mainController //extends resources
{
    private $pluginPath = "";
    private $pluginURL = "";
    public $model;
    private $viewObject;
    private $prefix;
    private $PrefixPlugin;
    private $controllerName;
    private $headScripts = array();
    private $resource;
    function __construct($controller = "basic", $showView = true) {

        global $prefixPlugin;
        global $pluginURL;
        global $pluginPath;
        global $resource;
        $this->prefix = $prefixPlugin;
        $this->pluginURL = $pluginURL;
        $this->pluginPath = $pluginPath;
        $this->resource = $resource;
        
        $controllerFile = $this->pluginPath."/models/".$controller."Model.php";

        if(file_exists($controllerFile)){ 
                require_once($controllerFile);
        }
        else
        {
            $controller = "basic"; //echo "ok2";
            require_once($this->pluginPath. "/models/basicModel.php");
        }

        $this->model = new $controller();
        $this->controllerName = $controller;

        $this->PrefixPlugin = $this->model->pluginPrefix;

        if(substr_count($_SERVER["SCRIPT_NAME"], "admin-ajax") == 0)
        {
            if($showView){
                add_action('admin_head', array($this, 'setHeadScripts'));
                add_action('admin_head', array($this, 'viewJSScripts'));
            }
        }

        if($showView){
            $this->view = $controller."View";
            add_action( 'admin_menu', array($this, 'Plugin_menu') );
            add_action( 'wp_ajax_action', array($this, 'action_callback'));
        }
    }

    function __destruct() {
    }

    function Plugin_menu() {
        $object = $this->model->getDataGrid("SELECT * FROM ".$this->PrefixPlugin."menus WHERE MenuStatus = 0",0, 200); 
        $menus = $object["data"];
        $countMenus = count($menus);
        for ( $i = 0; $i <  $countMenus; $i++)
        {
            //echo $menus[$i]->MenuTitle."<br>";
                if($menus[$i]->MenuType == 1)
                        add_menu_page($this->resource->getWord($menus[$i]->PageTitle),$this->resource->getWord($menus[$i]->MenuTitle), $menus[$i]->Capability, $menus[$i]->MenuSlug, $menus[$i]->FunctionMenu);
                else
                        add_submenu_page( $menus[$i]->parentSlug, $this->resource->getWord($menus[$i]->PageTitle),$this->resource->getWord($menus[$i]->MenuTitle), $menus[$i]->Capability, $menus[$i]->MenuSlug, $menus[$i]->FunctionMenu );
        }
    }

    function setHeadScripts()
    {
        wp_register_style( 'bootstrapCss', $this->pluginURL . 'css/bootstrap.min.css');
        wp_enqueue_style( 'bootstrapCss' );
        wp_register_style( 'bootstrapResponsiveCss', $this->pluginURL . 'css/bootstrap-responsive.min.css');
        wp_enqueue_style( 'bootstrapResponsiveCss' );
        wp_register_style( 'bootstrapThemeCss', $this->pluginURL . 'css/bootstrap-theme.min.css');
        wp_enqueue_style( 'bootstrapThemeCss' );
        wp_register_style( 'uiCss', $this->pluginURL . 'css/jqGrid/themes/ui-lightness/jquery-ui.min.css');
        wp_enqueue_style( 'uiCss' );
        wp_register_style( 'gridCss', $this->pluginURL . 'css/jqGrid/ui.jqgrid.css');
        wp_enqueue_style( 'gridCss' );
        wp_register_style( 'pluginCss', $this->pluginURL . 'css/plugincss.css');
        wp_enqueue_style( 'pluginCss' );
        wp_register_style( 'normalizeCss', $this->pluginURL . 'css/normalize.css');
        wp_enqueue_style( 'normalizeCss' );
        wp_register_style( 'datepickerCss', $this->pluginURL . 'css/datepicker.css');
        wp_enqueue_style( 'datepickerCss' );

        $this->headScripts[] = 'jquery';
        wp_register_script('locale_es', $this->pluginURL . 'js/jqGrid/grid.locale-es.js', $this->headScripts);
        wp_enqueue_script('locale_es');

        $this->headScripts[] = 'locale_es';
        wp_register_script('jqGrid', $this->pluginURL . 'js/jqGrid/jquery.jqGrid.src.js', $this->headScripts);
        wp_enqueue_script('jqGrid');

        $this->headScripts[] = 'jqGrid';
        wp_register_script('ajaxfileupload',  $this->pluginURL . 'js/ajaxfileupload.js',$this->headScripts);
        wp_enqueue_script( 'ajaxfileupload' );
        
        $this->headScripts[] = 'ajaxfileupload';
        wp_register_script('tinymce',  $this->pluginURL . 'js/tinymce/tinymce.min.js',$this->headScripts);
        wp_enqueue_script( 'tinymce' );

        $this->headScripts[] = 'tinymce';
        wp_register_script('validator',  $this->pluginURL . 'js/validator.js',$this->headScripts);
        wp_enqueue_script( 'validator' );

        $this->headScripts[] = 'validator';
        wp_register_script('tinymceJQuery',  $this->pluginURL . 'js/tinymce/jquery.tinymce.min.js',$this->headScripts);
        wp_enqueue_script( 'tinymceJQuery' );

        $this->headScripts[] = 'tinymceJQuery';
        wp_register_script('googlechart', 'https://www.google.com/jsapi',$this->headScripts);
        wp_enqueue_script( 'googlechart' );
  
        $this->headScripts[] = 'googlechart';
        wp_register_script('pluginjs',  $this->pluginURL . 'js/pluginjs.js',$this->headScripts);
        wp_enqueue_script( 'pluginjs' );

        $this->headScripts[] = 'pluginjs';
        wp_register_script('jquery-u', $this->pluginURL . 'js/jquery-ui-1.10.4.custom.min.js' ,$this->headScripts);
        wp_enqueue_script('jquery-u');

        $this->headScripts[] = 'jquery-u';
        wp_register_script('jCombo', $this->pluginURL . 'js/jquery.jCombo.js' ,$this->headScripts);
        wp_enqueue_script('jCombo');

        $this->headScripts[] = 'jCombo';
        wp_register_script('bootstrap',  $this->pluginURL . 'js/bootstrap.js',$this->headScripts);
        wp_enqueue_script( 'bootstrap' );

        $this->headScripts[] = 'bootstrap';
        wp_register_script('ExportExcel', $this->pluginURL . 'js/jqgridExcelExportClientSide.js',$this->headScripts);
	wp_enqueue_script( 'ExportExcel' );
		
	$this->headScripts[] = 'ExportExcel';	
	wp_register_script('ExportExcelclient', $this->pluginURL . 'js/jqgridExcelExportClientSide-libs.js',$this->headScripts);
	wp_enqueue_script( 'ExportExcelclient' );
		
	$this->headScripts[] = 'ExportExcelclient';
        wp_register_script('bootstrap-datepicker', $this->pluginURL . 'js/bootstrap-datepicker.js',$this->headScripts);
	wp_enqueue_script( 'bootstrap-datepicker' );
		
	$this->headScripts[] = 'bootstrap-datepicker';
    }

    function viewJSScripts() {
        $viewJSScripts = "/views/" . $this->view . "/JSScripts/";
        $JSPath = $this->pluginPath . $viewJSScripts;

        if(is_dir($JSPath))
        {
            $dir = opendir($JSPath);
            while ($file = readdir($dir)){
                if( $file != "." && $file != ".."){
                    if(is_file($JSPath.$file)){
                        $js =  $this->pluginURL . $viewJSScripts . $file ."?view=" . $this->controllerName;
                         if(array_key_exists('rowid', $_GET))
                            $js .= "&rowid=" . $_GET["rowid"];
                        $registerName = str_replace(".","",$file)."_" . $this->controllerName;
                        wp_register_script($registerName, $js, $this->headScripts);
                        wp_enqueue_script($registerName);
                        $this->headScripts[] = $registerName;
                    }
                }
            }
        }
    }

    function action_callback() {
        $responce = new StdClass;
        $page = ($_POST['page'] == 0)? 1:$_POST['page']; // get the requested page
        $limit = $_POST['rows']; // get how many rows we want to have into the grid
        $sidx = $_POST['sidx']; // get index row - i.e. user click to sort
        $sord = $_POST['sord']; // get the direction
        if ($limit < 0) $limit = 0;

        if(!$sidx) $sidx =1;

        $params = array(
                        "page" => $page
                        ,"sidx" => $sidx
                        ,"sord" => $sord
                        ,"limit" => $limit
                    );
        
        if(array_key_exists('query', $_POST))
            $params["filter"] = $_POST["query"];
        
        if(array_key_exists('filter', $_POST))
            $params["filter"] = $_POST["filter"];

        if(array_key_exists('filters', $_POST) && !empty($_POST["filters"]))
            $params["where"] = json_decode (stripslashes($_POST["filters"]));

        if(array_key_exists('method', $_POST)){
            $grid = $this->model->$_POST["method"]($params);
        }
        else{
            $grid = $this->model->getList($params);
        }
        
        if($grid["customResponce"]){
            $responce = $grid["data"];
        }
        elseif(is_array($grid["data"])){
            if( $grid["totalRows"] > 0 && $limit > 0)
                    $total_pages = ceil($grid[totalRows]/$limit);
            else 
                    $total_pages = 0;

            if ($page > $total_pages) $page = $total_pages;

            $responce->page = $page;
            $responce->total = $total_pages;
            $responce->records = $grid[totalRows];

            $countRows = count($grid["data"]);
            $j = 0;
            for ( $i = 0; $i < $countRows; $i++ )
            {
                    foreach ( $grid["data"][$i] as $key => $value ){
                            if(is_numeric($value))
                                $value = $value + 0;
                            if($j == 0){
                                $responce->rows[$i]['id']=$value;
                                $j = 1;
                            }
                            $responce->rows[$i]['cell'][]=$value;
                    }
                    $j = 0;
            }
        }elseif(is_object($grid["data"])){
            $responce->page = 1;
            $responce->total = 1;
            $responce->records = 1;
            foreach ( $grid["data"] as $key => $value ){
                $responce->row[$key]=$value;
            }
        }
        
        echo json_encode($responce, JSON_UNESCAPED_UNICODE);
        die();
    }

    function editOper($oper){
        $this->model->$oper();
    }

    function downloadFile($fileId){
        ini_set('memory_limit','16M');
        $this->model->rendererFile($fileId);
    }
}
?>
