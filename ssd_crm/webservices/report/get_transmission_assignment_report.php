<?php

require_once "../lib/common.php";

$cn = connectDB();

$today = date("Y-m-d");

$date_from = $today . " 00:00:00";
$date_to = $today . " 23:59:59";

if (isset($_REQUEST) && $_REQUEST) {
    $data = $_REQUEST['info'];
    $date_from = $data["date_from"];
    $date_to = $data["date_to"] ;
}


$select_qry = "SELECT * FROM otrs_ticket,contacts WHERE contacts.id=otrs_ticket.contact_id AND otrs_ticket.connection_due_date >= '$date_from' AND otrs_ticket.connection_due_date <= '$date_to'";
$result = Sql_exec($cn, $select_qry);

$data_array = array();
$i = 0;
$serial = 0;

while ($dt = Sql_fetch_array($result)) {
    $j = 0;
    $data_array[$i][$j++] = $serial + 1;
    $data_array[$i][$j++] = $dt['ticket_number'];
    $data_array[$i][$j++] = $dt['phone1'];
    $data_array[$i][$j++] = $dt['first_name'] . " " . $dt['last_name'];
    $data_array[$i][$j++] = $dt['address1'];
    $data_array[$i][$j++] = $dt['area'];
    $data_array[$i][$j++] = $dt['do_area'];
    $data_array[$i][$j++] = $dt['raise_date'];
    $data_array[$i][$j++] = $dt['connection_due_date'];
    $data_array[$i][$j++] = $dt['note'];

    $i++;
    $serial++;
}

ClosedDBConnection($cn);

echo json_encode($data_array);