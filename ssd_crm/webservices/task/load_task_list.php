<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 12/18/2015
 * Time: 12:15 PM
 */
require_once "../lib/common.php";

$cn = connectDB();

$inline_editor = array();//this variable is for inline editor...
$options = '';

$select_qry = "SELECT id,task_title FROM work_task WHERE work_flow_id !='-1'";

$res = Sql_exec($cn, $select_qry);

while($dt = Sql_fetch_array(($res))){
    $options .= '<option value="'.$dt['id'].'">'.trim($dt['task_title'])."</option>";
}

ClosedDBConnection($cn);
echo $options;