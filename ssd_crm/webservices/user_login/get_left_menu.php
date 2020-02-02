<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 8/24/2015
 * Time: 11:24 AM
 */

require_once "../../../WebFramework/CMSWebService/config.php";
session_start();
$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;


    if( strtolower($_SESSION['user_role'])== strtolower('admin') ){
        $cid=6;
    }elseif( strtolower($_SESSION['user_role'])==strtolower('Retail') ){
        $cid=6;
    }elseif( strtolower($_SESSION['user_role'])==strtolower('Channel') ){
        $cid=47;
    }elseif( strtolower($_SESSION['user_role'])==strtolower('Corporate') ){
        $cid=48;
    }elseif( strtolower($_SESSION['user_role']) == strtolower('account_agent') ){
      //  $cid=47;
    }elseif( strtolower($_SESSION['user_role']) == strtolower('account_admin') ){
       // $cid=48;
    }
$query = "SELECT details FROM content WHERE cid='$cid'";
$result = mysql_query($query) or die(mysql_error());

while($row = mysql_fetch_array($result))
{
    echo $row['details'];
}
exit;