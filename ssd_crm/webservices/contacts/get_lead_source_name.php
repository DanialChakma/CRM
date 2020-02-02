<?php

require_once "../lib/common.php";

$cn = connectDB();

$query="select distinct lead_source from contacts";

$result = Sql_exec($cn,$query);
$i=0;

while($dt = Sql_fetch_array($result)){
    $data_array[$i] = $dt['lead_source'];
    $i++;
}

ClosedDBConnection($cn);
echo json_encode($data_array);