<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 9/7/2015
 * Time: 1:05 PM
 */

require_once "../lib/common.php";

$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
$cn = connectDB();

$options = '';

$select_qry = "select * from work_flow_details where work_flow_id=" . $datainfo['id'];

$res = Sql_exec($cn, $select_qry);

while ($dt = Sql_fetch_array(($res))) {
    $options .= '<option value="' . $dt['id'] . '">' . trim($dt['node_name']) . "</option>";
}

ClosedDBConnection($cn);

echo $options;