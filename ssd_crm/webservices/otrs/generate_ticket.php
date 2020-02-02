<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 9/9/2015
 * Time: 10:15 AM
 */

require_once "../lib/common.php";
require_once "send_mail.php";

checkSession();
$user_id = $_SESSION['user_id'];

$data_input = isset($_REQUEST['info']) ? $_REQUEST['info'] : exit;

//print_r($data_input); exit;
$result_data = array();
$id = $data_input['contact_id'];
$cn = connectDB();
$datainfo = array();
$total_msg_body = '';

//echo $id . '  ';
/**
 * this block is for receive data from database____________________________________________________________________
 **/

//    $qry = 'SELECT contact_type, do_area FROM contacts WHERE id=' . $id;
//    $res = Sql_exec($cn, $qry);
//    while ($dt = Sql_fetch_array(($res))) {
//        $datainfo['client_type'] = $dt['contact_type'];
//        $datainfo['area'] = $dt['do_area'];
//    }(SELECT client_type FROM customer_conversion WHERE customer_conversion.`contact_id`=contacts.`id` LIMIT 1) AS ''
$qry = 'SELECT contact_type,do_area,first_name, last_name, address1, address2, phone1,phone2, email FROM contacts WHERE id=' . $id;
$res = Sql_exec($cn, $qry);
while ($dt = Sql_fetch_array(($res))) {
    $datainfo['client_type'] = $dt['contact_type'];
    $datainfo['area'] = $dt['do_area'];
    $datainfo['name'] = $dt['first_name'] . ' ' . $dt['last_name'];
    $datainfo['address'] = $dt['address1'];
    $datainfo['collection_address'] = $dt['address2'];
    $datainfo['contact_number'] = $dt['phone1'];
    if(!empty($dt['phone2'])){
        $datainfo['contact_number'] .= "," .$dt['phone2'];
    }
    $datainfo['additional_contact_number'] = $dt['phone2'];
    $datainfo['email'] = $dt['email'];
}
$qry = 'SELECT assignment_date, collection_amount, package,conversion_note FROM customer_conversion WHERE contact_id=' . $id;
$res = Sql_exec($cn, $qry);
while ($dt = Sql_fetch_array(($res))) {
    $datainfo['assignment_date'] = $dt['assignment_date'];
    $datainfo['collection_amount'] = $dt['collection_amount'];
    $datainfo['package'] = $dt['package'];
    $datainfo['conversion_note'] = $dt['conversion_note'];
}
$qry = 'SELECT receipt_number FROM payments WHERE contact_id=' . $id;
$res = Sql_exec($cn, $qry);
while ($dt = Sql_fetch_array(($res))) {
    $datainfo['receipt_number'] = $dt['receipt_number'];
}


/**
 * this block is for receive orts ticket from api_____________________________________________________________________
 **/

$output = array();

$url = 'http://103.239.252.132/otrs/doze_ticket_nazibul.pl?';
$param = 'do=' . $datainfo['area'] . '&name=' . $datainfo['name'] . '&collection=' . $datainfo['collection_address'] . '&connection=' . $datainfo['address'] . '&contact=' . $datainfo['contact_number'] .$datainfo['additional_contact_number'] . '&email=' . $datainfo['email'] . '&type=' . $datainfo['client_type'] . '&time=' . $datainfo['assignment_date'] . '&package=' . $datainfo['package'] . '&receipt=' . $datainfo['receipt_number'] . '&install=' . $datainfo['assignment_date'] .  '&conversion_note=' . $datainfo['conversion_note'] . '';

/*  $url = 'http://103.239.252.132/otrs/doze_ticket_nazibul.pl?';
  $param = 'do=' . $datainfo['area'] . '&name=' . $datainfo['name'] . '&collection=' . $datainfo['collection_address'] . '&connection=' . $datainfo['address'] . '&contact=' . $datainfo['contact_number'] . '&contact_additional=' . $datainfo['contact_additional'] . '&email=' . $datainfo['email'] . '&type=' . $datainfo['client_type'] . '&time=' . $datainfo['assignment_date'] . '&package=' . $datainfo['package'] . '&receipt=' . $datainfo['receipt_number'] . '&install=' . $datainfo['due_date'] . '&conversion_note=' . $datainfo['conversion_note'] . '';*/
$crl = curl_init();
curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($crl, CURLOPT_URL, $url);
curl_setopt($crl, CURLOPT_HEADER, 0);
curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($crl, CURLOPT_POST, 1);
curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
$response = curl_exec($crl);
curl_close($crl);

$output = json_decode($response, true);


/**
 * this block is for save orts data to database_____________________________________________________________________
 **/

$contact_id = $id;
$raise_date = $data_input['cur_time'];
$connection_due_date = '';
$ticket_number = $output['ticket_number'];
$ticket_agent = $data_input['name'];
$status = 'open';

$result_data['raise_date'] = $raise_date;
$result_data['ticket_number'] = $ticket_number;
$result_data['ticket_agent'] = $ticket_agent;

$date_time = isset($data_input['cur_time']) ? $data_input['cur_time'] : date('Y-m-d H:i:s');

$today = $date_time;
$nextday = date('Y-m-d H:i:s', strtotime("+3 days"));

if (trim($raise_date) == '' || $raise_date == null) {
    $raise_date = $today;
}

if (trim($connection_due_date) == '' || $connection_due_date == null) {
    $connection_due_date = $nextday;
}


$is_error = 0;

$select_qry = "select count(*) as `count` from otrs_ticket where contact_id='$contact_id'";

$result = Sql_exec($cn, $select_qry);

$count = 0;

while ($data = Sql_fetch_array($result)) {
    $count = $data['count'];
}

//if(trim($ticket_number) !='' && ($count ==0 || trim($count) =='0') ){
if (($count == 0 || trim($count) == '0')) {
    if (trim($ticket_number) != '') {
        $contact_qry = "insert into otrs_ticket(status,raise_date,connection_due_date,ticket_number,ticket_agent,contact_id,update_date,update_by) values('$status','$raise_date','$connection_due_date','$ticket_number','$ticket_agent','$contact_id', '" . $date_time . "','" . $user_id . "')";
    } else {
        $contact_qry = "insert into otrs_ticket(status,raise_date,connection_due_date,ticket_number,ticket_agent,contact_id,update_date,update_by) values('$status','$raise_date','$connection_due_date','$ticket_number','$ticket_agent','$contact_id', '" . $date_time . "','" . $user_id . "')";
    }

} else {
    $contact_qry = "update otrs_ticket set raise_date='$raise_date',connection_due_date='$connection_due_date',status='$status',ticket_number='$ticket_number',ticket_agent='$ticket_agent', update_date= '" . $date_time . "',update_by='" . $user_id . "' where contact_id='$contact_id'";
}

try {
    $res = Sql_exec($cn, $contact_qry);
} catch (Exception $e) {
    $is_error = 1;
}


/**
 * this block is for send mail_____________________________________________________________________________________
 **/

$message = "";
$message .= "<b>Client Name:  </b>" . $datainfo['name'] . "</br>";
$message .= "<b>Connection Address:  </b>" . $datainfo['address'] . "</br>";
$message .= "<b>Collection Address:  </b>" . $datainfo['collection_address'] . "</br>";
$message .= "<b>Contact Number:  </b>" . $datainfo['contact_number'] . "</br>";
$message .= "<b>Additional Contact Number:  </b>" . $datainfo['additional_contact_number'] . "</br>";
$message .= "<b>Email Address:  </b>" . $datainfo['email'] . "</br>";
$message .= "<b>Client Type:  </b>" . $datainfo['client_type'] . "</br>";
$message .= "<b>Availability or Access Time:  </b>" . $datainfo['assignment_date'] . "</br>";
$message .= "<b>Package Detail:  </b>" . $datainfo['package'] . "</br>";
$message .= "<b>Initial Collection:  </b>Collected</br>";
$message .= "<b>Money Receipt Number:  </b>" . $datainfo['receipt_number'] . "</br>";
$message .= "<b>Date of Installation:  </b>" . $connection_due_date . "</br>";
$message .= "<b>Client Comments:  </b>" . $datainfo['conversion_note'] . "</br>";

$total_msg_body .= $message . '</br>';

ClosedDBConnection($cn);

$to = array("Ashiq@ssd-tech.com" => "Ashiq", "Zaman@ssd-tech.com" => "Zaman", "jyoti@ssd-tech.com" => "Joyti", "tazmin@fosterpayments.com" => "Tazmin");
//$to = array("talemul@ssd-tech.com" => "Nazibul");

$subject = 'Installation: ' . $datainfo['name'] . ' (' . $datainfo['area'] . ')';

//$cc_send = array("sales@dozeinternet.com" => "Doze Sales", "siraj@ssd-tech.com" => "Sakib Siraj", "jannat@ssd-tech.com" => "Laila Jannat");
$cc_send = array("abeda@ssd-tech.com" => "Abeda" );
//echo $to . ' ' . $subject . ' ' . $total_msg_body . ' ' . $cc_send;
$retval = send_mail($to, $subject . ' test develop', $total_msg_body, $cc_send);

$result_data['status'] = true;

echo json_encode($result_data);