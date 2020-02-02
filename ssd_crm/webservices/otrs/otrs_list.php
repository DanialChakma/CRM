<?php
    /**
     * Created by PhpStorm.
     * User: Nazibul
     * Date: 9/8/2015
     * Time: 5:13 PM
     */

    require('../lib/DataTableServerSideCustom.php');

    $table = 'otrs_ticket as o, contacts as c';
// Table's primary key
    $primaryKey = 'id';
//print_r($_REQUEST['info']);exit;

    $condition = " (o.contact_id = c.id) ";

    if (isset($_REQUEST['info']['search_key'])) {
        $key = $_REQUEST['info']['search_key'];
        $condition .= " and (o.ticket_number like '%$key%' or c.phone1 like '%$key%' or c.address1 like '%$key%' or c.first_name like '%$key%' or c.last_name like '%$key%') ";
    }

    if (isset($_REQUEST['info']['condtion_ticket'])) {
        $cond = $_REQUEST['info']['condtion_ticket'];
        if ($cond == 'not') {
            $condition .= " and (o.ticket_number='') ";
        } else {
            $condition .= " and (o.ticket_number!='') ";
        }
    }

    if (isset($_REQUEST['info']['date_form']) && isset($_REQUEST['info']['date_to'])) {
        $from = $_REQUEST['info']['date_form'];
        $to = $_REQUEST['info']['date_to'];

        if ($from != '') {
            $condition .= " AND o.raise_date>='$from' ";
        }
        if ($to != '') {
            $condition .= " AND o.raise_date<='$to' ";
        }
    }

    $_REQUEST['info']['qryCondition'] = $condition;
    $_REQUEST['info']['orderString'] = 'o.connection_due_date desc ';
//$_REQUEST['info']['selectString']='molpay_api.id as pri_id, molpay_api.bill_name, molpay_api.amount, molpay_api.tr_status, molpay_api.entry_time, molpay_api.api_response_time, users.id as user_id_dhk';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case object
// parameter names

    $columns = array(
        array('db' => 'contact_id', 'dt' => 'filter', 'formatter' => function ($d, $row) {
            if ($d == null || $d == '')
                return ' ';
            else {
                if (trim($row['ticket_number']) == '' || $row['ticket_number'] == null) {
                    $str = ' <input type="checkbox" value="' . $d . '"> ';
                } else {
                    $str = ' ';
                }

                return $str;
            }
        }),
        array('db' => 'phone1', 'dt' => 'contact_id', 'formatter' => function ($d, $row) {
            $str = '';
            $str .= '<div style="color: #002a80; width: 600px;"><div style="width: 300px; text-align: left; float: left" onclick="show_detail_lead(' . $row['contact_id'] . ')"><b><a>' . $row['first_name'] . '</a></b> (' . $row['phone1'] . ') </div>';
            $str .= '<div style="color: #444444; font-size: 11px; width: 300px; text-align: left; float: right; ">' . $row['address1'] . ' </div></div>';

            return $str;

        }),
        array('db' => 'raise_date', 'dt' => 'raise_date', 'formatter' => function ($d, $row) {
            if ($d == null || $d == '')
                return ' ';
            else {
                return $d;
            }
        }),
        array('db' => 'connection_due_date', 'dt' => 'connection_due_date', 'formatter' => function ($d, $row) {
            if ($d == null || $d == '')
                return ' ';
            else {
                return $d;
            }
        }),
        array('db' => 'ticket_number', 'dt' => 'ticket_number', 'formatter' => function ($d, $row) {
            if ($d == null || $d == '')
                return ' ';
            else {
                return $d;
            }
        }),
    );
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * If you just want to use the basic configuration for DataTables with PHP
     * server-side, there is no need to edit below this line.
     */
   // $_REQUEST['info']['qryCondition'] = $_REQUEST['info']['qryCondition'] . '99999';
    $input_data = isset($_REQUEST) ? $_REQUEST : exit;
    $dataTableServer = new DataTableServer();
    echo json_encode(
        $dataTableServer->simplePagination($input_data, $table, $columns)
    );
