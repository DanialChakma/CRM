<?php

require_once "../lib/common.php";

$cn = connectDB();


$options = '';

$select_qry = "select * from user_info where user_role='Retail'";

$res = Sql_exec($cn, $select_qry);

while($dt = Sql_fetch_array(($res))){
    $options .= '<option value="'.$dt['user_id'].'">'.trim($dt['first_name'])." ".trim($dt['last_name'])."</option>";
}


ClosedDBConnection($cn);

echo $options;