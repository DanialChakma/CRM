<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 9/17/2015
 * Time: 12:45 PM
 */

require_once "../lib/common.php";

checkSession();
$user_id = $_SESSION['user_id'];

$input_data = isset($_REQUEST['info']) ? $_REQUEST['info'] : exit;

$action_id = $input_data['action_id'];
$email_address = $input_data['email_address'];
$first_name = $input_data['first_name'];
$last_name = $input_data['last_name'];
$phone = $input_data['phone'];
$connection_address = $input_data['connection_address'];

$cn = connectDB();
$reslt_data = array();
$uid = '';
$doze_id = '';

$api_url = "http://localhost/subscriptionservices_test/services/subscriber/add_subscriber_info.php?";

$api_url .= "uid=" . urlencode(trim($email_address)) . "&status=Active&Pwd=123456&yy=2014&mm=06&dd=12&FirstName=" . urlencode($first_name) . "&LastName=" . urlencode($last_name) . "&ContactNo=" . urlencode($phone) . "&PermanentAddress=" . urlencode($connection_address) . "&EmailAddress=" . urlencode($email_address)."&PresentAddress1=".urlencode($connection_address);

$response = file_get_contents_user_define($api_url);

$resultResponse = explode("|", $response);

if ($resultResponse[0] == '+OK') {
    $api_url = "http://localhost/subscriptionservices_test/services/subscriber/SubscriberService.php?appid=test&apppass=test&cmdid=SHOW_SUB_LIST&cmdparam=where+uid='" . urlencode(trim($email_address)) . "'";
    $response = file_get_contents_user_define($api_url);
    $resultResponse2 = explode("\n", $response);

    if ($resultResponse2[0] == '+OK') {
        $result = explode("|", $resultResponse2[2]);
        $length = $resultResponse2[1];

        for ($i = 3; $i < ($length + 3); $i++) {
            $value = explode("|", $resultResponse2[$i]);

            $arrayResult = array();
            for ($j = 0; $j < sizeof($value); $j++) {
                $arrayResult[$result[$j]] = $value[$j];
            }
        }
    }
    //print_r($arrayResult); exit;

    $doze_id = 'DOZE' . $arrayResult['id'];
    $uid = $arrayResult['uid'];

} else if ($resultResponse[0] == 'FAILED') {
    $doze_id = 'old_user';
    $uid = trim($email_address);
}

$contact_qry = "update contacts set customer_type='closed',doze_id='$doze_id',uid='$uid',promoted_to_closed= NOW(), update_date= NOW(),update_by='" . $user_id . "' where id='$action_id'";

$reslt_data['qry'] = $contact_qry;

try {
    $res = Sql_exec($cn, $contact_qry);
} catch (Exception $e) {
    $is_error = 1;
}

$reslt_data['doze_id'] = $doze_id;
$reslt_data['uid'] = $uid;

echo json_encode($reslt_data);
