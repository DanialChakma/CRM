<?php
    /**
     * Created by PhpStorm.
     * User: Talemul
     * Date: 1/19/2016
     * Time: 3:16 PM
     */

    require_once "../lib/common.php";
    checkSession();
    $login_user_id = $_SESSION['user_id'];
    $arrayInput = $_REQUEST;
    $cn = connectDB();
    $info = $_REQUEST['info'];
    $user_id = mysql_escape_string(htmlspecialchars($info['user_id']));
    /*$user_name = mysql_escape_string(htmlspecialchars($info['user_name']));
    $user_password =  mysql_escape_string(htmlspecialchars($info['user_password']));
    $first_name =  mysql_escape_string(htmlspecialchars($info['first_name']));
    $last_name =  mysql_escape_string(htmlspecialchars($info['last_name']));
    $user_email =  mysql_escape_string(htmlspecialchars($info['user_email']));
    $working_schedule =  mysql_escape_string(htmlspecialchars($info['working_schedule']));
    $user_address =  mysql_escape_string(htmlspecialchars($info['user_address']));
    $user_phone =  mysql_escape_string(htmlspecialchars($info['user_phone']));
    $user_alt_phone =  mysql_escape_string(htmlspecialchars($info['user_alt_phone']));
    $user_role =  mysql_escape_string(htmlspecialchars($info['user_role']));*/
    $user_status = 1;//mysql_escape_string(htmlspecialchars($info['user_status']));


    if (!empty($info['user_id'])) {
        $query = "update user_info set user_status='$user_status', update_date= '" . date('Y-m-d H:i:s') . "',update_by='" . $login_user_id . "' where user_id=$user_id";

    }

    try {
        $result = Sql_exec($cn, $query);
        echo json_encode(array("status" => "yes", "message" => "Successful"));
    } catch (Exception $e) {
        echo json_encode(array("status" => "no", "message" => "Failure"));

    }
    Sql_Free_Result($result);
    ClosedDBConnection($cn);
?>

