<?php
/**
 * Created by PhpStorm.
 * User: Mazhar
 * Date: 3/10/2015
 * Time: 4:33 PM
 */

include_once "../lib/commonAPIFunction.php";

checkSession();

$returnValue = array();
$returnValue['status'] = false;
$returnValue['msg'] = 'login failed';


$returnValue['status'] = true;
$returnValue['msg'] = 'You are successfully logged in';


$returnValue['read'] = array();
$returnValue['read']['uid'] = 0;
$returnValue['read']['role'] = 'unknown';
$returnValue['read']['name'] = 'unknown';
$returnValue['read']['fund'] = 0;

$returnResponse = getUserType();

if ($returnResponse['status'] == true) {

    $returnValue['read']['role'] = $returnResponse['SubscriberType'];
    $returnValue['read']['layoutId'] = getLayoutId($returnResponse['SubscriberType']);

    $returnValue['read']['name'] = $returnResponse['name'];
    $returnValue['read']['uid'] = $returnResponse['uid'];
    $returnValue['read']['phone'] = $returnResponse['phone'];
    $returnValue['read']['email'] = $returnResponse['email'];
  /*  $returnResponse = getUserInfo($returnValue['read']['role'], $arrayInput['uid']);

    if ($returnResponse['status'] == true) {
        $returnValue['read']['fund'] = $returnResponse['balance'];
    }*/
}
echo json_encode($returnValue);


function getUserType()
{
    $returnValue = array('status' => false);

    // find role
    $query = "SELECT * FROM users WHERE id='" . $_SESSION['uid_dhk'] . "'";
    $cn = connectDB();

    $resultQuery = Sql_exec($cn, $query);
    $row = Sql_fetch_array($resultQuery);
    if ($row) {

        $returnValue['uid'] = $row['id'];
        $returnValue['SubscriberType'] = strtolower($row['role']);

        $returnValue['name'] = $row['name'];
        $returnValue['phone'] = $row['phone'];
        $returnValue['email'] = $row['email'];
        $returnValue['status'] = true;
    }
    return $returnValue;
}
