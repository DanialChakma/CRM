<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 7/5/2015
 * Time: 3:17 PM
 */

require_once "../lib/common.php";

$input_data = isset($_REQUEST) ? $_REQUEST : exit;

// print your data here. note the following:
// - cells/columns are separated by tabs ("\t")
// - rows are separated by newlines ("\n")
$general_filter = $input_data["general_filter"];
$filter_by_lead_source = $input_data["filter_by_lead_source"];

$condition = "";

if($general_filter == "-1" ||$general_filter == "all" ){
    $condition .= '';
} else if($general_filter == "unasigned_lead"){
    $condition .= "WHERE customer_type='lead' AND assign_to <=0";
} else if($general_filter == "unasigned_block"){
    $condition .= "WHERE customer_type='block' AND assign_to <=0";
} else if($general_filter == "unasigned_block"){
    $condition .= "WHERE customer_type='block' AND assign_to ='".$_SESSION['user_id']."'";
} else {
    $condition .= "WHERE customer_type='".$general_filter."'";
}


if($filter_by_lead_source != '-1'){
    if(trim($condition) != ""){
        $condition .= " AND lead_source like '%" . $filter_by_lead_source . "%'";
    } else {
        $condition .= " WHERE lead_source like '%" . $filter_by_lead_source . "%'";
    }

}

$file_name = $general_filter .'_'. $filter_by_lead_source .'_contacts'. date('now') . '.xls';

header("Content-Type: application/vnd.ms-excel");
header("Content-disposition: attachment; filename=$file_name");

$cn = connectDB();
$stage_map = array();
$agent_map = array();

$qry = "SELECT uinfo.user_id, TRIM(CONCAT_WS(' ',uinfo.first_name,uinfo.last_name)) AS 'agent' FROM user_info uinfo";
$rs = Sql_exec($cn,$qry);
while($row = Sql_fetch_array($rs)){
    $agent_map[trim($row['user_id'])] = $row['agent'];
}

Sql_Free_Result($rs);

$stage_qry = "SELECT id, stage FROM select_stage";
$rs = Sql_exec($cn,$stage_qry);
while($row = Sql_fetch_array($rs)){
    $stage_map[trim($row['id'])] = $row['stage'];
}
Sql_Free_Result($rs);



$select_qry = "SELECT * FROM  contacts ".$condition;

//echo $select_qry; exit;
$res = Sql_exec($cn, $select_qry);

ClosedDBConnection($cn);

echo 'Contact ID' . "\t" . 'First Name' . "\t" . 'Last Name' . "\t" . 'Address' . "\t" . 'Phone' . "\t" . 'Last Call Date' . "\t" .'Agent Name'. "\t" . 'Stage'."\n";

while ($dt = Sql_fetch_array(($res))) {
    $agent_name = (array_key_exists(trim($dt['assign_to']),$agent_map) ? $agent_map[trim($dt['assign_to'])]: "");
    $stage_name = (array_key_exists(trim($dt['stage_id']),$stage_map) ? $stage_map[trim($dt['stage_id'])]: "");
    echo $dt['id'] . "\t" . $dt['first_name'] . "\t" . $dt['last_name'] . "\t" . $dt['address1'] . "\t" . $dt['phone1'] . "\t" . $dt['last_call_date']. "\t" .$agent_name. "\t". $stage_name . "\n";
}

unset($agent_map);
unset($stage_map);

?>