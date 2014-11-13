<?php
class buildViewForm extends Form
{
	private $gridType;
	function __construct($v = "basic", $params = array(), $t = "") {
            parent::__construct( $params, $v, $t);
	}
}
?>