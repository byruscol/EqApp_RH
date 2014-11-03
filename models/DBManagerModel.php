<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');
*/
require_once $pluginPath . "/helpers/DBManager.php";
require_once $pluginPath . "/helpers/mailSender.php";
abstract class DBManagerModel extends DBManager{
        public $mail;
        function __construct() {
            parent::__construct();
            $this->mail = new mailSender();
        }
        
        public function sendAssignedMail($user, $id, $type){
            $assignedUserData = $this->getUserdata($user);
            $this->mail->PQRAssigned($assignedUserData->data->display_name
                                         , $type
                                         , $id
                                         , $assignedUserData->data->user_email
                                    );
        }
        
        function __destruct() {}
        
	abstract protected function getList($params = array());
	abstract protected function add();
	abstract protected function edit();
	abstract protected function del();
        abstract protected function detail();
}
?>
