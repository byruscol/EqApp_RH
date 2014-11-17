<?php
require_once "Grid.php"; 
if(!isset($resource)){
	require_once "resources.php";
	$resource = new resources();
}
class Form extends Grid
{	
	private $path = "../wp-content/plugins/Talento_Vial/";
	private $PrefixPlugin;
	public $pluginPath;
        
        function __construct($p, $v, $t = null) {
            parent::__construct("table", $p, $v, $t, "Form"); 
            $this->rederForm();
        }
        
        function __destruct() {}
        
        function rederForm(){
            $form = $this->ColModelFromTableForm();
            echo 'jQuery(\'#'.$this->view.'\').html(\''.$form.'\')';
            
            //echo 'jQuery("#integrantesDetail").html(\'<form id="form" data-toggle="validator" role="form"><div class="row"></div><div class="row"></div><div class="row"><div class="show">dfgfgd</div></div><div class="row"></div><div class="row"></div><div class="row"></div><div class="row"></div><div class="row"></div><div class="row"><div class="hidden">dfgfgd</div></div><div class="row"></div><div class="row"></div><div class="row"><div class="hidden">dfgfgd</div></div><div class="row"><div class="col-md-7"><br><div id="dialog-message" title="Datos cargados"></div><button href="#" type="submit" class="btn btn-primary" id="save">Submit</button></div></form>\')';
        }
	
	function ColModelFromTableForm(){
            $this->data = $this->model->getList();
            $form = '<form id="form" class="form-horizontal" data-toggle="validator" role="form">';
            $colSize = round(12/$this->entity["formConfig"]["cols"],0);
            $i = 0;
            foreach ($this->entity["atributes"] as $col => $value){
                if(array_key_exists('references', $value))
                    $colType = "Referenced";
                elseif(array_key_exists('enum', $value))
                    $colType = "enum";
                else
                    $colType = $value["type"];
                
                $hidden = (isset($value['hidden']) && $value['hidden'] == true)? 'hidden': 'show';
                
                if($i==0)
                    $formColmodel .= '<div class="row-fluid">';
                else{				
                    if($i%($colSize -1) == 0)
                        $formColmodel .= '</div><div class="row-fluid">';
                }
                if($hidden == 'show')
                    $i++;
                
                
                if($hidden == 'show')
                    $formColmodel .= '<div class="col-xs-'.$colSize.' col-md-'.$colSize.'">';
                $formColmodel .= $this->typeDataStructure($colType,array("model" => $model, "style" => $style,"col" => $col, "value" => $value, "dataForm" => $this->data, "required" => $required, "hidden" => $hidden));
                if($hidden == 'show')
                    $formColmodel .= '</div>';
            }
            $form .= '</form>';
		/*$this->data = $this->model->getList();
		$PrimaryKey = 0;
		
		$countCols = count($this->entity["atributes"]);
		$j=1;
		$k=1;
		$i=0;
		$numCols = 2;
		$columnValidateEdit = "";
		$formColmodel = '<form id="form" data-toggle="validator" role="form">';
		foreach ($this->entity["atributes"] as $col => $value){
                    if(isset($value['PK'])){
                        $PrimaryKey = $this->data['data'][0]->$col;
                    }	

                    $this->colnames[] = $col;
                    $label = $col;
                    if(isset($value['label'])){
                        $label = $value['label'];
                    }

                    if($i==0){
                            $formColmodel .= '<div class="row">';
                    }else{				
                        if($i==2){
                            $i=0;
                            $formColmodel .= '</div><div class="row">';
                        }
                    }
                    $i++;

                    $required = ($value['required'])? 'required': ''; 

                    if(array_key_exists('references', $value))
                        $colType = "Referenced";
                    elseif(array_key_exists('enum', $value))
                        $colType = "enum";
                    else
                        $colType = $value["type"];

                    $style = 'style="height:15px; max-width: 80%;" class="form-control"';

                    $hidden = (isset($value['hidden']) && $value['hidden'] == true)? 'hidden': 'show';

                    $formColmodel .= $this->typeDataStructure($colType,array("model" => $model, "style" => $style,"col" => $col, "value" => $value, "dataForm" => $this->data, "required" => $required, "hidden" => $hidden));
		}
		$formColmodel .='';
		$formColmodel .='<div class="col-md-7"><br><div id="dialog-message" title="Datos cargados"></div><button href="#" type="submit" class="btn btn-primary" id="save">Submit</button></div></form>';
		*/		
		/*$formColmodel .='<script>
					$(document).ready(function() {
						$("#form").validator("validate");
						
						$("#form").submit(function(e){
							e.preventDefault();
							if($("#save").hasClass("disabled")) {
								"";
							}else{
								form = $("#form").serialize();
								//$.post("'.plugins_url().'/'.$this->pluginName.'/edit.php?controller='.$this->entity["Model"].'&oper=edit", form)
								var oper = "add";
								if('.$PrimaryKey.'!=0)
									oper = "edit";
									
								$.ajax({
									type: "POST",
									url: "'.plugins_url().'/'.$this->pluginName.'/edit.php?controller='.$this->entity["Model"].'&oper="+oper,
									data: form,						 
									success: function(data){
									    $( "#dialog-message" ).dialog({
                                                                                    modal: true,
                                                                                    buttons: {
                                                                                        Ok: function() {
                                                                                            $( this ).dialog( "close" );
                                                                                        }
                                                                                    }
										});
									}
								});
							}
						});
					});
				</script>';*/
		return $formColmodel;
	}
}
