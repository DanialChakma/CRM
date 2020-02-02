<?php

require_once "../lib/common.php";

$cn = connectDB();

$today = isset($_REQUEST['info']['current_date_js']) ? $_REQUEST['info']['current_date_js'] : date("Y-m-d");

$date_from = $today . " 00:00:00";
$date_to = $today . " 23:59:59";

if (isset($_REQUEST['info']) && $_REQUEST['info']) {
    $data = $_REQUEST['info'];
    $date_from = $data["date_from"];
    $date_to = $data["date_to"];
}


/*
  $select_qry="SELECT   DISTINCT contacts.id AS 'contact_id', contacts.lead_source as 'lead_source',user_info.`user_name` AS 'agent',
CONCAT(contacts.first_name,contacts.last_name) AS  'client_name',
contacts.address1 AS 'collection_address',contacts.address2 AS 'connection_address',contacts.email AS 'email',
contacts.phone1 AS 'phone1',customer_conversion.`client_type` AS 'client_type',
customer_conversion.package AS 'package',customer_conversion.install_cost AS 'install_cost',customer_conversion.monthly_cost AS 'monthly_cost',customer_conversion.month_number AS 'month_number',
contacts.do_area as 'do_area', otrs_ticket.ticket_number AS 'ticket_number', otrs_ticket.`raise_date` AS 'raise_date',
customer_conversion.`collection_amount` AS 'total_amount', customer_conversion.collection_date AS 'collection_date',
payments.`payment_mode` AS 'payment_mode',payments.`collection_status` AS 'collection_status',payments.`receipt_number` AS 'receipt_number',select_stage.`stage` AS 'status',
(SELECT COUNT(*) FROM call_history WHERE call_history.`contact_id`= contacts.`id`) AS 'number_of_calls',
(SELECT  TIME_FORMAT(SEC_TO_TIME(SUM(total_duration)),'%Hhr : %imin : %ssec') FROM call_history WHERE call_history.`contact_id`= contacts.`id`) AS 'call_duration'
  FROM   `contacts`
  LEFT JOIN `user_info` ON user_info.`user_id`=contacts.`assign_to`
  LEFT JOIN `select_stage` ON select_stage.`id`=contacts.`stage_id`
  LEFT JOIN customer_conversion ON customer_conversion.`contact_id`=contacts.`id`
  LEFT JOIN otrs_ticket ON otrs_ticket.`contact_id`=contacts.`id`
  LEFT JOIN payments ON payments.`contact_id`=contacts.`id`
  LEFT JOIN call_history ON call_history.`contact_id`=contacts.id
WHERE   call_history.`call_date` BETWEEN '$date_from' AND '$date_to'";

*/

$select_qry = "SELECT DISTINCT
    contacts.id AS 'contact_id',
    contacts.lead_source AS 'lead_source',
    MAX(call_history.call_date) AS 'CallDate',
    user_info.`user_name` AS 'agent',
    user_info.`user_role` AS 'user_role',
    CONCAT(contacts.first_name, contacts.last_name) AS 'client_name',
    contacts.address1 AS 'collection_address',
    contacts.address2 AS 'connection_address',
    contacts.email AS 'email',
    contacts.phone1 AS 'phone1',
    customer_conversion.`client_type` AS 'client_type',
    customer_conversion.package AS 'package',
    customer_conversion.install_cost AS 'install_cost',
    customer_conversion.monthly_cost AS 'monthly_cost',
    customer_conversion.month_number AS 'month_number',
    customer_conversion.real_ip_cost AS 'real_ip_cost',
    customer_conversion.additional_cost AS 'additional_cost',
    contacts.do_area AS 'do_area',
    otrs_ticket.ticket_number AS 'ticket_number',
    otrs_ticket.`raise_date` AS 'raise_date',
    customer_conversion.`collection_amount` AS 'total_amount',
    customer_conversion.collection_date AS 'collection_date',
    payments.`payment_mode` AS 'payment_mode',
    payments.`collection_status` AS 'collection_status',
    payments.`receipt_number` AS 'receipt_number'
FROM
    contacts
        INNER JOIN
    user_info ON user_info.`user_id` = contacts.`assign_to`
        INNER JOIN
    customer_conversion ON customer_conversion.contact_id = contacts.id
        INNER JOIN
    call_history ON call_history.contact_id = contacts.id
        INNER JOIN
    otrs_ticket ON otrs_ticket.contact_id = contacts.id
        INNER JOIN
    payments ON payments.contact_id = contacts.id
WHERE
    call_history.stage_id = 7
        AND call_history.call_date BETWEEN '$date_from' AND '$date_to'
GROUP BY (contacts.id)";

$result = Sql_exec($cn,$select_qry);

$data_array = array();
$i = 0;
$serial = 0;

while($dt = Sql_fetch_array($result)){
    $j = 0;
    $package_type = "";
    $package = trim($dt['package']);
    if( strpos($package,"Unlimited") !== false ){
        $package_type = "Unlimited";
    }else{
        $package_type = "Capped";
    }

    $temp_address = (trim($dt['collection_address']) == '') ? $dt['connection_address']: $dt['collection_address'];
    $data_array[$i][$j++] = $dt['client_name'];
    $data_array[$i][$j++] = $dt['contact_id'];
    $data_array[$i][$j++] = $dt['CallDate'];
    $data_array[$i][$j++] = $dt['phone1'];
    $data_array[$i][$j++] = $temp_address;
    $data_array[$i][$j++] = $dt['email'];
    $data_array[$i][$j++] = $dt['do_area'];
    $data_array[$i][$j++] = $package_type;
    $data_array[$i][$j++] = $dt['package'];
    $data_array[$i][$j++] = $dt['agent'];
    $data_array[$i][$j++] = $dt['user_role'];
    $data_array[$i][$j++] = $dt['install_cost'];
    $data_array[$i][$j++] = $dt['monthly_cost'];
    $data_array[$i][$j++] = $dt['month_number'];
    $data_array[$i][$j++] = $dt['real_ip_cost'];
    $data_array[$i][$j++] = $dt['additional_cost'];
    $data_array[$i][$j++] = $dt['total_amount'] + $dt['real_ip_cost'] + $dt['additional_cost'];
    $data_array[$i][$j++] = $dt['collection_date'];
    $data_array[$i][$j++] = $dt['receipt_number'];
    $data_array[$i][$j++] = $dt['ticket_number'];
    $data_array[$i][$j++] = $dt['payment_mode'];

    $i++;
    $serial++;
}

ClosedDBConnection($cn);

echo json_encode($data_array);


/*
 *
 *   this is was created by monir hossain......................
 *




$select_closed = "SELECT lead_source, COUNT(lead_source) AS `count_closed` FROM contacts WHERE promoted_to_closed <>'' AND promoted_to_closed >= '$date_from' AND promoted_to_closed <= '$date_to' AND doze_id LIKE 'DOZE%' AND lead_source<>'' GROUP BY lead_source";
$result_closed = Sql_exec($cn, $select_closed);

$closed_array = array();

while ($dt = Sql_fetch_array($result_closed)) {
    $closed_array[trim($dt['lead_source'])] = $dt['count_closed'];
}


//$select_customer = "SELECT lead_source, COUNT(lead_source) AS `count_customer` FROM contacts WHERE promoted_to_customer <>''  AND promoted_to_customer >= '$date_from' AND promoted_to_customer <= '$date_to' AND doze_id LIKE 'DOZE%' AND lead_source<>'' GROUP BY lead_source";

$select_customer = "SELECT
  lead_source,
  COUNT(lead_source) AS `count_customer`
FROM contacts,
  conversion_history
WHERE contacts.id = conversion_history.contact_id
    AND promoted_to_customer <> ''
    AND conversion_history.collection_date >= '$date_from'
    AND conversion_history.collection_date <= '$date_to'
    AND doze_id LIKE 'DOZE%'
    AND lead_source <> ''
GROUP BY lead_source";

$result_customer = Sql_exec($cn, $select_customer);

$customer_array = array();

while ($dt = Sql_fetch_array($result_customer)) {
    $customer_array[trim($dt['lead_source'])] = $dt['count_customer'];
}

//print_r($customer_array);

$merged_array = array_merge($closed_array, $customer_array);

$result_array = array();

foreach ($merged_array AS $key => $val) {
    if (array_key_exists($key, $closed_array)) {
        $result_array[$key]['closed'] = $closed_array[$key];
    } else {
        $result_array[$key]['closed'] = 0;
    }
    if (array_key_exists($key, $customer_array)) {
        $result_array[$key]['customer'] = $customer_array[$key];
    } else {
        $result_array[$key]['customer'] = 0;
    }

}

//print_r($result_array); exit;

$data_array = array();
$i = 0;

foreach ($result_array AS $index => $value) {
    $j = 0;
    $data_array[$i][$j++] = $index;
    $data_array[$i][$j++] = $value['closed'];
    $data_array[$i][$j++] = $value['customer'];

    $i++;
}

ClosedDBConnection($cn);

echo json_encode($data_array);

*/