<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 9/6/2015
 * Time: 9:47 AM
 */

require_once "../lib/common.php";

$cn = connectDB();

$inline_editor = array();//this variable is for inline editor...
$options = '';

$select_qry = "select * from work_flow_def";

$res = Sql_exec($cn, $select_qry);

while($dt = Sql_fetch_array(($res))){
    $options .= '<option value="'.$dt['id'].'">'.trim($dt['work_flow_name'])."</option>";
    $inline_editor[$dt['id']] = trim($dt['work_flow_name']);
}


ClosedDBConnection($cn);

if (isset($_REQUEST['for_inline'])) {
    $inline_editor['0']='--select--';
    $array['selected'] =  '0';
    echo json_encode($inline_editor);
} else {
    echo $options;
}