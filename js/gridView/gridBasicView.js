/*$dataRow->nonConformityId
,$dataRow->name
,$dataRow->date_entered
,$dataRow->date_modified
,$dataRow->modified_user_id
,$dataRow->created_by
,$dataRow->description
,$dataRow->deleted
,$dataRow->assigned_user_id
,$dataRow->estadonc
,$dataRow->nombre_del_clientenc
,$dataRow->telefononc
,$dataRow->fuentenc
,$dataRow->generalidadnc
,$dataRow->sedenc
,$dataRow->unidaddenegocionc
,$dataRow->gestion
,$dataRow->clasificacion_nc*/
jQuery(document).ready(function(){
	jQuery("#list2").jqGrid({
	   	url:'admin-ajax.php',
		datatype: "json",
		postData : {action: 'my_action'},
	   	colNames:['nonConformityId','name', 'date_entered', 'date_modified','description','estadonc','nombre_del_clientenc'],
	   	colModel:[
	   		{name:'nonConformityId',index:'nonConformityId', width:55},
	   		{name:'name',index:'name', width:90},
	   		{name:'date_entered',index:'date_entered', width:100},
	   		{name:'date_modified',index:'date_modified', width:80/*, align:"right"*/},
	   		{name:'description',index:'description', width:120/*, align:"right"*/},		
	   		{name:'estadonc',index:'estadonc', width:80/*,align:"right"*/},		
	   		{name:'nombre_del_clientenc',index:'nombre_del_clientenc', width:150/*, sortable:false*/}		
	   	],
	   	rowNum:10,
	   	rowList:[10,20,30],
	   	pager: '#pager2',
	   	sortname: 'nonConformityId',
	    viewrecords: true,
	    sortorder: "desc",
	    caption:"No conformidades"
	});
	jQuery("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});
});