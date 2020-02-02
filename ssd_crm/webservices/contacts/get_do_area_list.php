<?php

require_once "../lib/common.php";

$cn = connectDB();


$options = '';

$select_qry = "select * from do_areas";

$res = Sql_exec($cn, $select_qry);

while($dt = Sql_fetch_array(($res))){
    $options .= '<option value="'.trim($dt['area']).'">'.$dt['area']."</option>";
}


ClosedDBConnection($cn);

echo $options;
