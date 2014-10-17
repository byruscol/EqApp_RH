<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once "../../../helpers/Charts.php";
require_once "../../class.buildChartView.php";

header('Content-type: text/javascript');
/*$chartParams = array("title"=>"consolidatePStauts","subtitle"=>"puntoacopio", "queryId" => "PPieChart");
$viewPieChart = new buildChartView("pie",$chartParams,"PPieChart","PQRCustomerService");

$chartParams = array("title"=>"consolidateQStauts","subtitle"=>"puntoacopio", "queryId" => "QPieChart");
$viewPieChart = new buildChartView("pie",$chartParams,"QPieChart","PQRCustomerService");

$chartParams = array("title"=>"consolidateRStauts","subtitle"=>"puntoacopio", "queryId" => "RPieChart");
$viewPieChart = new buildChartView("pie",$chartParams,"RPieChart","PQRCustomerService");
*/
$chartParams = array("title"=>"consolidateBarSource","subtitle"=>"puntoacopio", "queryId" => "PQRBarSourceChart", "chartConfig" => array("serieName" => "Cantidad", "ValueName" => "Value"));
$viewPieChart = new buildChartView("bar",$chartParams,"PQRBarSourceChart","PQRCustomerService");

/*
$chartBarParams = array("title"=>"consolidatePQRStautsClasification","subtitle"=>"puntoacopio", "queryId" => "PQRBarCalsifStutsChart"
                        , "chartConfig" => array("series" => "status", "rows" => "classification", "data" => "Q"
                                                , "isStacked" => false*/
                                                /*, "listeners" => array(
                                                                    array("type"=>"select"
                                                                            ,"function" => "var selectedItem = chart.getSelection()[0];
                                                                                            
                                                                                            if (selectedItem && selectedItem.column != null && selectedItem.row != null) {
                                                                                                var serie = data.getColumnLabel(selectedItem.column);
                                                                                                var xlabel = data.getValue(selectedItem.row, 0);
                                                                                                var serieId;

                                                                                                var colModel = jQuery('#tasks').jqGrid('getGridParam','colModel');
                                                                                                for(i = 0; i < colModel.length; i++){
                                                                                                    if(colModel[i].name == 'status'){
                                                                                                        col = colModel[i];
                                                                                                        var selectOptions = col.editoptions['value'].split(';');

                                                                                                        for(var selOp in selectOptions){
                                                                                                            selOpArray = selectOptions[selOp].split(':');
                                                                                                            if(selOpArray[1] == serie){
                                                                                                                serieId = selOpArray[0];
                                                                                                                break;
                                                                                                            }
                                                                                                        }
                                                                                                        break;
                                                                                                    }
                                                                                                }
                                                                                                jQuery('#tasks').jqGrid('setGridParam',{search:true, postData:{\"filters\":'{\"groupOp\":\"AND\",\"rules\":[{\"field\":\"ExpiredStatus\",\"op\":\"eq\",\"data\":\"'+xlabel+'\"},{\"field\":\"status\",\"op\":\"eq\",\"data\":\"'+serieId+'\"}]}'}});     
                                                                                                jQuery('#tasks').trigger('reloadGrid');"
                                                                    )
                                                    )*/
                                          /*  )
                    );
$viewBarChart = new buildChartView("bar",$chartBarParams,"PQRBarCalsifStutsChart","PQRCustomerService");
*/
/*var str = '';
                                                                for(xx in selectedItem){
                                                                    str += xx + ' -> ' + selectedItem[xx] + '<br/>';
                                                                }
                                                                alert(str)*/
/*,"chartConfig" => array(
                            "listeners" => array(
                                                array("type"=>"select"
                                                    ,"function" => "var selectedItem = chart.getSelection()[0];
                                                           
                                                        if (selectedItem && selectedItem.row != null) {
                                                            var xlabel = data.getValue(selectedItem.row, 0);
                                                            var serieId;
                                                            var colModel = jQuery('#tasks').jqGrid('getGridParam','colModel');
                                                            for(i = 0; i < colModel.length; i++){
                                                                if(colModel[i].name == 'status'){
                                                                    col = colModel[i];
                                                                    var selectOptions = col.editoptions['value'].split(';');

                                                                    for(var selOp in selectOptions){
                                                                        selOpArray = selectOptions[selOp].split(':');
                                                                        if(selOpArray[1] == xlabel){
                                                                            serieId = selOpArray[0];
                                                                            break;
                                                                        }
                                                                    }
                                                                    break;
                                                                }
                                                            }
                                                            jQuery('#tasks').jqGrid('setGridParam',{search:true, postData:{\"filters\":'{\"groupOp\":\"AND\",\"rules\":[{\"field\":\"status\",\"op\":\"eq\",\"data\":\"'+serieId+'\"}]}'}});     
                                                            jQuery('#tasks').trigger('reloadGrid');"
                                                )
                                        )
                                )*/
?>
