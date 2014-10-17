<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');
*/
require_once $pluginPath . "/helpers/DBManager.php";
abstract class DBManagerModel extends DBManager{
		
	abstract protected function getList($params = array());
	abstract protected function add();
	abstract protected function edit();
	abstract protected function del();
        abstract protected function detail();
}
?>