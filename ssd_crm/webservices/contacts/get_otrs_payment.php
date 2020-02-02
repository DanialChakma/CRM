<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 6/9/2015
 * Time: 4:49 PM
 */

require_once "../lib/common.php";

$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

$cn = connectDB();
//var_dump($_SESSION); exit;

$action_id = $datainfo["action_id"];

$reslt_data=array();

$select_qry = "SELECT * FROM otrs_ticket WHERE otrs_ticket.contact_id=$action_id";

$res = Sql_exec($cn, $select_qry);

while($dt = Sql_fetch_array($res)){
    $reslt_data['otrs_rise_date']=$dt['raise_date'];
    $reslt_data['otrs_tic_number']=$dt['ticket_number'];
    $reslt_data['otrs_tic_agent']=$dt['ticket_agent'];
    $reslt_data['otrs_status']=$dt['status'];
    $reslt_data['ticket_generated']=$dt['ticket_generated'];
    $reslt_data['connection_due_date']=$dt['connection_due_date'];
}

$select_qry = "SELECT * FROM payments WHERE payments.contact_id=$action_id";

$res = Sql_exec($cn, $select_qry);

while($dt = Sql_fetch_array($res)){
    $reslt_data['payment_collection_date']=$dt['collection_date'];
    $reslt_data['payment_collected_by']=$dt['collected_by'];
    $reslt_data['payment_rec_number']=$dt['receipt_number'];
    $reslt_data['payment_mode']=$dt['payment_mode'];
    $reslt_data['payment_status']=$dt['collection_status'];
    $reslt_data['nid'] = $dt['nid'];
    $reslt_data['photo'] = $dt['photo'];
    $reslt_data['sap'] = $dt['saf'];
    $reslt_data['cash_receive'] = $dt['cash_receive'];
    $reslt_data['remarks'] = $dt['remarks'];
}

ClosedDBConnection($cn);

echo json_encode($reslt_data);
