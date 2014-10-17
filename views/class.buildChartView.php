<?php
class buildChartView extends Charts
{
    private $chartType;
    function __construct($v = "pie",$params = array(),$div= "",$view= "") {
            $this->chartType = $v;
            parent::__construct($this->chartType,$params,$div,$view);
    }
}
?>