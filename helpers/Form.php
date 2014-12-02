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
        }
	
	function ColModelFromTableForm(){
            $this->data = $this->model->getList();
            $formColmodel = '<form id="'.$this->view.'Form" class="form-horizontal" data-toggle="validator" role="form"><input type="hidden" id="oper" name="oper" value="">';
            $colSize = round(12/$this->entity["formConfig"]["cols"],0);
            $i = 0;
            $dataEvents = "";
            foreach ($this->entity["atributes"] as $col => $value){
                
                if(array_key_exists('references', $value))
                    $colType = "Referenced";
                elseif(array_key_exists('enum', $value))
                    $colType = "enum";
                else
                    $colType = $value["type"];
                
                $hidden = (isset($value['hidden']) && $value['hidden'] == true)? 'hidden': 'show';
                $required = ($value['required'])? 'required': '';
                
                if($i==0)
                    $formColmodel .= '<div class="row-fluid  show-grid">';
                else{				
                    if($i%($colSize -1) == 0)
                        $formColmodel .= '</div><div class="row-fluid  show-grid">';
                }
                if($hidden == 'show')
                    $i++;
                
                
                if($hidden == 'show')
                    $formColmodel .= '<div class="col-xs-'.$colSize.' col-md-'.$colSize.'">';
                $formColmodel .= $this->typeDataStructure($colType,array("model" => $model, "style" => $style,"col" => $col, "value" => $value, "dataForm" => $this->data, "required" => $required, "hidden" => $hidden));
                if($hidden == 'show')
                    $formColmodel .= '</div>';
                if(array_key_exists('dataEvents', $value)){
                    $countEvents = count($value["dataEvents"]);
                    for($m = 0; $m < $countEvents; $m++){
                        $dataEvents .= 'jQuery("#'.$col.'").'.$value["dataEvents"][$m]["type"].'('.$value["dataEvents"][$m]["fn"].');';
                    }
                    
                }
            }
            $formColmodel .= '<div class="row-fluid"><div class="col-xs-12 col-md-12"><div id="dialog-message" title="Datos cargados"></div><button href="#" type="submit" class="btn btn-primary pull-right" id="save">'.$this->loc->getWord("accept").'</button></div></div></form>'
                          . '<script>'
                          . $dataEvents
                          . '	jQuery(document).ready(function() {'
			  . '			jQuery("#'.$this->view.'Form").validator("validate");'
                          . '      		jQuery("#'.$this->view.'Form").submit(function(e){'
			  . '				e.preventDefault();'
			  . '				if(jQuery("#save").hasClass("disabled")) {'
			  . '					"";'
			  . '				}else{'
                          . '					form = jQuery("#'.$this->view.'Form").serialize();'
			  . '					jQuery.ajax({'
			  . '						type: "POST",'
			  . '						url: "'.$this->pluginURL.'edit.php?controller='.$this->view.'",'
			  . '						data: form,'
			  . '						success: function(data){'
			  . '						    jQuery( "#dialog-message" ).dialog({'
                          . '                                                       modal: true,'
                          . '                                                       buttons: {'
                          . '                                                              Ok: function() {'
                          . '                                                                  jQuery( this ).dialog( "close" );'
                          . '                                                              }'
                          . '                                                          }'
			  . '							});'
			  . '						}'
			  . '					});'
			  . '				}'
			  . '			});'
			  . '		});'
			  . '	</script>';	
		return $formColmodel;
	}
}
