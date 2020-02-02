<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 8/5/2015
 * Time: 12:57 PM
 */

require('../lib/DataTableServerSideCustom.php');

$input_data = isset($_REQUEST['info']) ? $_REQUEST['info'] : exit;

// DB table to use
//$table = 'users';
//$table = '`molpay_api` LEFT JOIN `users` ON (`molpay_api`.`uid` = `users`.`username` OR `molpay_api`.`uid` = CONCAT(`users`.`username`, \'@dhakagate.com\'))';
$table = 'contacts AS c';
// Table's primary key
$primaryKey = 'id';
/*
$from = $_REQUEST['info']['from'] . ' 00:00:00';
$to = $_REQUEST['info']['to'] . ' 23:59:59';
$report_type = $_REQUEST['info']['report_type'];
$report_menu = $_REQUEST['info']['report_menu'];
*/

$condition = " (c.id='" . $input_data['sample'] . "' OR c.email like '%" . $input_data['sample'] . "%' OR c.phone1 like '%" . $input_data['sample'] . "%'  OR c.phone2 like '%" . $input_data['sample'] . "%')";

$_REQUEST['info']['qryCondition'] = $condition;
//$_REQUEST['info']['selectString']='molpay_api.id as pri_id, molpay_api.bill_name, molpay_api.amount, molpay_api.tr_status, molpay_api.entry_time, molpay_api.api_response_time, users.id as user_id_dhk';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case object
// parameter names

$columns = array(
    array('db' => 'id', 'dt' => 'contact_id', 'formatter' => function ($d, $row) {
        if ($d == null || $d == '')
            return ' ';
        else {
            return $d;
        }
    }),
    array('db' => 'customer_type', 'dt' => 'customer_type', 'formatter' => function ($d, $row) {
        if ($d == null || $d == '')
            return ' ';
        else {
            return $d;
        }
    }),
    array('db' => 'first_name', 'dt' => 'first_name', 'formatter' => function ($d, $row) {
        if ($d == null || $d == '')
            return ' ';
        else {
            return $d;
        }
    }),
    array('db' => 'last_name', 'dt' => 'last_name', 'formatter' => function ($d, $row) {
        if ($d == null || $d == '')
            return ' ';
        else {
            return $d;
        }
    }),
    array('db' => 'address1', 'dt' => 'address1', 'formatter' => function ($d, $row) {
        if ($d == null || $d == '')
            return ' ';
        else {
            return $d;
        }
    }),
    array('db' => 'phone1', 'dt' => 'phone1', 'formatter' => function ($d, $row) {
        if ($d == null || $d == '')
            return ' ';
        else {
            return $d;
        }
    }),
    array('db' => 'email', 'dt' => 'email', 'formatter' => function ($d, $row) {
        if ($d == null || $d == '')
            return ' ';
        else {
            return $d;
        }
    }),
    array('db' => 'id', 'dt' => 'id', 'formatter' => function ($d, $row) {
//        $fn_name='';
//        if ($row['customer_type'] == 'lead') {
//            $fn_name='show_detail_lead';
//        }
//        if ($row['customer_type'] == 'prospect') {
//            $fn_name='show_detail_prospect';
//        }
//        if ($row['customer_type'] == 'closed') {
//            $fn_name='show_detail_closed_customer';
//        }
//        if ($row['customer_type'] == 'customer') {
//            $fn_name='show_detail_customer';
//        }
//        if ($row['customer_type'] == 'block') {
//            $fn_name='show_detail_block';
//        }

        return '<a href="#" title="Details" class="text_green" onclick="show_detail_lead(' . $d . ');">Detail</a>';
    })
);
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
$input_data = isset($_REQUEST) ? $_REQUEST : exit;
$dataTableServer = new DataTableServer();
echo json_encode(
    $dataTableServer->simplePagination($input_data, $table, $columns)
);

