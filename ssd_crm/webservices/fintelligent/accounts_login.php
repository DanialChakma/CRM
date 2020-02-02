<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 3/23/2017
 * Time: 5:50 PM
 */


require_once "../lib/common.php";
$username = (isset($_REQUEST['username'])) ? $_REQUEST['username'] :exit;
$password = (isset($_REQUEST['password'])) ? $_REQUEST['password'] :exit;

//echo json_encode($_REQUEST);
$cn = connectDB();
$username = mysql_real_escape_string(trim($username));
$password = mysql_real_escape_string(trim($password));
$md5_password = md5($password);
$qry = "SELECT 	COUNT(*) AS 'row_count',user_name,TRIM(CONCAT_WS(' ',first_name,last_name)) AS 'user_full_name'
        FROM `user_info` WHERE `user_name`='$username' AND `user_password`='$md5_password' AND `user_role`= 'account_agent' AND `user_status` = 0;";

$rs = Sql_exec($cn,$qry);
$dt = Sql_fetch_array($rs);
$row_count = intval($dt['row_count']);
if( $row_count > 0 ){
    echo json_encode( array( "code"=>1,"user_name"=>$dt['user_name'],
                            "usr_full_name" => $dt['user_full_name'],
                            "msg" => "Login Successful." )
                    );
}else{
    echo json_encode(array("code"=>0,"msg"=>"Login Failed!"));
}
Sql_Free_Result($rs);
ClosedDBConnection($cn);