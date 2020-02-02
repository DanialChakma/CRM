<?php
    /**
     * Created by PhpStorm.
     * User: Nazibul
     * Date: 6/9/2015
     * Time: 4:49 PM
     */

    require_once "../lib/common.php";

    $datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
   // $time_start = microtime_float();
    $cn = connectDB();
/*    $time_end = microtime_float();
    $time = $time_end - $time_start;
    echo "DB conncetion time $time  ";*/
//var_dump($_SESSION); exit;

    $action_id = $datainfo["action_id"];

    $reslt_data = array();
  //  $time_start = microtime_float();
    $select_qry = "SELECT * FROM customer_conversion WHERE contact_id=$action_id order by id desc LIMIT 1";

    $res = Sql_exec($cn, $select_qry);/*
    $time_end = microtime_float();
    $time = $time_end - $time_start;
    echo "Run Query |SELECT * FROM customer_conversion WHERE contact_id=$action_id order by id desc LIMIT 1 | time: $time  ";*/
    while ($dt = Sql_fetch_array(($res))) {
        $reslt_data['id'] = $dt['id'];
        $reslt_data['conversion_date'] = $dt['conversion_date'];
        $reslt_data['conversion_agent'] = $dt['conversion_agent'];
        $reslt_data['conversion_status'] = $dt['conversion_status'];
        $reslt_data['conversion_note'] = $dt['conversion_note'];
        $reslt_data['client_type'] = $dt['client_type'];
        $reslt_data['collection_amount'] = $dt['collection_amount'];
        $reslt_data['install_cost'] = $dt['install_cost'];
        $reslt_data['monthly_cost'] = $dt['monthly_cost'];
        $reslt_data['month_number'] = $dt['month_number'];
        $reslt_data['package'] = $dt['package'];
        $reslt_data['collection_note'] = $dt['collection_note'];
        $reslt_data['assignment_date'] = $dt['assignment_date'];
        $reslt_data['paymode'] = $dt['payment_mode'];
        $reslt_data['paytype'] = $dt['payment_type'];
        $time = strtotime($dt['collection_date']);

        $reslt_data['collection_date'] = date('Y-m-d', $time);
        $reslt_data['collection_time'] = date('H:i:s', $time);
    }

    ClosedDBConnection($cn);

    echo json_encode($reslt_data);
    function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }