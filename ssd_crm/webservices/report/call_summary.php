<?php
/**
 * Created by PhpStorm.
 * User: rahat
 * Date: 5/5/2015
 * Time: 7:39 PM
 */

include_once "../lib/common.php";
/*
$data_req=$_REQUEST['info'];

$cn = connectDB();

$arrayInput = array();

$query = "SELECT * FROM users";

$result = Sql_exec($cn, $query);

if (!$result) {
    echo "err+" . $query . " in line " . __LINE__ . " of file" . __FILE__;
    exit;
}

$data = array();
$i = 0;


while ($row = Sql_fetch_array($result)) {
    $j = 0;
    $data[$i][$j++] = Sql_Result($row, "id");
    $data[$i][$j++] = Sql_Result($row, "username");
    $data[$i][$j++] = Sql_Result($row, "name");

   // $info = '' . Sql_Result($row, "id") . '|' . Sql_Result($row, "gre_name") . '|' . Sql_Result($row, "peer_outer") . '|' . Sql_Result($row, "peer_inner") . '|' . Sql_Result($row, "my_inner");


    $data[$i][$j++] = '<button>detail</button>';
    $i++;
}

*/

$data = array();
$i = 0;


while ( $i <5) {
    $j = 0;
    $data[$i][$j++] = "28/05/2015";
    $data[$i][$j++] = 'A';
    $data[$i][$j++] = '6';
    $data[$i][$j++] = '6';
    $data[$i][$j++] = '6';
    

   // $info = '' . Sql_Result($row, "id") . '|' . Sql_Result($row, "gre_name") . '|' . Sql_Result($row, "peer_outer") . '|' . Sql_Result($row, "peer_inner") . '|' . Sql_Result($row, "my_inner");
    $i++;
}
//Sql_Free_Result($result);

//ClosedDBConnection($cn);

echo json_encode($data);

?>
