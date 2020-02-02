<?php

require_once "../lib/common.php";

$cn = connectDB();

$today = isset($_REQUEST['info']['current_date_js']) ? $_REQUEST['info']['current_date_js'] : date("Y-m-d");

$date_from = $today . " 00:00:00";
$date_to = $today . " 23:59:59";

if (isset($_REQUEST['info']) && $_REQUEST['info']) {
    $data = $_REQUEST['info'];
    $date_from = $data["date_from"] ;
    $date_to = $data["date_to"];
}

$select_qry="SELECT
  c.id,
  c.first_name,
  c.last_name,
  c.address1,
  c.lead_source,
  c.email,
  c.phone1,
  c.phone2,
  c.area,
  o.ticket_number,
  o.raise_date,
  h.collection_amount,
  p.collection_date,
  p.collected_by
FROM contacts AS c,
  payments AS p,
  otrs_ticket AS o,
  customer_conversion AS h
WHERE c.id = p.contact_id
    AND p.collection_status = 'closed'
    AND c.id = o.contact_id
    AND c.id = h.contact_id
    AND p.update_date >= '$date_from'
    AND p.update_date <= '$date_to'";


$result = Sql_exec($cn,$select_qry);

$data_array = array();
$i = 0;
$serial = 0;

while($dt = Sql_fetch_array($result)){
    $j = 0;
    $data_array[$i][$j++] = '<a href="#" title="Details" class="text_green" onclick="show_detail_lead(' . $dt['id'] . ');">Detail</a>';;
    $data_array[$i][$j++] = $dt['first_name'];
    $data_array[$i][$j++] = $dt['last_name'];
    $data_array[$i][$j++] = $dt['address1'];
    $data_array[$i][$j++] = $dt['lead_source'];
    $data_array[$i][$j++] = $dt['email'];
    $data_array[$i][$j++] = $dt['phone1'];
    $data_array[$i][$j++] = $dt['phone2'];
    $data_array[$i][$j++] = $dt['area'];
    $data_array[$i][$j++] = $dt['ticket_number'];
    $data_array[$i][$j++] = $dt['raise_date'];
    $data_array[$i][$j++] = $dt['collection_amount'];
    $data_array[$i][$j++] = $dt['collection_date'];
    $data_array[$i][$j++] = $dt['collected_by'];
    
    
    $i++;
    $serial++;
}

ClosedDBConnection($cn);

echo json_encode($data_array);