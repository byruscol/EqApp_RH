<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once "DBManager.php"; 
if(!isset($resource)){
	require_once "resources.php";
	$resource = new resources();
}
class Charts extends DBManager
{	
    private $table;	
    private $params;
    private $div;
    private $model;
    private $data;
    function __construct($type = "pie", $p, $d, $v) { 
            global $resource;
            $controller = $v;
            $this->type = $type;
            $this->params = $p;
            $this->div = $d;
            $this->loc = $resource;
            parent::__construct();
            require_once $this->pluginPath."/models/".$v."Model.php";
            $this->model = new $controller();                
            $this->data = $this->model->getChart($p);
            $this->chartBuilder();		
    }

    function __destruct() {
    }

    function chartBuilder(){
        global $resource;
        $chart="";
        $total = $this->data["total"];
        $dataChart = $this->data["data"];
        $dataCol = array();
        $isStacked = "false";

        if(array_key_exists('chartConfig', $this->params) &&
            array_key_exists('series', $this->params["chartConfig"]) &&
            array_key_exists('rows', $this->params["chartConfig"]) &&
            array_key_exists('data', $this->params["chartConfig"])){
            $series = array();
            $rows = array();
            $bulidChartData = array();

            $serie = $this->params["chartConfig"]["series"];
            $row = $this->params["chartConfig"]["rows"];
            $q = $this->params["chartConfig"]["data"];

            $dataRow = array();
            foreach($dataChart as $k => $v){

                if (!in_array($v->$serie, $series))
                    $series[] = $v->$serie;

                if (!in_array($v->$row, $rows))
                    $rows[] = $v->$row;

            }
            $cr = count($rows);
            $cs = count($series);

            for($i = 0; $i < $cr; $i++){
                for($j = 0; $j < $cs; $j++){
                    $bulidChartData[$rows[$i]][$series[$j]] = 0;
                }
            }

            foreach($dataChart as $k => $v){
                if($v->$serie && $v->$row)
                    $bulidChartData[$v->$row][$v->$serie] = $v->$q + 0;
            }
            array_unshift($series, "serie");

            $dataCol[] = $series;

            foreach($bulidChartData as $key => $value){
                $rowData = array($key);
                foreach($value as $k => $v){
                    $rowData[] = $v;
                }
                $dataCol[] = $rowData;
            }

            $isStacked = (array_key_exists("isStacked", $this->params["chartConfig"]) && $this->params["chartConfig"]["isStacked"])? "true" : "false";

        }
        else
        {
            if(array_key_exists('chartConfig', $this->params) &&
                array_key_exists('serieName', $this->params["chartConfig"]) &&
                array_key_exists('ValueName', $this->params["chartConfig"]) )
                $dataCol[] = array($this->loc->getWord($this->params["chartConfig"]["serieName"]),$this->loc->getWord($this->params["chartConfig"]["ValueName"]), array("role" => "annotation"));
            print_r($rowData);
            foreach($dataChart as $k => $v){

                $dataArray = array();
                foreach($v as $key => $value){

                    if(is_numeric($value)){
                        $value = $value + 0;
                    }
                    if(!empty($key))
                        $dataArray[] = $value;
                }

                switch ($this->type) {
                    case "bar":
                        $dataArray[] = '';
                        break;
                }

                $dataCol[] = $dataArray;                    
            }
        }
        //print_r($dataCol);
        header('Content-type: text/javascript');
        switch ($this->type) {
            case "bar":
                $dc = json_encode($dataCol);
                $chart = "
                    google.load('visualization', '1', {packages:['corechart']});
                    google.setOnLoadCallback(".$this->div.");
                    function ".$this->div."() {
                        var data = google.visualization.arrayToDataTable(".$dc.");

                        var options = {
                          title: '".$this->loc->getWord($this->params["title"])."',
                          hAxis: {
                            title: '',
                            format : '#%',
                            gridlines : {
                              count : 5,
                              color: 'white'
                            }
                          },
                          legend: { position: 'top', maxLines: 3 },
                            bar: { groupWidth: '75%' },
                            isStacked: ".$isStacked."
                        };

                        var chart = new google.visualization.ColumnChart(document.getElementById('".$this->div."'));";
                        if(array_key_exists('chartConfig', $this->params) && array_key_exists('listeners', $this->params["chartConfig"]) &&is_array($this->params["chartConfig"]["listeners"]) ){
                            $listeners = $this->params["chartConfig"]["listeners"];
                            $countListeners = count($listeners);
                            for($i = 0; $i < $countListeners; $i++){
                                $chart .= "
                                    google.visualization.events.addListener(chart, '".$listeners[$i]["type"]."', function(){
                                            ".$listeners[$i]["function"]."
                                        }
                                    });
                                    ";
                            }
                        }
                        $chart .= "chart.draw(data, options);
                    }
                    jQuery(document).ready(function ($) {
                        $(window).resize(function(){
                            ".$this->div."();
                        });
                    });
                    ";
                break;
            case "pie":
                array_unshift($dataCol, array("series","value"));
                $dc = json_encode($dataCol);
                $chart = "google.load('visualization', '1.0', {'packages':['corechart']});
                    google.setOnLoadCallback(".$this->div.");
                    function ".$this->div."() {
                      var data = google.visualization.arrayToDataTable(".$dc.");
                      var options = {'title':'".$this->loc->getWord($this->params["title"])."'};
                      var chart = new google.visualization.PieChart(document.getElementById('".$this->div."'));";
                      if(array_key_exists('chartConfig', $this->params) && array_key_exists('listeners', $this->params["chartConfig"]) && is_array($this->params["chartConfig"]["listeners"]) ){
                          $listeners = $this->params["chartConfig"]["listeners"];
                            $countListeners = count($listeners);
                            for($i = 0; $i < $countListeners; $i++){
                                $chart .= "
                                    google.visualization.events.addListener(chart, '".$listeners[$i]["type"]."', function(){
                                            ".$listeners[$i]["function"]."
                                        }
                                    });
                                    ";
                            }
                        }
                        $chart .= "chart.draw(data, options);
                    }

                    jQuery(document).ready(function ($) {
                        $(window).resize(function(){
                            ".$this->div."();
                        });
                    });
                ";
                break;
            case "geo":
                $dc = json_encode($dataCol);
                $chart ="google.load('visualization', '1', {'packages': ['geochart']});
                        google.setOnLoadCallback(".$this->div.");

                        function ".$this->div."() {
                            var data = google.visualization.arrayToDataTable([
                                ['City', '".$this->loc->getWord($this->params["subtitle"])."'],
                                ".$dc."
                            ]);

                            var options = {                                    
                                region: 'CO', 
                                displayMode: 'markers',
                                colorAxis: {colors: ['#e7711c', '#4374e0']} // orange to blue
                            };

                            var chart = new google.visualization.GeoChart(document.getElementById('".$this->div."'));
                            chart.draw(data, options);
                        };
                        jQuery(document).ready(function ($) {
                            $(window).resize(function(){
                                ".$this->div."();
                            });
                        });
                   ";
                break;

            default:
                break;
        }

        echo $chart;
    }
}
?>
