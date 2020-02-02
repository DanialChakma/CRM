<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 9/5/2016
 * Time: 3:11 PM
 */
require_once "../lib/common.php";
$input_data = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
$response_data = array();
$response_data['status'] = false;

$cn = connectDB();
//print_r($input_data); exit;

$select_qry = "SELECT * FROM asde_entry_table WHERE customer_email = '" . $input_data['email_address'] . "';";
$result = Sql_exec($cn, $select_qry);
$numrow = Sql_Num_Rows($result);
$dt = Sql_fetch_array($result);

if ($numrow > 0) {

    $response_data['status'] = true;
    $response_data['mes'] = 'Customer is recognised. CGW registration date is ' . $dt['cgw_reg_date'];

} else {

    $api_url = "http://103.239.252.134/subscriptionservices_test/services/subscriber/SubscriberService.php?appid=test&apppass=test&cmdid=SHOW_SUBSCRIPTION_LIST&cmdparam=";
    $api_url .= urlencode("WHERE msisdn='" . $input_data['email_address'] . "' AND ServiceID='ISP'");

    $response = file_get_contents_user_define($api_url);
    $resultResponse2 = explode("\n", $response);
    $response_api = array();

    //$response_data['api_response'] = $response;

    if ($resultResponse2[0] == '+OK') {
        $length = $resultResponse2[1];
        $result = explode("|", $resultResponse2[2]);
        $value = explode("|", $resultResponse2[3]);
//    print_r($result);
//    print_r($value);
        $response_api['msisdn'] = $value[0];
        $response_api['parentID'] = $value[1];
        $response_api['SubscriptionGroupID'] = $value[2];
        $response_api['registrationDate'] = $value[3];
        $response_api['ServiceDuration'] = $value[4];
        $response_api['status'] = $value[5];
        $response_api['ChargingDueDate'] = $value[6];
        $response_api['NextRenewalDate'] = $value[7];
        $response_api['ServiceID'] = $value[8];

        //print_r($response_api);

        $insert_qry = "INSERT INTO asde_entry_table (contact_id, customer_email, cgw_reg_date, cgw_service_id, cgw_status, agent_id, agent_name) VALUES ('" . $input_data['contact_id'] . "','" . $input_data['email_address'] . "','" . $response_api['registrationDate'] . "','" . $response_api['ServiceID'] . "','" . $response_api['status'] . "','" . $input_data['user_id'] . "','" . $input_data['user_name'] . "');";
        //echo $insert_qry . "\n";
        try {
            //print_r($cn);
            $res = Sql_exec($cn, $insert_qry);
            $last_id = Sql_insert_id($cn);
            //echo "....".$last_id; exit;
            if ($last_id > 0) {
                $response_data['status'] = true;
                $response_data['mes'] = 'Customer is recognised. CGW registration date is ' . $response_api['registrationDate'];
            } else {
                $response_data['status'] = false;
                $response_data['mes'] = 'Failed on insert data';
            }

        } catch (Exception $e) {
            $response_data['status'] = false;
            $response_data['mes'] = 'Failed on database';
        }

    } else if ($resultResponse2[0] == 'FAILED') {
        $response_data['status'] = false;
        $response_data['mes'] = 'CGW connection failed';

    } else if ($resultResponse2[0] == '-ERR|11|No data found') {
        $response_data['status'] = false;
        $response_data['mes'] = 'This customer is not registered in CGW';
    }
}

echo json_encode($response_data);
