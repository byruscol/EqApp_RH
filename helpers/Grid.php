<?php
require_once "DBManager.php"; 
if(!isset($resource)){
	require_once "resources.php";
	$resource = new resources();
}
class Grid extends DBManager
{	
    private $table;
    private $ColMolde;
    private $colnames = array();
    private $baseId;
    private $params;
    private $beforeShowForm = "";
    private $type;
    protected $model;
    protected $entity;
    protected $loc;
    public $ValidateEdit = false;
    public $view;
    public $validateFileSize = false;
    public $validateCode = array();
    public $fileId = array();

    function __construct($type = "table", $p, $v, $t, $y = "Grid") {
            global $resource;
            $this->view = $v;
            $this->params = $p;
            $this->loc = $resource;
            $this->type = $y;
            parent::__construct();
            if($type == "table"){
                require_once $this->pluginPath."/models/".$v."Model.php";
                $this->model = new $v();
                $this->baseId = $t;
                $this->table = $this->pluginPrefix.$t;
                $this->entity = $this->model->entity($p["CRUD"]);
                switch ($this->type){
                    case "Grid": $this->gridBuilderFromTable();break;
                }
            }
    }

    function __destruct() {
    }

    function RelationShipData($references){

            $DataArray = array();

            $query = "SELECT " . $references["id"] . " Id, " . $references["text"] . " Name FROM ". $references["table"];
            
            if(array_key_exists('filter', $references)){
                $query .= " WHERE " . $references["id"] ." " . $references["filter"]["op"] . " " . $references["filter"]["value"];
            }
            //echo $query;
            $Relation = $this->getDataGrid($query, null, null, $references["text"], "ASC");

            foreach ( $Relation["data"] as $k => $v ){
                    $DataArray[] = $Relation["data"][$k]->Id.":".htmlspecialchars($Relation["data"][$k]->Name);
            }
            
            if($this->type == "Form")
                $data = $DataArray;
            else
                $data = implode(";", $DataArray);
            
            return $data;
    }
    function EnumData($enums){
	
        $DataArray = array();               
        $query = "SHOW COLUMNS FROM ".$enums["table"]." WHERE Field = '" . $enums["id"] . "'  " ;
        $Relation = $this->getDataGrid($query, null, null, null, null);
        $e = str_replace(array("'","(",")","enum","ENUM"),"", $Relation["data"][0]->Type);
        $enumList = explode(",",$e);
        foreach ( $enumList as $k){
                    $DataArray[] = $k.":".$k;
            }
            
        if($this->type == "Form")
            $data = $DataArray;
        else
            $data = implode(";", $DataArray);
        
        return $data;
    }
    
    function typeDataStructure($colType,$params){
        switch($this->type){
            case "Grid":
                switch($colType){
                    case 'date':
                            $params["model"] = array_merge($params["model"]
                                                ,array(
                                                    'sorttype' => "date",
                                                    'formatter' => "date",
                                                    'formatoptions' => array('newformat' => 'Y-m-d', 'srcformat' => 'Y-m-d'),
                                                    'editoptions' => array('dataInit'=>"@initDateEdit@")
                                                    )
                                                );
                            break;
                    case 'datetime':
                                    $params["model"] = array_merge($params["model"]
                                                        ,array(
                                                            'sorttype' => "date",
                                                            'formatter' => "date",
                                                            'formatoptions' => array('newformat' => 'Y-m-d H:i:s', 'srcformat' => 'Y-m-d H:i:s'),
                                                            'editoptions' => array('dataInit'=>"@initDateEdit@")
                                                            )
                                                        );
                                    break;
                    case 'mail':
                                $params["model"] = array_merge($params["model"]
                                                     ,array('editrules' => array('email' => true))
                                        );
                                break;
                    case 'enum':
                            $enums = array("table" => $this->entity["tableName"], "id" => $params["col"]);
                            $QueryData = $this->EnumData($enums);
                            $params["model"] = array_merge($params["model"]
                                            ,array(
                                                'edittype' => 'select',
                                                'formatter' => 'select',
                                                'stype' => 'select',
                                                'editoptions' => array( "value" => "@'".$QueryData."'@"),
                                                'searchoptions' => array('value' => "@'".$QueryData."'@")
                                            )
                                        );
                            break;
                    case "Referenced":
                            $QueryData = $this->RelationShipData($params["value"]["references"]);

                            $params["model"] = array_merge($params["model"]
                                            ,array(
                                                'edittype' => 'select',
                                                'formatter' => 'select',
                                                'stype' => 'select',
                                                'editoptions' => array( "value" => "@'".$QueryData."'@"),
                                                'searchoptions' => array('value' => "@'".$QueryData."'@")
                                            )
                                        );
                            if(array_key_exists('dataEvents', $params["value"])){
                                $params["model"]['editoptions']['dataEvents'] = $params["value"]['dataEvents'];
                            }
                            break;
                    case 'file':
                                $this->validateFileSize = true;
                                $this->fileId[] = $col;
                                $this->validateCode[] = "jQuery(document).on('change', '#".$params["col"]."', function() {
                                                            if((jQuery('#".$params["col"]."')[0].files[0].size/".$params["value"]['validateAttr']["factor"].") > ".$params["value"]['validateAttr']["size"]."){
                                                                jQuery('#".$params["col"]."').replaceWith(jQuery('#".$params["col"]."').clone(true));
                                                                jQuery('#".$params["col"]."').val('');
                                                                alert('".sprintf($this->loc->getWord("fileSize"), $params["value"]['validateAttr']["size"], $params["value"]['validateAttr']["units"])."');
                                                            }
                                                          });";

                                $params["model"] = array_merge($params["model"]
                                            ,array(
                                                'edittype' => 'file',
                                                'formatter' => "FilesLinks",
                                                'search' => false,
                                                'editoptions' => array( "enctype" => "multipart/form-data" )
                                            )
                                        );
                            break;
                } break;
            case 'Form': 
                $colType = ($params["hidden"] == 'hidden')?"hidden":$colType;
                switch($colType){
                        case 'hidden':
                                $params["model"] =  '<input type="hidden" id="'.$params["col"].'" name="'.$params["col"].'" placeholder="'.$this->loc->getWord(strtoupper($params["col"]),false).'" value="'.$params["dataForm"]['data'][0]->$params["col"].'">';
                            break;
                        case 'date':
                                $params["model"] =  '<div>'
                                                        . '<label class="control-label">'.$this->loc->getWord($params["col"]).'</label>'
                                                        . '<div class="input-append date" id="'.$params["col"].'"  data-date="1975-01-01" data-date-format="yyyy-mm-dd" data-date-viewmode="years">'
                                                            . '<input class="form-control" '.$params["style"].' name="'.$params["col"].'" type="date" value="'.$params["dataForm"]['data'][0]->$params["col"].'" readonly '.$params["required"].'>'
                                                            . '<span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>'
                                                        . '</div>'
                                                    . '</div>'
                                                    . '<script>'
                                                            . 'jQuery(function(){'
                                                                    . 'jQuery("#'.$params["col"].'").datepicker();'
                                                            . '});'
                                                    . '</script>';
                        break;
                        case 'varchar':
                                $params["model"] = '<div>'
                                                        . '<label for="'.$params["col"].'">'.$this->loc->getWord($params["col"]).'</label>'
                                                        . '<input type="text" class="form-control" '.$params["style"].' id="'.$params["col"].'" name="'.$params["col"].'" placeholder="'.$this->loc->getWord($params["col"]).'" value="'.$params["dataForm"]['data'][0]->$params["col"].'"  '.$params["required"].'>'
                                                    . '</div>';
                            break;
                        case 'email':
                                $params["model"] =  '<div>'
                                                        . '<label for="'.$params["col"].'">'.$this->loc->getWord($params["col"]).'</label>'
                                                        . '<input type="email" class="form-control" '.$params["style"].' id="'.$params["col"].'" name="'.$params["col"].'" placeholder="'.$this->loc->getWord($params["col"]).'" value="'.$params["dataForm"]['data'][0]->$params["col"].'" data-error="Bruh, that email address is invalid"  '.$params["required"].'>'
                                                    . '</div>';
                        break;			
                        case 'int':
                                $params["model"] =  '<div>'
                                                        . '<label for="'.$params["col"].'">'.$this->loc->getWord($params["col"]).'</label>'
                                                        . '<input type="number" class="form-control" '.$params["style"].' id="'.$params["col"].'" name="'.$params["col"].'" placeholder="'.$this->loc->getWord($params["col"]).'" value="'.$params["dataForm"]['data'][0]->$params["col"].'" '.$params["required"].'>'
                                                    . '</div>';
                        break;
                        case 'enum':
                                $enums = array("table" => $this->entity["tableName"], "id" => $params["col"]);
                                $QueryData = $this->EnumData($enums);
                                $countQueryData = count($QueryData);
                                $options = "";
                                for($i = 0; $i < $countQueryData; $i++){
                                    $element = explode(":",$QueryData[$i]);
                                    $selected = ($params["dataForm"]['data'][0]->$params["col"] == $element[0])? 'selected="selected"': "";
                                    $options .='<option value ="'.htmlspecialchars($element[0]).'"'.$selected.'>'.htmlspecialchars($element[1]).'</option>';
                                }
                                $params["model"] = '<div>'
                                                        . '<div>'
                                                            . '<label for="'.$params["col"].'">'.$this->loc->getWord($params["col"]).'</label>'
                                                            . '<select class="form-control" id="'.$params["col"].'" name="'.$params["col"].'" '.$params["required"].'>'
                                                                .$options
                                                            . '</select> '
                                                        . '</div>'
                                                    . '</div>';
                        break;
                        case 'Referenced':
                                $QueryData = $this->RelationShipData($params["value"]["references"]);
                                $countQueryData = count($QueryData);
                                $options = "";
                                for($i  = 0; $i < $countQueryData; $i++){
                                    $element = explode(":",$QueryData[$i]);
                                    $selected = ($params["dataForm"]['data'][0]->$params["col"] == $element[0])? 'selected="selected"': "";
                                    $options .='<option value ="'.htmlspecialchars($element[0]).'" '.$selected.'>'.htmlspecialchars($element[1]).'</option>';
                                }
                            
                                $params["model"] = '<div>'
                                                        . '<div>'
                                                            . '<label for="'.$params["col"].'">'.$this->loc->getWord($params["col"]).'</label>'
                                                            . '<select class="form-control" id="'.$params["col"].'" name="'.$params["col"].'" placeholder="'.$this->loc->getWord($params["col"]).'" '.$params["required"].'>'
                                                                    .$options
                                                            . '</select>'
                                                        . '</div>'
                                                    .'</div>';
                        break;
                    }
                break;
        }
        
        return $params["model"];
    }
    
    function colModelFromTable(){
    	$countCols = count($this->entity["atributes"]);
    	$j=1;
    	$k=1;
    	$numCols = 2;
        $columnValidateEdit = "";
        
        if(array_key_exists("columnValidateEdit", $this->entity)){
            $this->ValidateEdit = true;
            $this->columnValidateEdit = $this->entity["columnValidateEdit"];
        }
        
    	foreach ($this->entity["atributes"] as $col => $value){
    		$this->colnames[] = $col;
    		
    		$hidden = (isset($value['hidden']) && $value['hidden'] == true)? true: false;
                $required = ($value['required'])? true: false;
                $sortable = (isset($value['sortable']) )? $value['sortable']: true;
                
                if($value["type"])
                    $editable = true;
                
    		if($hidden){
                    $required = false;
                    $sortable = false;
                }
                
                
    		$required = ($value['required'])? true: false;
    		
    		if($j <= $numCols){
    			$option = array('rowpos' => $k, 'colpos' => $j);
    		}
    		else{
    			$k++;
    			$j=1;
    			$option = array('rowpos' => $k, 'colpos' => $j);
    		}
    		
    		$model = array(
    				'label' => $this->loc->getWord($col),
                                'name'=> $col,
    				'index'=> $col,
    				'align' => 'center',
    				'sortable' => $sortable,
    				'editable' => (isset($value['editable']) && $value['editable'] == false)? false: true,
    				'editrules' => array('required' => $required),
    				'formoptions' => $option,
    				'hidden' => (isset($value['hidden']) && $value['hidden'] == true)? true: false,
    				'classes'=> 'ellipsis'
    		);
    		
    		if(array_key_exists('references', $value))
    			$colType = "Referenced";
    		else
    			$colType = $value["type"];
    		
                $model = $this->typeDataStructure($colType,array("model" => $model, "col" => $col, "value" => $value));
                
                switch($col){
                    case "parentId": $model["editoptions"]["defaultValue"] = "@function(g){return this.p.postData.filter}@"; break;
                    case "parentRelationShip": $model["editoptions"]["defaultValue"] = "@function(g){return this.p.postData.parent}@"; break;
                }
                if(array_key_exists('edithidden', $value) && $value['edithidden']){
                        $model["editrules"]["edithidden"] = true;
                }
                
                if((!array_key_exists('readOnly', $value) || !$value['readOnly'])
                    && ($colType == "date")){
                        $model["editoptions"]["defaultValue"] = "@function(g){return '".date("Y-m-d", time())."'}@";
                    }
                
                if((!array_key_exists('readOnly', $value) || !$value['readOnly'])
                    && ($colType == "datetime")){
                        $model["editoptions"]["defaultValue"] = "@function(g){return '".date("Y-m-d H:i:s", time())."'}@";
                    }
                
                if(array_key_exists('downloadFile', $value) && $value['downloadFile']["show"]){
                    
                    $icon = $this->pluginURL."images/file.jpg";
                    $rowObjectId = (isset($value['downloadFile']["rowObjectId"]))? $value['downloadFile']["rowObjectId"]:8;
                    $viewParam = (isset($value['downloadFile']["view"]))? $value['downloadFile']["view"] : $this->view;
                    $model["formatter"] = "@function(cellvalue, options, rowObject){"
                                            ."var icon = '".$this->pluginURL."/images/'+rowObject[".$rowObjectId."];"
                                            . "return '<a title=\"'+cellvalue+'\" href=\"".$this->pluginURL."download.php?controller=".$viewParam."&id='+cellvalue+'\" target=\"_blank\"> <img src=\"'+icon+'\"/> </a>'}@";
                }    
                                    
                if($value['text']){
    			$model["edittype"] = "textarea";
    			$model["editoptions"]["rows"] = 6; 
    			$model["editoptions"]["cols"] = 50;
                        $model["searchoptions"]["searchhidden"] = true;
                        $model["editrules"]["edithidden"] = true;
                        
    			if($j == $numCols){
                            $k++;
                            $option = array('rowpos' => $k, 'colpos' => 1);
                            $model["formoptions"] = $option;
    			}
    			$k++;
    			$j=0;
                        
                        $this->beforeShowForm .= "setTextAreaForm(form,'tr_".$col."');";
    		}

    		if($value['readOnly'])
                    $model["editoptions"]["dataInit"] = "@function(element) { jQuery(element).attr('readonly', 'readonly');}@";
    		
    		$j++;
    		$colmodel[] = $model;
    		$model = array();
    	}
        
    	$this->ColModel = str_ireplace('"@',"",json_encode($colmodel));
    	$this->ColModel = str_ireplace('@"',"",$this->ColModel);
    }
	
    function gridBuilderFromTable() {
    	$this->colModelFromTable();
    	$title = $this->table;
    	$files = (isset($this->params["CRUD"]["files"]) && $this->params["CRUD"]["files"])?true:false;
    	$ajaxFileUpload = "";
        if($files){
            $filesCount = count($this->params["fileActions"]);
            for($i = 0; $i < $filesCount; $i++){
                $url = $this->params["fileActions"][$i]["url"];
                $idFile = $this->params["fileActions"][$i]["idFile"];
                $oper = $this->params["fileActions"][$i]["oper"];
                $parentRelationShip = $this->params["fileActions"][$i]["parentRelationShip"];
                $ajaxFileUpload  .= "ajaxFileUpload(result.parentId, '".$url."','".$idFile."','".$oper."','".$parentRelationShip."','".$this->view."');";
            }
        }
        
        if(array_key_exists('postData', $this->params)){
    		if(is_array($this->params['postData']))
    		{	
    			$pd = array();
    			foreach ( $this->params['postData'] as $k => $v ){
    				$pd[] = '"'. $k .'":"'. $v .'"';
	    		}
	    		$postData = ",". implode(",", $pd);
    		}
    		else 
    			$postData = "";
    	}
    	else
    		$postData = "";
    	
        if($this->ValidateEdit){
            $scriptEditing = 'var row = jQuery(this).jqGrid("getRowData", rowid);
                                                if(row.'.$this->columnValidateEdit.' != '.$this->currentUser->ID.'){
                                                    jQuery("#del_' . $this->view . '").hide();
                                                    jQuery("#edit_' . $this->view . '").hide();
                                                }
                                                else{
                                                    jQuery("#del_' . $this->view . '").show();
                                                    jQuery("#edit_' . $this->view . '").show();
                                                };';
            if(is_array($this->params["actions"])){
                $countParams = count($this->params["actions"]);
                $addUpdateFunction = "add";
                for($i = 0; $i < $countParams; $i++){
                    if($this->params["actions"][$i]["type"] == "onSelectRow"){
                        $addUpdateFunction = "update";
                        $content = explode("{",$this->params["actions"][$i]["function"]);
                        $paramsFunction = explode(",",str_replace(array("function","(",")"), "", $content[0]));
                        
                        if(count($paramsFunction) > 0)
                        {
                            $rowid = $paramsFunction[0];
                            $scriptEditing = str_replace("rowid", $rowid, $scriptEditing);
                            $content[1] = $scriptEditing . $content[1];
                            $this->params["actions"][$i]["function"] = implode("{",$content);
                        }
                        break;
                    }
                }
            }
            else
                $addUpdateFunction = "add";
            
            if($addUpdateFunction == "add"){
                $this->params["actions"][]=array("type" => "onSelectRow"
                                                ,"function" => 'function(rowid, e){
                                                    '. $scriptEditing .'
                                                }');
            }
            
            
        }
        $this->beforeShowForm .= ' form.find(".FormElement[readonly]")
                                                        .prop("disabled", true)
                                                        .addClass("ui-state-disabled")
                                                        .closest(".DataTD")
                                                        .prev(".CaptionTD")
                                                        .prop("disabled", true)
                                                        .addClass("ui-state-disabled");';
        
        $grid = 'jQuery(document).ready(function($){
                    $grid = jQuery("#' . $this->view . '"),
                                    initDateEdit = function (elem) {
                                            setTimeout(function () {
                                                    jQuery(elem).datepicker({
                                                            dateFormat: "yy-m-dd",
                                                            autoSize: true,
                                                            showOn: "button", 
                                                            changeYear: true,
                                                            changeMonth: true,
                                                            showButtonPanel: true,
                                                            showWeek: true
                                                    });        
                                            }, 100);
                                    },
                                    numberTemplate = {formatter: "number", align: "right", sorttype: "number",
                                    editrules: {number: true, required: true}
                            };
                    $grid.jqGrid({						
                                    url:"admin-ajax.php",
                                    datatype: "json",
                                    mtype: "POST",
                                    postData : {
                                            action: "action",
                                            id: "' . $this->view . '"
                                            '. $postData.'
                                    },
                                    //colNames:'.json_encode($this->colnames).',					
                                    colModel:'.$this->ColModel.',
                                    rowNum:'. $this->params["numRows"].',
                                    rowList: ['. $this->params["numRows"] .', '. ($this->params["numRows"] * 2) .', '. ($this->params["numRows"] * 3) .', "All"],
                                    pager: "#' . $this->view . 'Pager",						
                                    sortname: "'. $this->params["sortname"].'",
                                    viewrecords: true,
                                    sortorder: "desc",
                                    viewrecords: true,
                                    gridview: true,
                                    height: "100%",
                                    autowidth: true,
                                    editurl: "'.$this->pluginURL.'edit.php?controller='.$this->view.'",
                                    caption:"' . $this->loc->getWord($this->view) . '",
                                    beforeRequest: function() {
                                        responsive_jqgrid(jQuery(".jqGrid"));
                                    }';

                            if(array_key_exists('actions', $this->params))
                            {
                                    foreach ($this->params['actions'] as $key => $value){
                                            $grid .= ',' . $value["type"] .': '. $value["function"];
                                    }
                            }						    

                            $grid .= '});
                            jQuery("#' . $this->view . '").jqGrid("navGrid","#' . $this->view . 'Pager",
                                            {   edit:'.(($this->entity["entityConfig"]["edit"])? "true" : "false").'
                                                ,add:'.(($this->entity["entityConfig"]["add"])? "true" : "false").'
                                                ,del:'.(($this->entity["entityConfig"]["del"])? "true" : "false").'
                                            }';
                                    if($this->entity["entityConfig"]["edit"]){
                                            $grid .= ',{ // edit options
                                                            recreateForm: true,
                                                            viewPagerButtons: true,
                                                            width:"99%",
                                                            reloadAfterSubmit:true,
                                                            closeAfterEdit: true,
                                                            afterSubmit: function(response, postdata){
                                                                var result = jQuery.parseJSON(response.responseText);
                                                                '.$ajaxFileUpload.'
                                                                return [true]
                                                            },
                                                            afterShowForm:function(form){'.$this->beforeShowForm.' ;}
                                                        }';
                                    }
                                    else
                                        $grid .= ',{}';
                                    
                                    if($this->entity["entityConfig"]["add"]){
                                            $grid .= ',{//add options
                                                            recreateForm: true,
                                                            viewPagerButtons: false,
                                                            width:"99%",
                                                            reloadAfterSubmit:true,
                                                            closeAfterAdd: true,
                                                            afterSubmit: function(response, postdata){
                                                                var result = jQuery.parseJSON(response.responseText);
                                                                '.$ajaxFileUpload.'
                                                                return [true]
                                                            },
                                                            afterShowForm:function(form){'.$this->beforeShowForm.' ;}
                                                        }';
                                    }else
                                        $grid .= ',{}';
                                    
                                    if($this->entity["entityConfig"]["add"]){
                                            $grid .= ',{//del option
                                                            mtype:"POST",
                                                            reloadAfterSubmit:true
                                                            ,beforeShowForm:function(form){'.$this->beforeShowForm.'}
                                                        }';
                                    }else
                                        $grid .= ',{}';
                                    
                                            $grid .= ',{multipleSearch:true
                                                            , multipleGroup:false
                                                            , showQuery: false
                                                            , sopt: ["eq", "ne", "lt", "le", "gt", "ge", "bw", "bn", "ew", "en", "cn", "nc", "nu", "nn", "in", "ni"]
                                                            , width:"99%"
                                                        })';
                                            
                                            if($this->entity["entityConfig"]["excel"]){
                                                    $grid .= '.navButtonAdd("#' . $this->view . 'Pager",{
                                                         caption:"Export to Excel",
                                                         id:"csv_' . $this->view . '",
                                                         onClickButton : function () {
                                                             jQuery("#' . $this->view . '").jqGrid("exportarExcelCliente",{nombre:"HOJATEST",formato:"excel"});
                                                         }
                                                      })
                                                    '; 
                                             }
                                                if($this->entity["entityConfig"]["view"]){     
                                                    $grid .= '.navSeparatorAdd("#' . $this->view . 'Pager").navButtonAdd("#' . $this->view . 'Pager",{
                                                            caption:"", 
                                                            title: $.jgrid.nav.viewtitle,
                                                            buttonicon:"ui-icon-document", 
                                                            onClickButton: function(){ 
                                                                var rowid = jQuery("#' . $this->view . '").jqGrid("getGridParam", "selrow");
                                                                if(rowid){
                                                                    $.get( "'.$this->pluginURL.'views/'.$this->view.'View/'.$this->view.'Detail.php" )
                                                                        .done(function( data ) {
                                                                        
                                                                            var rowData = jQuery("#' . $this->view . '").jqGrid("getRowData", rowid);
                                                                            var colModel = jQuery("#' . $this->view . '").jqGrid("getGridParam","colModel");
                                                                            
                                                                            for(i = 0; i < colModel.length; i++){
                                                                                data = data.replace("{"+colModel[i].name+"-Label}", colModel[i].label);
                                                                                var valReplace = rowData[colModel[i].name];
                                                                                if(colModel[i].editoptions && jQuery.type(colModel[i].editoptions["value"]) == "string" && colModel[i].editoptions["value"] != ""){
                                                                                    var selectOptions = colModel[i].editoptions["value"].split(";");
                                                                                    
                                                                                    for(var selOp in selectOptions){
                                                                                        selOpArray = selectOptions[selOp].split(":");
                                                                                        if(selOpArray[0] == valReplace)
                                                                                            valReplace = selOpArray[1];
                                                                                    }
                                                                                }
                                                                                data = data.replace("{"+colModel[i].name+"}", valReplace);
                                                                            }
                                                                           
                                                                            jQuery("<div>"+data+"</div>").dialog({
                                                                                height: 400,
                                                                                width: "75%",
                                                                                modal: true,
                                                                                title: $.jgrid.nav.viewtitle
                                                                              });
                                                                    });
                                                                }
                                                            }, 
                                                            position:"last"
                                                         }).navButtonAdd("#' . $this->view . 'Pager",{
                                                            caption:"", 
                                                            title: $.jgrid.nav.viewdetail,
                                                            buttonicon:"i-icon-zoomin", 
                                                            onClickButton: function(){ 
                                                                var rowid = jQuery("#' . $this->view . '").jqGrid("getGridParam", "selrow");
                                                                if(rowid){
                                                                    jQuery(location).attr("href","admin.php?page=' . $this->view . '&task=Details&rowid="+rowid );                                                                    
                                                                }
                                                            }, 
                                                            position:"last"
                                                        })';
                                                }
                            $grid .= '});';
            if($this->validateFileSize){
                $validateCount = count($this->validateCode);
                for($i = 0; $i < $validateCount; $i++){
                    $grid .=$this->validateCode[$i];
                }
            }
            echo  $grid;
	}
}
?>
