<?php
/**
 * Created by PhpStorm.
 * User: Mazhar
 * Date: 3/10/2015
 * Time: 4:33 PM
 */

include_once "../lib/commonAPIFunction.php";
//include_once "../error/error.php";



$arrayInput = $_REQUEST;


$returnValue = array();
$returnValue['status'] = false;
$returnValue['msg'] = 'login failed';


if (!isset($arrayInput['uid']) || !isset($arrayInput['pass'])) {
    $returnValue['msg'] = 'User name and password are required';
} else {

    $arrayInput["userid"] = "Mamun";

    if (!strpos($arrayInput['uid'], '@')) {
        $arrayInput['uid'] = $arrayInput['uid'] . '@dhakagate.com';
    }

    if (checkIfPendingUser($arrayInput['uid'])) {
        $returnValue['msg'] = 'You are a Pending User!';
    } else {

        $APIindex = 1;

        $apiUrl = getAPIURL($APIindex, $arrayInput);
        $response = file_get_contents_user_define($apiUrl);

        //apiErrorHandling($response, $APIindex, $arrayInput, $apiUrl);
        addLogToDB('user login 1', $response . ' arrayInput ' . implode(' ', $arrayInput) . ' apiUrl:' . $apiUrl, 'check_Session');

        $data = explode("|", $response);
        if ($data[0] != '+OK') {
            $arrayInput['pass'] = sha1($salt . $arrayInput['pass']);

            $apiUrl = getAPIURL($APIindex, $arrayInput);
            $response = file_get_contents_user_define($apiUrl);

            $data = explode("|", $response);
        }

        //apiErrorHandling($response, $APIindex, $arrayInput, $apiUrl);
        addLogToDB('user login 2', $response . ' arrayInput ' . implode(' ', $arrayInput) . ' apiUrl:' . $apiUrl, 'check_Session');

        if ($data[0] == '+OK') {

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
                $returnResponse = getUserInfo($returnValue['read']['role'], $arrayInput['uid']);

                if ($returnResponse['status'] == true) {
                    $returnValue['read']['fund'] = $returnResponse['balance'];
                }
            }
            // session *******************
            $_SESSION['role'] = $returnValue['read']['role']; // role

            $_SESSION['uid_dhk'] = $returnValue['read']['uid']; // int user id
            $_SESSION['uid_cgw'] = $arrayInput['uid']; // cbps user
        }

//    $username = Sql_real_escape($cn, $arrayInput['username']);
//    $password = sha1($salt . Sql_real_escape($cn, $arrayInput['password']));
//
//
//    $query = "SELECT * FROM users WHERE username = '$username' AND `password` = '$password'";
//
//    $result = Sql_exec($cn, $query);
//    if ($result) {
//        $row = Sql_fetch_array($result);
//        if ($row) {
//            $returnValue['status'] = true;
//            $returnValue['msg'] = 'You are successfully logged in';
//
//            $returnValue['read'] = array();
//            $returnValue['read'][] = Sql_Result($row, 'id');
//        } else {
//            $returnValue['msg'] = 'no user found';
//        }
//    } else {
//        $returnValue['msg'] = 'login failed';
//    }
    }
}
echo json_encode($returnValue);


function getUserType()
{
    $returnValue = array('status' => false);

    // find role
    $query = "SELECT * FROM users WHERE username='" . $_REQUEST['uid'] . "' OR  username='" . $_REQUEST['uid'] . "@dhakagate.com'";
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

//    $arrayInput = array();
//
//    $arrayInput['condition'] = urlencode("WHERE uid='" . $_REQUEST['uid'] . "'");
//
//    $APIindex = 7;
//
//    $apiUrl = getAPIURL($APIindex, $arrayInput);
//    $response = file_get_contents_user_define($apiUrl);
//
//    $data = explode("\n", $response);
//
//    $returnValue = array('status' => false);
//    if ($data[0] == '+OK') {
//        $returnValue['status'] = true;
//
//        $result = explode("|", $data[2]);
//        $value = explode("|", $data[3]);
//
//        // find role
//        $query = "SELECT * FROM users WHERE username='" . $_REQUEST['uid'] . "'";
//        $cn = connectDB();
//
//        $resultQuery = Sql_exec($cn, $query);
//        $row = Sql_fetch_array($resultQuery);
//
//        $returnValue['uid'] = $row['id'];
//        $returnValue['SubscriberType'] = strtolower($row['role']);
//
//        $returnValue['name'] = $row['name'];
//        $indexOfAPIData = getIndexOfAPIData($result, 'FirstName');
//        $returnValue['FirstName'] = $value[$indexOfAPIData];
//
//        $indexOfAPIData = getIndexOfAPIData($result, 'LastName');
//        $returnValue['LastName'] = $value[$indexOfAPIData];
//    }
//    return $returnValue;
}


function checkIfPendingUser($username){
    $returnValue = false;
    $query = "SELECT * FROM users WHERE username='" . $username . "' AND role='PendingUser'" ;
    $cn = connectDB();

    $resultQuery = Sql_exec($cn, $query);
    $row = Sql_fetch_array($resultQuery);
    if ($row) {
        $returnValue = true;
    } else {
        $returnValue = false;
    }
    return $returnValue;
}
