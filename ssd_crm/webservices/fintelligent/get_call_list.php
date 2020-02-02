<?php

require_once "../lib/common.php";
$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
$renew_date = trim($datainfo['renew_date']);
$start_date = $renew_date." ". "00:00:00";
$end_date = $renew_date ." ". "23:59:59";
$cn = connectDB();
$qry = "SELECT * FROM monthly_bill_call_list WHERE next_renewal_date BETWEEN '$start_date' AND '$end_date';";
$rs = Sql_exec($cn,$qry);
$dataset = array();
$i=0;
while($dt=Sql_fetch_array($rs)){
    $j=0;

    $id = $dt['id'];
    $renewal_date = $dt['next_renewal_date'];
    $email = $dt['email'];
    $assign_to = $dt['assign_to'];

    $dataset[$i][$j++] = '<input type="checkbox" value="'.$id.'"/>';
    $dataset[$i][$j++] = $email;
    $dataset[$i][$j++] = $renewal_date;
    $dataset[$i][$j++] = $assign_to;
    $i++;
}

ClosedDBConnection($cn);

echo json_encode($dataset);


