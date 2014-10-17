<?php
session_start();
 
if(isset($_REQUEST['captcha']))
{
    //echo $_SESSION['captcha'];
    echo json_encode(array("msg" => (strtolower($_REQUEST['captcha']) == strtolower($_SESSION['captcha']))? true: false));
}
else
{
    echo false; // no code
}
?>

