<?php


include_once "../lib/common.php";

$cn = connectDB();

$arrayInput = array();

$query = "SELECT * FROM groups";

$result = Sql_exec($cn, $query);

if (!$result) {
    echo "err+" . $query . " in line " . __LINE__ . " of file" . __FILE__;
    exit;
}
$data = array();
$i = 0;
//Sql_Num_Rows($result);
for(;$i<Sql_Num_Rows($result);$i++) {
    $j=0;
    $row=  Sql_fetch_array($result);
    $id= $row['id'];   
    $data[$i][$j++] = $row['id'];
    $data[$i][$j++] = $row['group_name'];
    $data[$i][$j++] = '<a onclick="update_group_details('.$id.')" href="#">Details</a>';
}


Sql_Free_Result($result);

ClosedDBConnection($cn);

echo json_encode($data);

?>