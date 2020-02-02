<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 9/9/2015
 * Time: 4:05 PM
 */

require_once "../lib/common.php";

$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

$cn = connectDB();
//var_dump($_SESSION); exit;
$reslt_data = array();

$select_qry = "SELECT otrs_ticket.id, payments.contact_id, collection_status, receipt_number, collection_date, collected_by,payment_mode, payments.doze_id, first_name, last_name, phone1, address1, email, connection_due_date, raise_date, ticket_number, ticket_agent, otrs_ticket.status FROM contacts LEFT JOIN payments ON (contacts.id=payments.contact_id) LEFT JOIN otrs_ticket ON (contacts.id=otrs_ticket.contact_id) WHERE contacts.id=" . $datainfo['id'];
//echo $select_qry; exit;
$res = Sql_exec($cn, $select_qry);

while ($dt = Sql_fetch_array(($res))) {
    $reslt_data['id'] = $dt['id'];
    $reslt_data['contact_id'] = $dt['contact_id'];
    $reslt_data['collection_status'] = $dt['collection_status'];
    $reslt_data['receipt_number'] = $dt['receipt_number'];
    $reslt_data['collection_date'] = $dt['collection_date'];
    $reslt_data['collected_by'] = $dt['collected_by'];
    $reslt_data['payment_mode'] = $dt['payment_mode'];
    $reslt_data['doze_id'] = $dt['doze_id'];
    $reslt_data['name'] = $dt['first_name']. ' ' .$dt['last_name'];
    $reslt_data['phone1'] = $dt['phone1'];
    $reslt_data['address1'] = $dt['address1'];
    $reslt_data['email'] = $dt['email'];
    $reslt_data['connection_due_date'] = $dt['connection_due_date'];
    $reslt_data['raise_date'] = $dt['raise_date'];
    $reslt_data['ticket_number'] = $dt['ticket_number'];
    $reslt_data['ticket_agent'] = $dt['ticket_agent'];
    $reslt_data['status'] = $dt['status'];
}

ClosedDBConnection($cn);

echo json_encode($reslt_data);