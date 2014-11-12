<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Form
 *
 * @author asus
 */
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
        
        function __construct($p, $v, $t) {
            parent::__construct("table", $p, $v, $t);  
        }
        
        function __destruct() {}	
	
	function ColModelFromTableForm(){
		
		$this->data = $this->model->getList();
		$this->entity = $this->model->entity();
		$dataForm = $this->data;
		
		$PrimaryKey = 0;
		//print_r($this->entity);
		
		$countCols = count($this->entity["atributes"]);
		$j=1;
		$k=1;
		$i=0;
		$numCols = 2;
		$columnValidateEdit = "";
		$formColmodel = '					
			<form id="form" data-toggle="validator" role="form">			
		';
		foreach ($this->entity["atributes"] as $col => $value){
			if(isset($value['PK'])){
				$PrimaryKey = $dataForm['data'][0]->$col;
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
				$colType = "Enum";
			else
				$colType = $value["type"];
			
			$style = 'style="height:15px; max-width: 80%;" class="form-control"';
			
			$hidden = (isset($value['hidden']) && $value['hidden'] == true)? 'hidden': 'show';
			
			//echo $colType;
			switch($colType){
				case 'date':
					$formColmodel .= '
						<div class="'.$hidden.'">
							<div class="form-group">
								<label class="col-sm-2 control-label">'.diccionario::getWord(strtoupper($col),false).'</label>								
								<div class="col-sm-2">
									<div class="input-append date" id="'.$col.'"  data-date="1975-01-01" data-date-format="yyyy-mm-dd" data-date-viewmode="years">
									   <input '.$style.' name="'.$col.'" type="date" value="'.$dataForm['data'][0]->$col.'" readonly '.$required.'>
									   <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
									</div>
							   </div>
							</div>
						</div>
						
						<script>
							$(function(){							
								$("#'.$col.'").datepicker();
							});
						</script>
						
					';
				break;
				case 'varchar':
					$formColmodel .= '
						<div class="'.$hidden.'">
							<div class="form-group">
								<label  class="col-sm-2 control-label">'.diccionario::getWord(strtoupper($col),false).'</label>
								<div class="col-sm-2">
									<input type="text" '.$style.' id="'.$col.'" name="'.$col.'" placeholder="'.diccionario::getWord(strtoupper($col),false).'" value="'.$dataForm['data'][0]->$col.'"  '.$required.'>
								</div>
							</div>
						</div>
						';
				break;
				case 'email':
					$formColmodel .= '
						<div class="'.$hidden.'">
							<div class="form-group">
								<label  class="col-sm-2 control-label">'.diccionario::getWord(strtoupper($col),false).'</label>
								<div class="col-sm-2">
									<input type="email" '.$style.' id="'.$col.'" name="'.$col.'" placeholder="'.diccionario::getWord(strtoupper($col),false).'" value="'.$dataForm['data'][0]->$col.'" data-error="Bruh, that email address is invalid"  '.$required.'>
								</div>
							</div>
						</div>
						';
				break;			
				case 'int':
					$formColmodel .= '
						<div class="'.$hidden.'">
							<div class="form-group">
								<label  class="col-sm-2 control-label">'.diccionario::getWord(strtoupper($col),false).'</label>
								<div class="col-sm-2">
									<input type="number" '.$style.' id="'.$col.'" name="'.$col.'" placeholder="'.diccionario::getWord(strtoupper($col),false).'" value="'.$dataForm['data'][0]->$col.'" '.$required.'>
								</div>
							</div>
						</div>
						';
				break;
				case 'Enum':
					$QueryData = $this->EnumData($value["enum"]);
					$formColmodel .= '
						<div class="'.$hidden.'">
						<div class="form-group">
							<label  class="col-sm-2 control-label">'.diccionario::getWord(strtoupper($col),false).'</label>
							<div class="col-sm-2">
								<select style="height:30px; max-width: 100%;" class="form-control" id="'.$col.'" name="'.$col.'" placeholder="'.diccionario::getWord(strtoupper($col),false).'" required>
								'.$QueryData.'	
								</select> 
							</div>
						</div>
						<script>
							$(function(){																
								$("#'.$col.'").val("'.$dataForm['data'][0]->$col.'");
							});
						</script>
						</div>
						
					';
				break;
				case 'Referenced':
					$QueryData = $this->RelationShipData($value["references"]);
					$formColmodel .= '
						<div class="'.$hidden.'">
						<div class="form-group">
							<label  class="col-sm-2 control-label">'.diccionario::getWord(strtoupper($col),false).'</label>
							<div class="col-sm-2">
								<select style="height:30px; max-width: 100%;" class="form-control" id="'.$col.'" name="'.$col.'" placeholder="'.diccionario::getWord(strtoupper($col),false).'" required>
								'.$QueryData.'	
								</select> 
							</div>
						</div>
						<script>
							$(function(){																
								$("#'.$col.'").val("'.$dataForm['data'][0]->$col.'");
							});
						</script>
						</div>
					';
				break;
			}
		
		}
		
		$formColmodel .='<div class="col-md-7">
					<br>
					<div id="dialog-message" title="Datos cargados">						
					</div>
						<button href="#" type="submit" class="btn btn-primary" id="save">Submit</button>						 
					</div>
				';
		$formColmodel .='</form>';
				
		$formColmodel .='
				<script>
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

					
				</script>
				
				';
		return $formColmodel;
	}
}
