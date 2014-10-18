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
    private $entity;
    private $params;
    private $loc;
    private $beforeShowForm = "";
    public $ValidateEdit = false;
    public $view;

    function __construct($type = "table", $p, $v, $t) {
            global $resource;
            $this->view = $v;
            $this->params = $p;
            $this->loc = $resource;
            parent::__construct();
            if($type == "table"){
                    require_once $this->pluginPath."/models/".$v."Model.php";
                    $this->baseId = $t;
                    $this->table = $this->pluginPrefix.$t;
                    $this->entity = $v::entity($p["CRUD"]);
                    $this->gridBuilderFromTable();
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
            
            $Relation = $this->getDataGrid($query, null, null, $references["text"], "ASC");

            foreach ( $Relation["data"] as $k => $v ){
                    $DataArray[] = $Relation["data"][$k]->Id.":".htmlspecialchars($Relation["data"][$k]->Name);
            }
            
            $data = implode(";", $DataArray);
            return $data;
    }
    function EnumData($enums){
	
        $DataArray = array();               
        $query = "SHOW COLUMNS FROM ".$enums["table"]." WHERE Field = '" . $enums["id"] . "'  " ;
        $Relation = $this->getDataGrid($query, null, null, null, null);
        $e = str_replace(array("'","(",")","enum"),"", $Relation["data"][0]->Type);
        $enumList = explode(",",$e);
        foreach ( $enumList as $k){
                    $DataArray[] = $k.":".$k;
            }
        $data = implode(";", $DataArray);
        return $data;
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
    				'sortable' => true,
    				'editable' => true,
    				'editrules' => array('required' => $required),
    				'formoptions' => $option,
    				'hidden' => $hidden,
    				'classes'=> 'ellipsis'
    		);
    		
    		if(array_key_exists('references', $value))
    			$colType = "Referenced";
    		else
    			$colType = $value["type"];
    		
    		switch($colType){
    			case 'date':
    				$model = array_merge($model
                                                    ,array(
                                                        'sorttype' => "date",
				    			'formatter' => "date",
				    			'formatoptions' => array('newformat' => 'Y-m-d', 'srcformat' => 'Y-m-d'),
				    			'editoptions' => array('dataInit'=>"@initDateEdit@")
                                                        )
                                                    );
    				break;
    			case 'datetime':
    					$model = array_merge($model
                                                            ,array(
                                                                'sorttype' => "date",
                                                                'formatter' => "date",
                                                                'formatoptions' => array('newformat' => 'Y-m-d H:i:s', 'srcformat' => 'Y-m-d H:i:s'),
                                                                'editoptions' => array('dataInit'=>"@initDateEdit@")
                                                                )
                                                            );
    					break;
                        case 'mail':
                                    $model = array_merge($model
                                                         ,array('editrules' => array('email' => true))
                                            );
                                    break;
                                    
                        case 'enum':
                                $enums = array("table" => $this->entity["tableName"], "id" => $col);
                                $QueryData = $this->EnumData($enums);
    				$model = array_merge($model
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
    				$QueryData = $this->RelationShipData($value["references"]);
    					
    				$model = array_merge($model
    						,array(
                                                    'edittype' => 'select',
                                                    'formatter' => 'select',
                                                    'stype' => 'select',
                                                    'editoptions' => array( "value" => "@'".$QueryData."'@"),
                                                    'searchoptions' => array('value' => "@'".$QueryData."'@")
    						)
                                            );
                                if(array_key_exists('dataEvents', $value)){
                                    $model['editoptions']['dataEvents'] = $value['dataEvents'];
                                }
    				break;
    			case 'longblob':
    		
    				break;
    		}
                
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
                        
                    $model["formatter"] = "@function(cellvalue, options, rowObject){"
                                            ."var icon = '".$this->pluginURL."/images/'+rowObject[8];"
                                            . "return '<a title=\"'+cellvalue+'\" href=\"".$this->pluginURL."download.php?controller=".$this->view."&id='+cellvalue+'\" target=\"_blank\"> <img src=\"'+icon+'\"/> </a>'}@";
                }    
                    
               /* test tooltip description
                * if(array_key_exists('toolTip', $value) && is_array($value['toolTip'])){
                    $return = "";
                    switch($value['toolTip']["type"]){
                        case "cell": $return = "rawObject[".$value['toolTip']["cell"]."]";break;
                        default: $return = '"'.$value['toolTip']["custom"].'"';break;
                    }
                    
                    
                    $model["cellattr"] = "@function (rowId, val, rawObject, cm, rdata) { var tip = noHTMLTags(".$return."); return ' title = ' + tip; }@";
                }   */ 
                    
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
                                                    $(elem).datepicker({
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
                                    rowList: ['. $this->params["numRows"] .', '. ($this->params["numRows"] * 2) .', '. ($this->params["numRows"] * 3) .'],
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
                                                            closeAfterEdit: true
                                                            ,afterShowForm:function(form){'.$this->beforeShowForm.' ;}
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
                                                            closeAfterAdd: true
                                                            ,afterShowForm:function(form){'.$this->beforeShowForm.' ;}
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
                                                if($this->entity["entityConfig"]["view"]){     
                                                    $grid .= '.navSeparatorAdd("#' . $this->view . 'Pager").navButtonAdd("#' . $this->view . 'Pager",{
                                                            caption:"", 
                                                            title: $.jgrid.nav.viewtitle,
                                                            buttonicon:"ui-icon-document", 
                                                            onClickButton: function(){ 
                                                                /*var str = "";
                                                                for(xx in id){
                                                                    str += xx + " -> " + id[xx] + "<br/>";
                                                                }*/
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
                                                                                width: "95%",
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
                            $grid .= '})';

            echo  $grid;
	}
}
?>
