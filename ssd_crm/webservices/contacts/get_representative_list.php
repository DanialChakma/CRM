<?php

require_once "../lib/common.php";

$cn = connectDB();


$options = '';
$inline_editor = array();//this variable is for inline editor...

$select_qry = "select * from user_info where user_status=0"; // user_role='Retail' and

$res = Sql_exec($cn, $select_qry);

while ($dt = Sql_fetch_array(($res))) {
    $options .= '<option value="' . $dt['user_id'] . '">' . trim($dt['first_name']) . " " . trim($dt['last_name']) . "</option>";
    $inline_editor[$dt['user_id']] = trim($dt['first_name']) . " " . trim($dt['last_name']);
}


ClosedDBConnection($cn);

if (isset($_REQUEST['for_inline'])) {
    $inline_editor['0']='--select--';
    $array['selected'] =  '0';
    echo json_encode($inline_editor);
} else {
    echo $options;
}