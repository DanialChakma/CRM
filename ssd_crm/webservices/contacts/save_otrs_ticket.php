<?php
/*
 *
Edited by Talemul Islam
*/
require_once "../lib/common.php";
checkSession();
$user_id = $_SESSION['user_id'];
$cn = connectDB();
//var_dump($_REQUEST); exit;

$raise_date = '';
$connection_due_date = '';

if (isset($_REQUEST)) {
    $action = $_REQUEST["action"];
    $action_id = $_REQUEST["action_id"];
    $contact_id = $_REQUEST["otrs_contact_id"];
    $raise_date = $_REQUEST["raise_date"];
    $connection_due_date = $_REQUEST["due_date"];
    $ticket_number = $_REQUEST["ticket_number"];
    $ticket_agent = $_REQUEST["ticket_agent"];
    $status = $_REQUEST["otrs_status"];
    $date_time = isset($_REQUEST["date_time"]) ? $_REQUEST["date_time"] : date('Y-m-d H:i:s');
}

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
    if(trim($ticket_number) !=''){
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


ClosedDBConnection($cn);

echo $is_error;
