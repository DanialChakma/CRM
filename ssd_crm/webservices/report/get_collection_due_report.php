<?php

require_once "../lib/common.php";

$cn = connectDB();

$today = isset($_REQUEST['info']['current_date_js']) ? $_REQUEST['info']['current_date_js'] : date("Y-m-d");

$date_from = $today." 00:00:00";
$date_to = $today." 23:59:59";

if (isset($_REQUEST) && $_REQUEST) {
    $data = $_REQUEST['info'];
    $date_from = $data["date_from"];
    $date_to = $data["date_to"];
    $select_qry = "SELECT * FROM contacts,customer_conversion WHERE contacts.id=customer_conversion.contact_id AND contacts.customer_type='closed' AND customer_conversion.collection_date >= '$date_from' AND customer_conversion.collection_date <= '$date_to'";
} else {
    $select_qry = "SELECT * FROM contacts,customer_conversion WHERE contacts.id=customer_conversion.contact_id AND contacts.customer_type='closed' AND customer_conversion.collection_date >= '$date_from' AND customer_conversion.collection_date <= '$date_to'";

}
//echo $select_qry; exit;

$result = Sql_exec($cn,$select_qry);

$data_array = array();
$i = 0;
$serial = 0;

while($dt = Sql_fetch_array($result)){
    $j = 0;
    $data_array[$i][$j++] = $serial+1;
    $data_array[$i][$j++] = '<a href="#" title="Details" class="text_green" onclick=\'show_detail_closed_customer('.$dt['contact_id'].');\'>Detail</a>';
    $data_array[$i][$j++] = $dt['first_name']." ".$dt['last_name'];
    $data_array[$i][$j++] = $dt['email'];
    $data_array[$i][$j++] = $dt['phone1'];
    $data_array[$i][$j++] = $dt['address1'];
    $data_array[$i][$j++] = $dt['do_area'];
    $data_array[$i][$j++] = $dt['area'];

    $data_array[$i][$j++] = $dt['assignment_date'];

    $collection_date = $dt['collection_date'];
    $collection_date_time = explode(" ",$collection_date);
    $date = $collection_date_time[0];
    $time = $collection_date_time[1];
    $data_array[$i][$j++] = $date;
    $data_array[$i][$j++] = $time;

    $data_array[$i][$j++] = $dt['promoted_to_closed'];
    $data_array[$i][$j++] = $dt['package'];
    $data_array[$i][$j++] = $dt['collection_amount'];

    $i++;
    $serial++;
}

ClosedDBConnection($cn);

echo json_encode($data_array);