<?php

    require_once "../lib/common.php";
    // $time_start = microtime_float();
    $cn = connectDB();
    $data_array = array();
    /*    $time_end = microtime_float();
        $time = $time_end - $time_start;
        echo "DB conncetion time $time  ";*/
//var_dump($_REQUEST); exit;


    /*    http://103.239.252.134/subscriptionservices_test/services/subscriber/transactionhistory.php?appid=test&apppass=test&cmdid=SHOW_TRANSACTION_HISTORY&cmdparam=|2013-08-03|2016-01-23*/
    if (isset($_REQUEST)) {
        $data = $_REQUEST['info'];
        $doze_id = $data["doze_id"];
        $doze_id_check = substr($doze_id, 4);
    }
    if (strlen($doze_id)<6) {
        echo json_encode($data_array);
        exit;
    }
    // $time_start = microtime_float();
    $api_url = "http://localhost/subscriptionservices_test/services/subscriber/SubscriberService.php?appid=test&apppass=test&cmdid=SHOW_SUB_LIST&cmdparam=where+id='" . trim($doze_id_check) . "'";
    $response = file_get_contents_user_define($api_url);

    /*    $time_end = microtime_float();
        $time = $time_end - $time_start;
        echo "SHOW_SUB_LIST API CAll: $time  ";*/
    $resultResponse = explode("\n", $response);

    if ($resultResponse[0] == '+OK') {
        $result = explode("|", $resultResponse[2]);
        $length = $resultResponse[1];

        for ($i = 3; $i < ($length + 3); $i++) {
            $value = explode("|", $resultResponse[$i]);

            $arrayResult = array();
            for ($j = 0; $j < sizeof($value); $j++) {
                $arrayResult[$result[$j]] = $value[$j];
            }
        }
    }

    $uid = $arrayResult['uid'];

    $arrayResult = array();

    $today = date("Y-m-d");
    // $time_start = microtime_float();
    $api_url = "http://localhost/subscriptionservices_test/services/subscriber/transactionhistory.php?appid=test&apppass=test&cmdid=SHOW_TRANSACTION_HISTORY&cmdparam=" . $uid . "|2013-08-03|" . $today;

    $api_url = str_replace(" ", "", $api_url);

    $response = file_get_contents_user_define($api_url);
    /*    $time_end = microtime_float();
        $time = $time_end - $time_start;
        echo "SHOW_TRANSACTION_HISTORY API CAll: $time  ";*/
    $resultResponse = explode("\n", $response);


    $arrayResult = array();
    if ($resultResponse[0] == '+OK') {
        $result = explode("|", $resultResponse[2]);
        $length = $resultResponse[1];

        for ($i = 3; $i < ($length + 3); $i++) {
            $value = explode("|", $resultResponse[$i]);

            $arrayResult[$i - 3] = array();
            for ($j = 0; $j < sizeof($value); $j++) {
                $arrayResult[$i - 3][$result[$j]] = $value[$j];
            }
        }
    }


    $i = 0;
    for ($k = 0; $k < sizeof($arrayResult); $k++) {
        $j = 0;

        if ($arrayResult[$k]['Debit'] == '0.00' || trim($arrayResult[$k]['Debit']) == '') {
            continue;
        }
        $data_array[$i][$j++] = $arrayResult[$k]['TransactionDate'];
        $data_array[$i][$j++] = $arrayResult[$k]['Debit'];
        $data_array[$i][$j++] = $arrayResult[$k]['Reference'];
        $data_array[$i][$j++] = $arrayResult[$k]['Transactionid'];

        $i++;
    }

    ClosedDBConnection($cn);

    echo json_encode($data_array);

    function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());

        return ((float)$usec + (float)$sec);
    }