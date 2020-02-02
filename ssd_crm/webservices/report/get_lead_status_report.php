<?php
require_once "../lib/common.php";

$data = $_REQUEST['info'];
$date_from = $data["date_from"];
$date_to = $data["date_to"];
$lead_source = $data["lead_source"];

$condition = "";
if($lead_source == '-1'){
    $condition = "AND 1=1";
} else {
    $condition = "AND lead_source='$lead_source'";
}

$cn = connectDB();

$select_qry = "SELECT
  lead_source,
  stage_id,
  COUNT(id)   AS `count`
FROM contacts
WHERE create_date BETWEEN '$date_from'
    AND '$date_to'
    AND lead_source IS NOT NULL
    AND stage_id IS NOT NULL
    AND customer_type <> 'block'
    AND TRIM(lead_source) <> '' $condition
GROUP BY lead_source,stage_id";


$result = Sql_exec($cn,$select_qry);

$result_array = array();

while($dt = Sql_fetch_array($result)){
    $stage_id = $dt['stage_id'];
    if($stage_id == 3){
        $type = "NotConnected";
    }else if($stage_id == 4){
        $type = "Interested";
    }else if($stage_id == 5){
        $type = "NotInterested";
    }else if($stage_id == 6){
        $type = "VerballyConfirmed";
    }else if($stage_id == 7){
        $type = "SalesDone";
    }else if($stage_id == 10){
        $type = "AfterSales";
    }  else {
        $type = $stage_id;
    }

    $result_array[$dt['lead_source']][$type] = $dt['count'];
    $result_array[$dt['lead_source']]['Total'] += $dt['count'];
}

$unassigned_qry = "SELECT
  lead_source,
  COUNT(id)   AS `count`
FROM contacts
WHERE create_date BETWEEN '$date_from'
    AND '$date_to'
    AND customer_type <> 'block'
    AND contacts.id NOT IN(SELECT DISTINCT contact_id FROM call_history) $condition
GROUP BY lead_source";


$result_unassigned = Sql_exec($cn,$unassigned_qry);

while($dt = Sql_fetch_array($result_unassigned)){
    $result_array[$dt['lead_source']]['UnAssigned'] = $dt['count'];
    $result_array[$dt['lead_source']]['Total'] += $dt['count'];
}

$block_qry = "SELECT
  lead_source,
  COUNT(id)   AS `count`
FROM contacts
WHERE create_date BETWEEN '$date_from'
    AND '$date_to'
    AND lead_source IS NOT NULL
    AND TRIM(lead_source) <> ''
    AND customer_type='block' $condition
GROUP BY lead_source";


$result_block = Sql_exec($cn,$block_qry);

while($dt = Sql_fetch_array($result_block)){
    $result_array[$dt['lead_source']]['Block'] = $dt['count'];
    $result_array[$dt['lead_source']]['Total'] += $dt['count'];
}


$data_array = array();
$i=0;
foreach($result_array AS $key=>$value){
    $j = 0;

    if($value['NotConnected'] == null){
        $value['NotConnected'] = 0;
    }
    if($value['Interested'] == null){
        $value['Interested'] = 0;
    }
    if($value['NotInterested'] == null){
        $value['NotInterested'] = 0;
    }
    if($value['VerballyConfirmed'] == null){
        $value['VerballyConfirmed'] = 0;
    }
    if($value['SalesDone'] == null){
        $value['SalesDone'] = 0;
    }
    if($value['UnAssigned'] == null){
        $value['UnAssigned'] = 0;
    }
    if($value['Block'] == null){
        $value['Block'] = 0;
    }
    if($value['AfterSales'] == null){
        $value['AfterSales'] = 0;
    }


    $data_array[$i][$j++] = $key;
    $data_array[$i][$j++] = $value['Total'];
    $data_array[$i][$j++] = $value['UnAssigned'];
    $data_array[$i][$j++] = $value['Block'];
    $data_array[$i][$j++] = $value['NotConnected'];
    $data_array[$i][$j++] = $value['Interested'];
    $data_array[$i][$j++] = $value['NotInterested'];
    $data_array[$i][$j++] = $value['VerballyConfirmed'];
    $data_array[$i][$j++] = $value['SalesDone'];
    $data_array[$i][$j++] = $value['AfterSales'];
    $i++;
}

ClosedDBConnection($cn);

echo json_encode($data_array);
