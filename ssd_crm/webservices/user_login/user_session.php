<?php
/**
 * Created by PhpStorm.
 * User: Mazhar
 * Date: 3/10/2015
 * Time: 4:33 PM
 */

include_once "../lib/common.php";

checkSession();

$return_data = array();



$data['user_id'] = $_SESSION['user_id'];
$data['user_name'] = $_SESSION['user_name'];
$data['first_name'] = $_SESSION['first_name'];
$data['last_name'] = $_SESSION['last_name'];
$data['user_email'] = $_SESSION['user_email'];
$data['working_schedule'] = $_SESSION['working_schedule'];
$data['user_address'] = $_SESSION['user_address'];
$data['user_phone'] = $_SESSION['user_phone'];
$data['user_role'] = $_SESSION['user_role'];
$data['layout_id'] = $_SESSION['layout_id'];


$return_data['status'] = true;
$return_data['message'] = "You have been logged in successfully.";
$return_data['read'] = $data;


echo json_encode($return_data);

