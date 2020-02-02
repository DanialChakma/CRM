<?php


include_once "../lib/common.php";

$data_req=$_REQUEST['info'];

$cn = connectDB();

$arrayInput = array();

$query = "SELECT * FROM user_info";

$result = Sql_exec($cn, $query);

if (!$result) {
    echo "err+" . $query . " in line " . __LINE__ . " of file" . __FILE__;
    exit;
}
$data = array();
$i = 0;
//Sql_Num_Rows($result);
for(;$i<Sql_Num_Rows($result);$i++) {
    $row=  Sql_fetch_array($result);
    $j = 0;
    $id=$row['user_id'];
    $data[$i][$j++] = $row['first_name']." ".$row['last_name'];
    $data[$i][$j++] = '<input class="subjectid" id='.$id.' name="SubjectID" value='.$id.' type="checkbox">';
   
   // $info = '' . Sql_Result($row, "id") . '|' . Sql_Result($row, "gre_name") . '|' . Sql_Result($row, "peer_outer") . '|' . Sql_Result($row, "peer_inner") . '|' . Sql_Result($row, "my_inner");
}


Sql_Free_Result($result);

ClosedDBConnection($cn);

echo json_encode($data);

?>