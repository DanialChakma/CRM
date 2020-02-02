<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 9/2/2015
 * Time: 9:53 AM
 */

require_once "../lib/common.php";

$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

$cn = connectDB();
//var_dump($_SESSION); exit;
$reslt_data = array();
$reslt_data['status'] = false;

if ($datainfo['action'] == 'update_single_row') {
    $qry = "update `" . $datainfo['table_name'] . "` set `" . $datainfo['update_col_name'] . "`='" . $datainfo['update_col_value'] . "' where `" . $datainfo['search_col_name'] . "`='" . $datainfo['search_col_value'] . "'";

    $res = Sql_exec($cn, $qry);
    $reslt_data['status'] = true;

}else if ($datainfo['action'] == 'delete_single_row') {
    $qry = "delete from `" . $datainfo['table_name'] . "` where `" . $datainfo['search_col_name'] . "`='" . $datainfo['search_col_value'] . "'";

    $res = Sql_exec($cn, $qry);
    $reslt_data['status'] = true;
    $reslt_data['qry_2'] = $qry;

}

ClosedDBConnection($cn);

echo json_encode($reslt_data);
