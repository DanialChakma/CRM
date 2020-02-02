<?php
/*
 *
Edited by Talemul Islam
*/
require_once "../lib/common.php";
checkSession();
$login_user_id = $_SESSION['user_id'];
$arrayInput = $_REQUEST;
$cn = connectDB();

$info = $_REQUEST['info'];
$user_id=mysql_escape_string(htmlspecialchars($info['user_id']));
$user_name = mysql_escape_string(htmlspecialchars($info['user_name']));
$user_password =  mysql_escape_string(htmlspecialchars($info['user_password']));
$first_name =  mysql_escape_string(htmlspecialchars($info['first_name']));
$last_name =  mysql_escape_string(htmlspecialchars($info['last_name']));
$user_email =  mysql_escape_string(htmlspecialchars($info['user_email']));
$working_schedule =  mysql_escape_string(htmlspecialchars($info['working_schedule']));
$user_address =  mysql_escape_string(htmlspecialchars($info['user_address']));
$user_phone =  mysql_escape_string(htmlspecialchars($info['user_phone']));
$user_alt_phone =  mysql_escape_string(htmlspecialchars($info['user_alt_phone']));
$user_role =  mysql_escape_string(htmlspecialchars($info['user_role']));
$user_status =  isset($info['user_status']) ? mysql_escape_string(htmlspecialchars($info['user_status'])):'0';



if(!empty($info['user_id'])){
    $pass_query="select user_password from user_info where user_id=$user_id";
    $pass_result=Sql_exec($cn, $pass_query);
    if (!$pass_result) {
        echo "err+" . $query . " in line " . __LINE__ . " of file" . __FILE__;
        exit;
    }
    $row=  Sql_fetch_array($pass_result);
    $pass=$row['user_password'];
    $user_password=  trim($user_password);
    if($pass!=$user_password)
    {
        $user_password = md5($user_password);
    }

    $query="update user_info set user_name='$user_name',user_password='$user_password',first_name='$first_name',last_name='$last_name',user_email='$user_email',working_schedule='$working_schedule',"
        . "user_address='$user_address',user_phone='$user_phone',user_alt_phone='$user_alt_phone',user_role='$user_role',user_status='$user_status', update_date= '".date('Y-m-d H:i:s') . "',update_by='".$login_user_id."' where user_id=$user_id";
    unset($_SESSION['user_id_update']);
}
else
{

    $user_password = md5($user_password);
    $query = "INSERT INTO user_info (user_name,user_password,first_name,last_name,user_email,working_schedule,user_address,user_phone,user_alt_phone,user_role,user_status, update_date,update_by) "
        . "VALUES ( '$user_name','$user_password','$first_name','$last_name','$user_email','$working_schedule','$user_address','$user_phone','$user_alt_phone','$user_role','0', '".date('Y-m-d H:i:s') . "','".$login_user_id."')";
}
try{
    $result = Sql_exec($cn, $query);
    echo json_encode(array("status"=>"yes","message"=>"Successful"));
}catch( Exception $e){
    echo json_encode(array("status"=>"no","message"=>"Failure"));

}
Sql_Free_Result($result);
ClosedDBConnection($cn);
?>

