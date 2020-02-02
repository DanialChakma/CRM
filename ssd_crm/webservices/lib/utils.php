<?php
//mysql return to json

error_reporting(0);
session_start();
function formatJSON($result)
{
    $str = "[";
    $numRows = 0;
    while ($row = mysql_fetch_array($result)) {
        if ($numRows > 0)
            $str = $str . ", ";
        $numRows++;
        $n = mysql_num_fields($result);
        for ($i = 0; $i < $n; $i++) {
            $fld = mysql_field_name($result, $i);
            $val = addslashes($row[$fld]);
            $val = str_replace("\t", "", $val);
            $val = str_replace("'", "\'", $val);
            $val = str_replace("\r\n", "", $val);

            if ($i == 0)
                $str = $str . "{\"$fld\":\"$val\"";
            else
                $str = $str . ", \"$fld\":\"$val\"";
        }
        $str = $str . "}\r\n";
    }

    $str = $str . "]";
    return $str;
}

//json to add query @mahfooz
function jsonDataToQueryString($data)
{

    $field_string = "";
    $value_string = "";
    foreach ($data as $key => $val) {

        $field_string .= $key . ',';
        $value_string .= "'" . mysql_real_escape_string($val) . "',";
    }

    return array('fields' => substr($field_string, 0, -1), 'values' => substr($value_string, 0, -1));
}

//json to edit query @mahfooz

function jsonEditQuery($data)
{
    $string = "";
    foreach ($data as $key => $val) {
        $string .= "{$key} = '" . mysql_real_escape_string($val) . "',";
    }
    return substr($string, 0, -1);
}

function pr($data)
{
    print_r($data);
}

/* ===========================
 * 'uid' 'role'
 * ===========================*/
function checkSession()
{
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
        die('no user info found');
    }
}


/* ====================================================================================
 * log checking Database
 *
 * @param $step_name user defined name
 * @param $step_response response coder want to save
 *
 * @param $step_id enum field has specific values
 * check_Session
 * send_flexi_load
 * send_flexi_load_file
 * send_flexi_load_api
 * scratch_card_recharge
 * merchant_api
 * add_reseller_fund
 *
 * @param $parent_id value of parent used for identifying call flow
 * ===================================================================================== */
function addLogToDB($step_name, $step_response, $step_id, $parent_id = 0)
{
    date_default_timezone_set("Asia/Dhaka");
    $create_date = date('Y-m-d H:i:s');
    $step_response = str_ireplace("'", '+', $step_response);
    $query = "INSERT INTO debug_log_monitor ( step_name, step_response, step_id, parent_id, create_date ) VALUES ( '$step_name', '$step_response', '$step_id', '$parent_id', '$create_date' ) ";

    $cn = connectDB();
    $result = Sql_exec_continue($cn, $query);

    if ($result) {
        return Sql_insert_id($cn);
    } else {
        return 0;
    }
}

/* ===============================================================================
 * gives role number
 * =============================================================================== */
function getLayoutId($role)
{
    if (strtolower($role) == 'admin') {
        return 7;
    } else if (strtolower($role) == 'partner') {
        return 6;
    } else if (strtolower($role) == 'dealer') {
        return 5;
    } else if (strtolower($role) == 'distributor') {
        return 4;
    } else if (strtolower($role) == 'retailer') {
        return 3;
    } else if (strtolower($role) == 'user') {
        return 2;
    } else {
        return 1;
    }
}


/* ===============================================================================
 * @param $roleArray array of role which can access function
 *
 * @return true for successful role else die with error code
 * =============================================================================== */
function getPrivilege($roleArray)
{
    if (isset($_SESSION['role'])) {

        foreach ($roleArray as $role) {
            if ((getLayoutId($_SESSION['role'])) == getLayoutId($role)) {

                return true;
            }
        }
    }
    die("your are not eligible for the function");
}


function getUserInfo($role, $uid)
{
    $apiUrl = '';

    $returnValue = array('status' => false);

    $role = strtolower($role);
    // Dealer Info
    if ($role == 'partner' || $role == 'dealer' || $role == 'distributor') {

        $APIindex = 2;

        $arrayInput['condition'] = urlencode("WHERE dealeruid = '" . $uid . "'");

        $apiUrl = getAPIURL($APIindex, $arrayInput);

    } else if ($role == 'admin' || $role == 'user' || $role == 'pendinguser' || $role == 'retailer') {

        $APIindex = 11;

        $arrayInput['condition'] = urlencode("WHERE uid = '" . $uid . "'");

        $apiUrl = getAPIURL($APIindex, $arrayInput);
    }
    if ($apiUrl != '') {
        $response = file_get_contents($apiUrl);

        // add Log To DB *********************
        $step_name = 'user balance';

        $step_response = $response . ' api_url: ' . $apiUrl;
        $step_id = 'check_Session';
        addLogToDB($step_name, $step_response, $step_id);
        // ***********************************

        $dataDealer = explode("\n", $response);
        if ($dataDealer[0] == '+OK') {

            $result = explode("|", $dataDealer[2]);
            $value = explode("|", $dataDealer[3]);

            $indexOfAPIData = getIndexOfAPIData($result, 'balance');
            $returnValue['balance'] = $value[$indexOfAPIData];

            // add Log To DB *********************
            $step_name = 'user balance 2';

            $step_response = $indexOfAPIData . ' ' . $value[$indexOfAPIData] . ' ' . $returnValue['balance'];
            $step_id = 'check_Session';
            addLogToDB($step_name, $step_response, $step_id);
            // ***********************************

            $returnValue['status'] = true;
        }
    }
    return $returnValue;
}

function getIndexOfAPIData($arrayStraw, $pin)
{
    $length = sizeof($arrayStraw);
    for ($i = 0; $i < $length; $i++) {
        if ($arrayStraw[$i] == $pin) {
            return $i;
        }
    }
    return -1;
}