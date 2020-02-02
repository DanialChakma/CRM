<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 5/28/2017
 * Time: 5:40 PM
 */
require_once "../lib/common.php";
checkSession();
date_default_timezone_set('Asia/Dhaka');
$user_id = $_SESSION['user_id'];

$input_data = isset($_REQUEST['info']) ? $_REQUEST['info'] : exit;

$action_id = $input_data['action_id'];
$email_address = htmlentities($input_data['email_address']);
$first_name = htmlentities($input_data['first_name']);
$last_name = htmlentities($input_data['last_name']);
$phone = $input_data['phone'];
$mobile_no = $input_data['mobile_no'];
$connection_address = htmlentities($input_data['connection_address']);
$city = $input_data['city'];

$expiration =  date('Y-m-d', strtotime("+30 days"));
$srvid = $input_data['srvid'];
$package = $srvid;
$createdby = "DOZE_CRM_WEB_PANEL";

$alertsms = "1";
$lang = "English";
$enableuser = 0;
$manager = "crmwebservice";
$managerpass = "crm_doze";
$flag = "add";
$user = $input_data['user'];
$password = "123456";
$comment = "";
$cn = connectDB();

$ServiceID_qry = "SELECT * FROM internet_package WHERE CONCAT(TRIM(bandwidth),' ',TRIM(volume)) = '$srvid'";

$rs = Sql_exec($cn,$ServiceID_qry);
$dt = Sql_fetch_array($rs);
$srvid = trim($dt['radius_id']);

if( !$srvid ){
    // if not found in db table then set radius service id to default value 0
    $srvid = 0;
}

Sql_Free_Result($rs);


$history_count_qry = "select COUNT(id) AS 'cnt' where contact_id ='$action_id'";

$rs = Sql_exec($cn,$history_count_qry);
$dt = Sql_fetch_array($rs);
$history_entry_count = intval($dt['cnt']);
Sql_Free_Result($rs);

/* ZIP Code
 * Chittagong Head Office => 4100
 * Dhaka Head Office => 1100
 * Comilla => 3500
 * */

if( $city == "1" || $city == 1 ){
    $city = "Dhaka";
    $zip = "1100";
}else if( $city == "2" || $city == 2 ){
    $city = "Comilla";
    $zip = "3500";
}else if( $city == "2" || $city == 2 ){
    $city = "Comilla";
    $zip = "3500";
}else{
    $city = "NA";
    $zip = "NA";
}

$api_url = "http://103.218.27.138/radiusservices/user.php?";

$api_url .= "manager=" . urlencode(trim($manager)) . "&managerpass=" . urlencode(trim($managerpass))."&user=" . urlencode($user) . "&password=" . urlencode($password)."&firstname=" . urlencode($first_name) . "&lastname=" . urlencode($last_name)."&phone=" . urlencode($phone)."&mobile=".urlencode($mobile_no)."&address=" . urlencode($connection_address)."&city=".urlencode($city)."&zip=" . urlencode($zip)."&comment=".urlencode($comment)."&expiration=" . urlencode($expiration)."&srvid=".urlencode($srvid)."&createdby=".urlencode($createdby)."&email=".urlencode($email_address).
            "&alertemail=" . urlencode($alertsms)."&alertsms=".urlencode($alertsms)."&lang=".urlencode($lang)."&enableuser=".urlencode($enableuser)."&flag=".$flag;

//echo $api_url;

$response = file_get_contents_user_define($api_url);

$resultResponse = explode("|", $response);

if( trim($resultResponse[0]) == "SUCCESS" ){
    $uid = trim($email_address);
    $contact_qry = "update contacts set customer_type='closed',radius_user='$user',uid='$uid',promoted_to_closed= NOW(), update_date= NOW(),update_by='" . $user_id . "' where id='$action_id'";
    $conversion_history_qry = "";
    if( $history_entry_count > 0 ){
        $conversion_history_qry = "UPDATE customer_conversion SET package = '$package' WHERE contact_id = '$action_id';";
    }else{
        $conversion_history_qry = "INSERT INTO customer_conversion (contact_id,package, conversion_date, collection_date) VALUES ('$action_id','$package', now(), now());";
    }
    try {
        $res = Sql_exec($cn, $contact_qry);
        if( $conversion_history_qry != "" ){
            Sql_exec($cn, $conversion_history_qry);
        }
    } catch (Exception $e) {
        $is_error = 1;
    }
    echo json_encode(array("status"=>trim($resultResponse[0]),"msg"=>trim($resultResponse[1])));
}else{
    echo json_encode(array("status"=>trim($resultResponse[0]),"msg"=>trim($resultResponse[1])));
}

ClosedDBConnection($cn);