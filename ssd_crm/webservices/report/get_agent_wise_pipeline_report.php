<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 5/6/2016
 * Time: 6:11 PM
 */



require_once "../lib/common.php";

$cn = connectDB();

//$today = date("Y-m-d");

$date_from = "2016-06-19 00:00:00";
$date_to = " 2016-06-19 23:59:59";

//$date_from = $today . " 00:00:00";
//$date_to = $today . " 23:59:59";

if (isset($_REQUEST) && $_REQUEST) {
    $data = $_REQUEST['info'];
    $date_from = $data["date_from"];
    $date_to = $data["date_to"];
}
$user_role = $_SESSION['user_role'];
$user_id = $_SESSION['user_id'];
$temp_array = array();


$select_qry = "SELECT 
    table_1.call_agent_name
    ,table_1.TotalCalls
    ,table_2.SuccessfulCalls
    ,table_3.SuccessfulNewCalls
    ,(table_2.SuccessfulCalls - table_3.SuccessfulNewCalls) AS FollowUpCalls
    ,IFNULL(table_4.Interested, 0) AS Interested
    ,IFNULL(table_5.VerbalConfirmation, 0) AS VerbalConfirmation
    ,IFNULL(table_6.Sales, 0) AS Sales
FROM
    (SELECT 
        call_agent_name, COUNT(contact_id) AS TotalCalls
    FROM
        call_history
    WHERE
        call_date >= '$date_from'
            AND call_date <= '$date_to'
    GROUP BY call_agent_name) table_1
        INNER JOIN
    (SELECT 
        call_agent_name, COUNT(contact_id) AS SuccessfulCalls
    FROM
        call_history
    WHERE
        stage_id <> '3'
            AND call_date >= '$date_from'
            AND call_date <= '$date_to'
    GROUP BY call_agent_name) table_2 ON table_1.call_agent_name = table_2.call_agent_name
        INNER JOIN
    (SELECT 
        call_agent_name,
            COUNT(DISTINCT contact_id) AS SuccessfulNewCalls
    FROM
        call_history
    WHERE
        stage_id <> '3'
            AND call_date >= '$date_from'
            AND call_date <= '$date_to'
            AND contact_id NOT IN (SELECT DISTINCT
                contact_id
            FROM
                call_history
            WHERE
                stage_id <> '3'
                    AND call_date < '$date_from')
    GROUP BY call_agent_name) table_3 ON table_1.call_agent_name = table_3.call_agent_name
        LEFT OUTER JOIN
    (SELECT 
        call_agent_name, COUNT(contact_id) AS Interested
    FROM
        call_history
    WHERE
        stage_id IN ('4' , '6')
            AND call_date >= '$date_from'
            AND call_date <= '$date_to'
    GROUP BY call_agent_name) table_4 ON table_1.call_agent_name = table_4.call_agent_name
        LEFT OUTER JOIN
    (SELECT 
        call_agent_name, COUNT(contact_id) AS VerbalConfirmation
    FROM
        call_history
    WHERE
        stage_id IN ('6' , '7')
            AND call_date >= '$date_from'
            AND call_date <= '$date_to'
    GROUP BY call_agent_name) table_5 ON table_1.call_agent_name = table_5.call_agent_name
        LEFT OUTER JOIN
    (SELECT 
        call_agent_name, COUNT(contact_id) AS Sales
    FROM
        call_history
    LEFT JOIN contacts ON call_history.contact_id = contacts.id
    WHERE
        contacts.stage_id = '7'
            AND call_date >= '$date_from'
            AND call_date <= '$date_to'
    GROUP BY call_history.call_agent_name) table_6 ON table_1.call_agent_name = table_6.call_agent_name";
    
$result = Sql_exec($cn, $select_qry);


while ($dt = Sql_fetch_array($result)) {
    $array = array('TotalCalls' => $dt['TotalCalls'], 'SuccessfulCalls' => $dt['SuccessfulCalls'], 'SuccessfulNewCalls' => $dt['SuccessfulNewCalls'], 'FollowUpCalls' => $dt['FollowUpCalls'], 'Interested' => $dt['Interested'], 'VerbalConfirmation' => $dt['VerbalConfirmation'], 'Sales' => $dt['Sales']);
    $temp_array[$dt['call_agent_name']] = $array;
    //$array = array('total' => $dt['total'], 'new' => $dt['new']);
   // $temp_array[$dt['call_agent_name']] = $array_one;
}
//var_dump($temp_array);

$data_array = array();
$i = 0;

foreach ($temp_array as $key => $values) {
    $j = 0;
    $data_array[$i][$j++] = $key;
    $data_array[$i][$j++] = isset($values['TotalCalls']) ? $values['TotalCalls'] : 0;
    $data_array[$i][$j++] = isset($values['SuccessfulCalls']) ? $values['SuccessfulCalls'] : 0;
    $data_array[$i][$j++] = isset($values['SuccessfulNewCalls']) ? $values['SuccessfulNewCalls'] : 0;
    $data_array[$i][$j++] = isset($values['FollowUpCalls']) ? $values['FollowUpCalls'] : 0;
    $data_array[$i][$j++] = isset($values['Interested']) ? $values['Interested'] : 0;
    $data_array[$i][$j++] = isset($values['VerbalConfirmation']) ? $values['VerbalConfirmation'] : 0;
    $data_array[$i][$j++] = isset($values['Sales']) ? $values['Sales'] : 0;

    $i++;
}

ClosedDBConnection($cn);

echo json_encode($data_array);

?>