<?php

include_once "../lib/common.php";

$cn = connectDB();

$arrayInput = array();
$data = array();
if (isset($_REQUEST['info']['condition'])) {
    $group_query="select group_name from groups where ".$_REQUEST['info']['condition'];
    $query = "SELECT *
    FROM user_group
    
    INNER JOIN groups
    ON groups.id=user_group.group_id where group_id=" . $_REQUEST['info']['condition'];
    $result = Sql_exec($cn, $group_query);
    $row = Sql_fetch_array($result);
    $group_name=$row['group_name'];
} 

$arrayInput = array();



$result = Sql_exec($cn, $query);

if (!$result) {
    echo "err+" . $query . " in line " . __LINE__ . " of file" . __FILE__;
    exit;
}

$i = 0;
//Sql_Num_Rows($result);
for (; $i < Sql_Num_Rows($result); $i++) {
    $row = Sql_fetch_array($result);
    $j = 0;
    $id = $row['user_id'];
    $data[$i][$j++] = $id;
    // $info = '' . Sql_Result($row, "id") . '|' . Sql_Result($row, "gre_name") . '|' . Sql_Result($row, "peer_outer") . '|' . Sql_Result($row, "peer_inner") . '|' . Sql_Result($row, "my_inner");
}

$data['name']=$group_name;
$data['length']=$i;

Sql_Free_Result($result);

ClosedDBConnection($cn);

echo json_encode($data);
?>