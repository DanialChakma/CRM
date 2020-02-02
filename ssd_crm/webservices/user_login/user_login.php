<?php
/**
 * Created by PhpStorm.
 * User: Mazhar
 * Date: 3/10/2015
 * Time: 4:33 PM
 * Edited:Talemul Islam
 */

include_once "../lib/common.php";

$array_input = $_REQUEST;
$return_data = array();
$return_data['status'] = false;
$return_data['message'] = 'login failed';
$cn = connectDB();

if (!isset($array_input['user_name']) || !isset($array_input['user_password'])) {
    $return_data['message'] = 'User name and password are required';
} else {
    $query = "SELECT 	user_id,user_name, first_name, last_name, 	user_email, user_phone,  user_role,working_schedule,user_address,user_alt_phone, 	user_status, created_date	FROM 	 user_info where  user_name='" . $array_input['user_name'] . "' and user_password ='" . md5($array_input['user_password']) . "'  and user_status=0";
    $result = Sql_exec($cn, $query);

    $row = Sql_fetch_array($result);
    if (!empty($row)) {
        $data = array();
        $data['user_id'] = $row['user_id'];
        $data['user_name'] = $row['user_name'];
        $data['first_name'] = $row['first_name'];
        $data['last_name'] = $row['last_name'];
        $data['user_email'] = $row['user_email'];
        $data['working_schedule'] = $row['working_schedule'];
        $data['user_address'] = $row['user_address'];
        $data['user_phone'] = $row['user_phone'];
        $data['user_role'] = $row['user_role'];

        $data['layout_id'] = getLayoutId($data['user_role']);

        $return_data['status'] = true;
        $return_data['message'] = "You have been logged in successfully.";
        $return_data['read'] = $data;

        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['user_name'] = $data['user_name'];
        $_SESSION['first_name'] = $data['first_name'];
        $_SESSION['last_name'] = $data['last_name'];
        $_SESSION['user_email'] = $data['user_email'];
        $_SESSION['working_schedule'] = $data['working_schedule'];
        $_SESSION['user_address'] = $data['user_address'];
        $_SESSION['user_phone'] = $data['user_phone'];
        $_SESSION['user_role'] = $data['user_role'];
        $_SESSION['layout_id'] = $data['layout_id'];
    } else {
        $return_data = array('status' => false, 'message' => "Username and password does not match.");
    }
}
echo json_encode($return_data);

