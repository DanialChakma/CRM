<?php
    /**
     * Created by PhpStorm.
     * User: Nazibul
     * Date: 6/7/2015
     * Time: 4:36 PM
     */

    require('../lib/DataTableServerSideCustom.php');

// DB table to use
//$table = 'users';
$table = 'call_history LEFT JOIN select_stage ON call_history.stage_id=select_stage.id ';
   // $table = 'call_history';
// Table's primary key
    $primaryKey = 'id';
    

    $contact_id = $_REQUEST['info']['id'];

    $condition = "`contact_id`='$contact_id' ";

    $_REQUEST['info']['qryCondition'] = $condition;
    $_REQUEST['info']['orderString'] = ' call_date DESC';
//$_REQUEST['info']['selectString']='molpay_api.id as pri_id, molpay_api.bill_name, molpay_api.amount, molpay_api.tr_status, molpay_api.entry_time, molpay_api.api_response_time, users.id as user_id_dhk';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case object
// parameter names

    $columns = array(
        array('db' => 'call_date', 'dt' => 'call_date', 'formatter' => function ($d, $row) {
            if ($d == null || $d == '')
                return ' ';
            else {
                return $d;
            }
        }),
        array('db' => 'call_agent_name', 'dt' => 'call_agent', 'formatter' => function ($d, $row) {
            if ($d == null || $d == '')
                return ' ';
            else {
                return $d;
            }
        }),
        array('db' => 'feedback', 'dt' => 'feedback', 'formatter' => function ($d, $row) {
            if ($d == null || $d == '')
                return ' ';
            else {
                return $d;
            }
        }),
        array('db' => 'stage', 'dt' => 'stage', 'formatter' => function ($d, $row) {
            if ($d == null || $d == '')
                return ' ';
            else {
                return $d;
            }
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

