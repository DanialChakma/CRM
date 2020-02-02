<?php
/**
 * Created by PhpStorm.
 * User: Talemul
 * Date: 12/4/2015
 * Time: 5:51 PM
 */






require_once "../lib/common.php";

$cn = connectDB();

$today = date("Y-m-d");

$date_from = $today . " 00:00:00";
$date_to = $today . " 23:59:59";

if (isset($_REQUEST) && $_REQUEST) {
    $data = $_REQUEST['info'];
    $date_from = $data["date_from"] . " 00:00:00";
    $date_to = $data["date_to"] . " 23:59:59";
}


$select_qry = "SELECT user_id,user_name,
 (SELECT COUNT(sales_done) FROM change_stage_history
 WHERE change_stage_history.sales_done=1 AND change_stage_history.user_id=user_info.user_id
AND sales_done_date BETWEEN '$date_from' AND '$date_to' ) AS 'sales_done',

(SELECT COUNT(sales_done) FROM change_stage_history
 WHERE change_stage_history.sales_done=1 AND change_stage_history.user_id=user_info.user_id
AND DATE_FORMAT(sales_done_date,'%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')) AS 'current_month_before',
(SELECT COUNT(sales_done) FROM change_stage_history
 WHERE change_stage_history.sales_done=1 AND change_stage_history.user_id=user_info.user_id
AND DATE_FORMAT(sales_done_date,'%Y-%m') = DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 MONTH),'%Y-%m')) AS 'one_month_before',
(SELECT COUNT(sales_done) FROM change_stage_history
 WHERE change_stage_history.sales_done=1 AND change_stage_history.user_id=user_info.user_id
AND DATE_FORMAT(sales_done_date,'%Y-%m') = DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 2 MONTH),'%Y-%m')) AS 'two_month_before',
(SELECT COUNT(sales_done) FROM change_stage_history
 WHERE change_stage_history.sales_done=1 AND change_stage_history.user_id=user_info.user_id
AND DATE_FORMAT(sales_done_date,'%Y-%m') = DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 3 MONTH),'%Y-%m')) AS 'three_month_before', 
(SELECT COUNT(sales_done) FROM change_stage_history
 WHERE change_stage_history.sales_done=1 AND change_stage_history.user_id=user_info.user_id
AND sales_done_date <NOW() ) AS 'total_from_begain',
 (SELECT COUNT(sales_done) FROM change_stage_history
 WHERE change_stage_history.attempted=1 AND change_stage_history.user_id=user_info.user_id
AND attempted_date BETWEEN '$date_from' AND '$date_to' ) AS 'attempted',
 (SELECT COUNT(sales_done) FROM change_stage_history
 WHERE change_stage_history.connected=1 AND change_stage_history.user_id=user_info.user_id
AND connected_date BETWEEN '$date_from' AND '$date_to' ) AS 'connected',
 (SELECT COUNT(sales_done) FROM change_stage_history
 WHERE change_stage_history.interested=1 AND change_stage_history.user_id=user_info.user_id
AND interested_date BETWEEN '$date_from' AND '$date_to' ) AS 'interested',
 (SELECT COUNT(sales_done) FROM change_stage_history
 WHERE change_stage_history.verbally_confirmed=1 AND change_stage_history.user_id=user_info.user_id
AND verbally_confirmed_date BETWEEN '$date_from' AND '$date_to' ) AS 'verbally_confirmed',
 (SELECT COUNT(sales_done) FROM change_stage_history
 WHERE change_stage_history.verbally_confirmed=1 AND change_stage_history.user_id=user_info.user_id
AND verbally_confirmed_date <NOW() ) AS 'total_verbally_confirmed'
FROM user_info";
$result = Sql_exec($cn, $select_qry);

$data_array = array();
$i = 0;
$serial = 0;

while ($dt = Sql_fetch_array($result)) {
    $j = 0;
    $data_array[$i][$j++] = $dt['user_name'];
    $data_array[$i][$j++] = $dt['sales_done'];
    $data_array[$i][$j++] = $dt['current_month_before'];
    $data_array[$i][$j++] = $dt['one_month_before'] ;
    $data_array[$i][$j++] = $dt['two_month_before'];
    $data_array[$i][$j++] = $dt['three_month_before'];
    $data_array[$i][$j++] = $dt['total_from_begain'];
    $data_array[$i][$j++] = $dt['attempted'];
    $data_array[$i][$j++] = $dt['connected'];
    $data_array[$i][$j++] = $dt['interested'];
    $data_array[$i][$j++] = $dt['verbally_confirmed'];
    $data_array[$i][$j++] = $dt['total_verbally_confirmed'];

    $i++;
    $serial++;
}

ClosedDBConnection($cn);

echo json_encode($data_array);