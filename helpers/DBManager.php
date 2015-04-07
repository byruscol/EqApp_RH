<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

require_once dirname(__FILE__)."/../pluginConfig.php";
require_once(getPahtFile('wp-load.php') );

abstract class DBManager{
    public $conn;
    public $pluginPrefix;
    public $wpPrefix;
    public $pluginPath;
    public $currentUser;
    public $currentIntegrante;
    public $isRhAdmin;
    protected $pluginURL;
    protected $query;
    protected $DBOper = array("table" => "", "data" => array(), "filter" => array());
    protected $totalRows;
    protected $queryType;
    protected $LastId;
    protected $result;
    protected $gbd;
    private $target_path;
    
    function __construct() {
            global $wpdb;
            global $pluginURL;
            global $pluginPath;
            global $prefixPlugin;
            global $current_user;
            $this->conn = $wpdb;
            $this->pluginURL = $pluginURL;
            $this->pluginPath = $pluginPath;
            $this->wpPrefix = $this->conn->prefix;
            $this->pluginPrefix = $this->wpPrefix;
            if(!empty($prefixPlugin)) $this->pluginPrefix .= $prefixPlugin;
            $this->target_path = $this->pluginPath."/files/";
            $this->currentUser = $current_user;

            $query = "SELECT integranteId FROM ".$this->pluginPrefix."integrantesUsuarios i WHERE i.ID = ".$this->currentUser->ID;
            $result = $this->get($query,"var");
            $this->currentIntegrante = $result["data"];
            $this->isRhAdmin = ( in_array( "administrator", $this->currentUser->roles ) 
                                 || in_array( "editor", $this->currentUser->roles ))? true : false;
                    
            
            $this->gbd = new PDO('mysql:host='.$this->conn->dbhost.';dbname='.$this->conn->dbname, $this->conn->dbuser, $this->conn->dbpassword);
    }

    function __destruct() {}

    public function getDataGrid($query = "SELECT 1 FROM dual", $start = null, $limit = null, $colSort = null, $sortDirection = null)
    {
            $this->queryType = (empty($this->queryType))? "rows" : $this->queryType;
            $queryBuild = $query;

            if($colSort != null)
                    $queryBuild .= " ORDER BY " . $colSort;

            if($sortDirection != null)
                    $queryBuild .= " " . $sortDirection;

            if($start != null && $limit != null)
                    $queryBuild .= " LIMIT " . $start . " , " . $limit;
//echo $queryBuild;
            return $this->get($queryBuild, $this->queryType);
    }

    protected function get($query, $type)
    {
            $this->query = $query;
            $this->queryType = $type;
            $this->execute();

            $array = array("totalRows" => $this->totalRows, "data" => $this->result);
            return $array;
    }

    protected function getTotalRows() {
            $this->totalRows = $this->conn->get_var( "SELECT FOUND_ROWS() AS `found_rows`;" );
    }

    protected function standardQuery()
    {
            $q = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $this->query);
            $queryLen = strlen($q);
            if(substr($q, $queryLen - 1, 1) != ";")
                    $q = $q . ";";

            if(substr_count($q, "SELECT") > 0)
            {
                    $selectPos = stripos ( $q , "SELECT " ) + 6;
                    $q = "SELECT SQL_CALC_FOUND_ROWS " . substr ( $q , $selectPos, strlen($q));
            }

            $this->query = $q;
    }

    protected function executeQuery() {
            $this->standardQuery();
//echo $this->query;
            switch($this->queryType)
            {
                    case "var": $this->result = $this->conn->get_var( $this->query ); break;
                    case "row": $this->result = $this->conn->get_row($this->query, OBJECT); break;
                    case "rows":$this->result = $this->conn->get_results($this->query, OBJECT); break;
            }

            $this->getTotalRows();
    }

    protected function execute() {

        try {
               //    echo $this->query;
                switch($this->queryType)
                {
                    case "add": $this->result = $this->conn->insert( $this->DBOper["table"], $this->DBOper["data"]); $this->LastId = $this->conn->insert_id;break;
                    case "edit": $this->result = $this->conn->update( $this->DBOper["table"], $this->DBOper["data"], $this->DBOper["filter"]); break;
                    case "del": $this->result = $this->conn->delete( $this->DBOper["table"], $this->DBOper["filter"]); break;
                    default: $this->executeQuery();
                }
                
                $queryFail = $this->conn->print_error();
                if(!empty($queryFail))
                    echo $queryFail . " - SQL:[".$this->conn->last_query."]";

                //echo $this->conn->last_query;
                $this->queryType = "";
            }
            catch (Exception $e)
            {
                $this->result = "Error: ".$e->getMessage();
            }
    }

    private function setFilter($field, $opString, $data){
        $op = "";
        switch($opString){
            case 'eq':  $op = " = '{data}'"; break;
            case 'ne':  $op = " <> '{data}'"; break;
            case 'lt':  $op = " < '{data}'"; break;
            case 'le':  $op = " <= '{data}'"; break;
            case 'gt':  $op = " > '{data}'"; break;
            case 'ge':  $op = " >= '{data}'"; break;
            case 'bw':  $op = " LIKE '%{data}'"; break;
            case 'bn':  $op = " NOT LIKE '%{data}'"; break;
            case 'in':  $op = " LIKE '%{data}%'"; break;
            case 'ni':  $op = " NOT LIKE '%{data}%'"; break;
            case 'ew':  $op = " LIKE '{data}%'"; break;
            case 'en':  $op = " NOT LIKE '{data}%'"; break;
            case 'cn':  $op = " LIKE '%{data}%'"; break;
            case 'nc':  $op = " NOT LIKE '%{data}%'"; break;
            case 'nu':  $op = " IS NULL"; break;
            case 'nn':  $op = " IS NOT NULL"; break;
            default: $op = "="; break;
        }
        return $field. (str_replace("{data}", $data, $op));
    }

    protected function buildWhere($data){
        $where = array();

        $LogicalOperator = $data->groupOp;
        $filters = $data->rules;

        if (is_array( $filters )){
            $countFilters = count($filters);
            for($i = 0; $i < $countFilters; $i++){
                $where[] = " ". $this->setFilter($filters[$i]->field, $filters[$i]->op, $filters[$i]->data);
            }
        }

        return implode($LogicalOperator, $where);
    }

    protected function addRecord($entity, $newRecord, $auditData){
        if ( ! is_array( $newRecord ) || ! is_array( $auditData ))
            return false;

        $insert = false;
        $addData = $auditData;
        //echo $entity["tableName"]."<br>";
        foreach($entity["atributes"] as $key => $value){
            if((!array_key_exists("autoIncrement", $value) || !$value["autoIncrement"])
                && !array_key_exists($key, $addData)
                && (!array_key_exists("isTableCol", $value) || $value["isTableCol"])){
                $addData[$key] = empty($newRecord[$key])? null:$newRecord[$key];
                $insert = true;
            }
        }

        if($insert){
            $this->queryType = "add";
            $this->DBOper["table"] = $entity["tableName"];
            $this->DBOper["data"]  = $addData;

            $this->execute();
        }
    }

    private function getCurrentRecord($entity, $filters){
        $cols = array();
        $where = array();
        $ws = array();
        $PK = array();

        $query = "SELECT {COLS} from ".$entity["tableName"]." WHERE {WHERE}";

        foreach($entity["atributes"] as $key => $value){

            if((!array_key_exists("isTableCol", $value) || $value["isTableCol"])
               && (!array_key_exists("autoIncrement", $value) || !$value["autoIncrement"]))
            $cols[] = $key;

            if(array_key_exists($key, $filters))
                $where[$key] = $filters[$key];
        }

        foreach($where as $key => $value){
            $ws[] = $key ." = ". $value;
        }

        $query = str_replace("{COLS}", (implode(",", $cols)), $query);
        $query = str_replace("{WHERE}", (implode(" AND ", $ws)), $query);

        $this->queryType = "row";
        $currentRecord = $this->getDataGrid($query);

        return array("currentRecord" => $currentRecord, "where" => $where);
    }

    protected function eliminateRecord($entity, $filters, $validate = null){
        $edit = true;
        $PK = array();
        $currentRecord = $this->getCurrentRecord($entity, $filters);

        if(is_array( $validate ) && array_key_exists("columnValidateEdit", $validate))
        {
            if($currentRecord["currentRecord"]["data"]->$validate["columnValidateEdit"] == $this->currentUser->ID){
                $edit = true;
            }
            else{
                $edit = false;
            }
        }

        if($edit){
            foreach($entity["atributes"] as $key => $value){

                if(array_key_exists("PK", $value))
                    $PK[] = $filters[$key];
            }
            $pkId = implode(",", $PK);

            foreach($currentRecord["currentRecord"]["data"] as $key => $value){
                $this->queryType = "add";
                $this->DBOper["table"] = $this->pluginPrefix."audit";
                $this->DBOper["data"] = array( 
                                            "table" => $entity["tableName"]
                                            ,"column" => $key
                                            ,"data" => stripslashes($value)
                                            ,"action" => "eliminate "
                                            ,"date" => date("Y-m-d H:i:s",time())
                                            ,"user" => $this->currentUser->user_login
                                            ,"PK" => $pkId
                                         );
                $this->execute();
            }

            $this->queryType = "del";
            $this->DBOper = array();
            $this->DBOper["table"] = $entity["tableName"];
            $this->DBOper["filter"] = $filters;
            $this->execute();
        }
    } 
    
    protected function delRecord($entity, $filters, $validate = null){
        $edit = true;
        $PK = array();
        $currentRecord = $this->getCurrentRecord($entity, $filters);

        if(is_array( $validate ) && array_key_exists("columnValidateEdit", $validate))
        {
            if($currentRecord["currentRecord"]["data"]->$validate["columnValidateEdit"] == $this->currentUser->ID){
                $edit = true;
            }
            else{
                $edit = false;
            }
        }

        if($edit){
            foreach($entity["atributes"] as $key => $value){

                if(array_key_exists("PK", $value))
                    $PK[] = $filters[$key];
            }
            $pkId = implode(",", $PK);

            foreach($currentRecord["currentRecord"]["data"] as $key => $value){
                $this->queryType = "add";
                $this->DBOper["table"] = $this->pluginPrefix."audit";
                $this->DBOper["data"] = array( 
                                            "table" => $entity["tableName"]
                                            ,"column" => $key
                                            ,"data" => stripslashes($value)
                                            ,"action" => "del"
                                            ,"date" => date("Y-m-d H:i:s",time())
                                            ,"user" => $this->currentUser->user_login
                                            ,"PK" => $pkId
                                         );

                $this->execute();
            }

            $this->queryType = "edit";
            $this->DBOper["data"]  = array("deleted" => 1);
            $this->DBOper["table"] = $entity["tableName"];
            $this->DBOper["filter"] = $filters;
            $this->execute();
        }
    }

    protected function updateRecord($entity, $newRecord, $filters, $validate = null){
        if ( ! is_array( $newRecord ) || ! is_array( $filters ))
            return false;
        $edit = true;
        $updateData = array();
        $auditData = array();
        $PK = array();

        $currentRecord = $this->getCurrentRecord($entity, $filters);

        if(is_array( $validate ) && array_key_exists("columnValidateEdit", $validate))
        {
            if($currentRecord["currentRecord"]["data"]->$validate["columnValidateEdit"] == $this->currentUser->ID){
                $edit = true;
            }
            else{
                $edit = false;
            }
        }
        
        if($edit){
            foreach($entity["atributes"] as $key => $value){

                if(array_key_exists("PK", $value))
                    $PK[] = $newRecord[$key];

                if(stripslashes($newRecord[$key]) != $currentRecord["currentRecord"]["data"]->$key
                   && (!array_key_exists("isTableCol", $value) || $value["isTableCol"])
                   && (!array_key_exists("autoIncrement", $value) || !$value["autoIncrement"])
                   && (!array_key_exists("update", $value) || $value["update"]) ){
                    $updateData[$key] = stripslashes($newRecord[$key]);
                    $auditData[] = array( 
                                       "table" => $entity["tableName"]
                                       ,"column" => $key
                                       ,"data" => stripslashes($currentRecord["currentRecord"]["data"]->$key)
                                       ,"action" => "edit"
                                       ,"date" => date("Y-m-d H:i:s",time())
                                       ,"user" => $this->currentUser->user_login
                                    );
                }
            }
            if(count($updateData) > 0)
            {
                $pkId = implode(",", $PK);
                foreach($auditData as $key => $value){
                    $this->queryType = "add";
                    $this->DBOper["table"] = $this->pluginPrefix."audit";
                    $this->DBOper["data"] = $value;
                    $this->DBOper["data"]["PK"] = $pkId;

                    $this->execute();
                }
                               
                $this->queryType = "edit";
                $this->DBOper["table"] = $entity["tableName"];
                $this->DBOper["filter"] = $currentRecord["where"];
                $this->DBOper["data"]  = $updateData;
                
                $this->execute();
                return $updateData;
            }
        }
    }

    protected function uploadFile($fileName,$ext,$file){
        if(file_exists($file)){
            if (!copy($file, $this->target_path.$fileName.".".$ext)) {
                echo "Error al copiar $archivo...\n";
            }
            unlink($file);
        }
    }
        
    public function rendererFile($fileId, $return = false){
        try {
            $sql = "SELECT `name`, `ext`, `mime`, `size`
                            FROM `".$this->pluginPrefix."files` f 
                            WHERE f.fileId = ".$fileId;
            $q = $this->gbd->prepare($sql);
            $q->execute();

            $q->bindColumn(1, $name);
            $q->bindColumn(2, $ext);
            $q->bindColumn(3, $mime);
            $q->bindColumn(4, $size);

            while($q->fetch())
            {
                $fileDownload = $this->target_path.$fileId.".".$ext;
                
                if(file_exists($fileDownload)){
                    $fdata = file_get_contents($fileDownload);
                    if($return)
                        return array("mime" => $mime, "data" => $fdata);
                    else {
                       header("Content-Type: ". $mime);
                        header("Content-Length: ". $size);
                        header("Content-Disposition: attachment; filename=". $name);

                        echo $fdata; 
                    }
                }
                else
                    echo "File not found";
            }
        } catch (PDOException $e) {
            print "¡Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
}
?>
