<?php

    require_once "../lib/common.php";

    $cn = connectDB();

    $today = isset($_REQUEST['info']['current_date_js']) ? $_REQUEST['info']['current_date_js'] : date("Y-m-d");

    $date_from = $today . " 00:00:00";
    $date_to = $today . " 23:59:59";

    if (isset($_REQUEST['info']) && $_REQUEST['info']) {
        $data = $_REQUEST['info'];
        $date_from = $data["date_from"];
        $date_to = $data["date_to"];
        $sales_representative = $data['sales_representative'];
    }

    $select_qry = "SELECT * FROM `hourly_visit_report` where entry_date_time BETWEEN '$date_from' and '$date_to'";

    if ($sales_representative != '-1' && $sales_representative > 0) {
        $select_qry = $select_qry . " and agent='$sales_representative'";
    }

    $result = Sql_exec($cn, $select_qry);
    $data_array=array(array());
    $i=0;
    while ($dt = Sql_fetch_array($result)) {
        $j = 0;
        $data_array[$i][$j++] = date('Y-m-d', strtotime($dt['entry_date_time']));
        $data_array[$i][$j++] = date('h A', strtotime($dt['entry_date_time']));
        $data_array[$i][$j++] = $dt['agent_name'];
        $data_array[$i][$j++] = $dt['sum_of_follow_up'];
        $data_array[$i][$j++] = $dt['sum_of_new_visit'];
        $data_array[$i][$j++] = $dt['sum_of_total_call'];
        $data_array[$i][$j++] = $dt['explore'];
        $data_array[$i][$j++] = $dt['establish'];
        $data_array[$i][$j++] = $dt['evaluate'];
        $data_array[$i][$j++] = $dt['execute'];
        $data_array[$i][$j++] = $dt['large_company'];
        $data_array[$i][$j++] = $dt['bank_insurance'];
        $data_array[$i][$j++] = $dt['mnc'];
        $data_array[$i][$j++] = $dt['it_software_firm'];
        $i++;

    }
    ClosedDBConnection($cn);

    echo json_encode($data_array);

