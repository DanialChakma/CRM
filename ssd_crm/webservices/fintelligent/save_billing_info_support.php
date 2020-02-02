<?php
/**
 * Created by PhpStorm. save_billing_info_support
 * User: Nazibul
 * Date: 11/29/2016
 * Time: 3:22 PM
 */

require_once "../lib/common.php";
require_once "fin_lib.php";

date_default_timezone_set("Asia/Dhaka");
$datainfo = (isset($_REQUEST)) ? $_REQUEST : exit;
$transaction_id = substr(uniqid("DB", true), 0, 16);
//$current_date_time = date("Y-m-d H:i:s");
$current_date = date("Y-m-d") . ' 00:00:00';
$err_msg = "";
$cn = connectDB();

$customer_email = $datainfo['email'];
$feedback = $datainfo['feedback'];

$cgwapitohit = "http://103.239.252.134/subscriptionservices_test/services/subscriber/SubscriberService_price.php?appid=test&apppass=test&cmdid=SHOW_SUBSCRIPTION_LIST&cmdparam=" . urlencode("WHERE msisdn='$customer_email' AND status <>'Deregistered'");

$fintapitohit = "http://fin.ssd-tech.com/dozecrm/CommonService.asmx/ParcelEntry";

$rescgw = file_get_contents($cgwapitohit);
$err_msg .= $rescgw . "|";
//$rescgw = "+OK
//1
//msisdn|parentID|SubscriptionGroupID|registrationDate|ServiceDuration|status|ChargingDueDate|NextRenewalDate|ServiceID|PackagePrice|DueAmount
//sameerequal@gmail.com|Internet|Internet10Mbps45GB|2016-02-24 09:36:57|30|RenewalFailed|2016-11-29 14:00:00|2016-11-29 12:25:44|ISP|1725.00|1600.00
//";

$cgwinfo = explode("\n", $rescgw);

if (strtolower(trim($cgwinfo[0])) == "+ok") {

    $info = explode("|", $cgwinfo[3]);
    $chargingduedate = $info[6];
    $nxtrenewdate = $info[7];
    $packageprice = $info[9];
    $dueammount = $info[10];

    $customer_qry = "SELECT * FROM contacts WHERE email='$customer_email' AND customer_type='prospect' OR customer_type='customer'";
    $rs_customer_qry = Sql_exec($cn, $customer_qry);
    $dt = Sql_fetch_array($rs_customer_qry);

    $contactid = $dt['id'];

    $iquery = "INSERT INTO monthly_bill_call_list (contact_id, email, charging_due_date, next_renewal_date, call_date, assign_to, update_count, STATUS, remarks, transaction_id, collection_date, package_price, due_amount)
VALUES ('$contactid', '$customer_email', '$chargingduedate', '$nxtrenewdate', '$current_date', 'account_admin', '1', '3', '$feedback', '$transaction_id', '$current_date', '$packageprice', '$dueammount')";

    $rs_iqry = Sql_exec($cn, $iquery);
    $mbcl_id = Sql_insert_id($cn);

    $iquery1 = "INSERT INTO monthly_bill_call_list_history (mbcl_id, contact_id, agent_name, feedback, call_date, call_status, call_outcome, payment_method, payment_date)
VALUES ('$mbcl_id', '$contactid', 'account_admin', '$feedback', '$current_date', 'Connected', '3', '3', '$current_date')";

    $rs_iqry = Sql_exec($cn, $iquery1);
    $last_id = Sql_insert_id($cn);

    $qry = "SELECT
                 CONCAT_WS(\" \", contact.first_name,contact.last_name ) AS customer_name,
                 contact.email AS email,
                 CONCAT_WS(\", \", NULLIF(contact.phone1, \"\"), NULLIF(contact.phone2, \"\")) AS phone,
                 contact.address1 AS address1,
                 contact.address2 AS address2,
                 convertion.real_ip_cost AS real_ip_cost,
                 convertion.collection_amount AS total_cost,
                 convertion.package AS package
                FROM contacts AS contact INNER JOIN customer_conversion AS convertion ON  contact.id = convertion.contact_id
                WHERE contact.id ='" . $contactid . "';";

    $rs = Sql_exec($cn, $qry);
    $dt = Sql_fetch_array($rs);

    $payment_collection_address = trim($dt['address2']);
    $net_connection_address = trim($dt['address1']);
    $customer_name = trim($dt['customer_name']);
    $phone_number = trim($dt['phone']);
    $package = trim($dt['package']);
    $email = trim($dt['email']);
    $real_ip_cost = floatval($dt['real_ip_cost']);
    $real_ip_cost = empty($real_ip_cost) ? 0.0 : $real_ip_cost;

    $total_cost = $dueammount + $real_ip_cost;
    $data = array(

        'CustID' => $contactid,
        'TransactionID' => $transaction_id,
        'PaymentType' => "MonthlyBill",
        'Name' => $customer_name,
        'Mobile' => $phone_number,
        'EmailID' => $email,
        'PaymentCollectionAddress' => $payment_collection_address,
        'Package' => $package,

        'OriginalCost' => 0,
        'RealIPCost' => $real_ip_cost,
        'OtherCost' => 0,

        'MonthlyBill' => $dueammount,
        'Months' => 1,
        'TotalCost' => $total_cost,

        'CollectDate' => $current_date,
        'CollectTime' => '',
        'CollectedCost' => 0,
        'ConnectionAddress' => $net_connection_address,
        'CollectDO' => '',
        'BillInserter' => 'Support Subscriber',
        'Remark' => '',
        'ReceiptNO' => '',
        'Agent' => 'account_admin'

    );

    $curl_response = curl_request($fintapitohit, "GET", $data);
    $xml = simplexml_load_string($curl_response);
    $res = (string)$xml[0];
    $res = strtolower($res);
    if ($res == "+ok") {
        echo "OK|Successful";
    } else {
        $err_msg .= $curl_response . "|";
        echo $err_msg;
    }


}