<?php

require_once "../lib/common.php";

$cn = connectDB();

$today = date("Y-m-d");

$date_from = $today." 00:00:00";
$date_to = $today." 23:59:59";


if (isset($_REQUEST) && $_REQUEST) {
    $data = $_REQUEST['info'];
    $date_from = $data["date_from"];
    $date_to = $data["date_to"];
    $sales_representative = $data['sales_representative'];
}

if(trim($sales_representative) !='-1'){
    $select_qry = "SELECT DISTINCT
    contacts.customer_type AS customer_type,
    contacts.next_call_date AS next_call_date,
    contacts.first_name AS cfname,
    contacts.last_name AS clname,
    contacts.phone1 AS phone1,
    contacts.phone2 AS phone2,
    call_history.call_agent_name AS call_agent_name,
    call_history.call_date AS call_date,
    call_history.feedback AS 'feedback',
    table_1.CallType AS CallType,
    TIME_FORMAT(SEC_TO_TIME(total_duration),
            '%Hhr : %imin : %ssec') AS 'call_duration',
    (SELECT 
            select_stage.stage
        FROM
            select_stage
        WHERE
            select_stage.id = (IF(call_history.stage_id = 0,
                contacts.stage_id,
                call_history.stage_id))) AS 'note'
FROM
    call_history,
    contacts,
    (SELECT 
        T_table.id,
            T_table.contact_id,
            CASE
                WHEN
                    (T_table.id , T_table.contact_id) IN (SELECT 
                            MIN(id), contact_id
                        FROM
                            call_history
                        WHERE
                            contact_id = T_table.contact_id
                                AND stage_id <> '3')
                THEN
                    'NewCall'
                ELSE 'FollowUpCalls'
            END AS CallType
    FROM
        call_history T_table
    WHERE
        stage_id <> '3'
            AND call_date >= '$date_from'
            AND call_date <= '$date_to') table_1
WHERE
    call_history.id = table_1.id
        AND call_history.contact_id = contacts.id
        AND call_history.call_agent = '$sales_representative'
        AND call_history.call_date >= '$date_from'
        AND call_history.call_date <= '$date_to'
ORDER BY call_history.call_date DESC";
} else {
    $select_qry = "SELECT DISTINCT
    contacts.customer_type AS customer_type,
    contacts.next_call_date AS next_call_date,
    contacts.first_name AS cfname,
    contacts.last_name AS clname,
    contacts.phone1 AS phone1,
    contacts.phone2 AS phone2,
    call_history.id,
    call_history.contact_id,
    call_history.call_agent_name AS call_agent_name,
    call_history.call_date AS call_date,
    call_history.feedback AS 'feedback',
    table_1.CallType AS CallType,
    TIME_FORMAT(SEC_TO_TIME(total_duration),
            '%Hhr : %imin : %ssec') AS 'call_duration',
    (SELECT 
            select_stage.stage
        FROM
            select_stage
        WHERE
            select_stage.id = (IF(call_history.stage_id = 0,
                contacts.stage_id,
                call_history.stage_id))) AS 'note'
FROM
    call_history,
    contacts,
    (SELECT 
        T_table.id,
            T_table.contact_id,
            CASE
                WHEN
                    (T_table.id , T_table.contact_id) IN (SELECT 
                            MIN(id), contact_id
                        FROM
                            call_history
                        WHERE
                            contact_id = T_table.contact_id
                                AND stage_id <> '3')
                THEN
                    'NewCall'
                ELSE 'FollowUpCalls'
            END AS CallType
    FROM
        call_history T_table
    WHERE
        stage_id <> '3'
            AND call_date >= '$date_from'
            AND call_date <= '$date_to') table_1
WHERE
    call_history.id = table_1.id
        AND call_history.contact_id = contacts.id
        AND call_history.call_date >= '$date_from'
        AND call_history.call_date <= '$date_to'
ORDER BY call_history.call_date DESC";}


//echo $select_qry; exit;

$result = Sql_exec($cn,$select_qry);
//echo $select_qry;
$data_array = array();
$i = 0;
$serial = 1;
//echo $select_qry; exit;
while($dt = Sql_fetch_array($result)){
    $j = 0;

    $data_array[$i][$j++] = $serial;
    $data_array[$i][$j++] = $dt['call_agent_name'];
    $data_array[$i][$j++] = $dt['cfname']. " ".$dt['clname']."(".$dt['phone1'].")";
    $call_date_time = explode(" ",$dt['call_date']);
    $call_date = $call_date_time[0];
    $call_time = $call_date_time[1];
    $data_array[$i][$j++] = $call_date;
    $data_array[$i][$j++] = $call_time;
    $data_array[$i][$j++] = $dt['customer_type'];
    $next_date_time = explode(" ",$dt['next_call_date']);
    $next_date = $next_date_time[0];
    $data_array[$i][$j++] = $next_date;
    $data_array[$i][$j++] = $dt['feedback'];
    $data_array[$i][$j++] = $dt['CallType'];
    $data_array[$i][$j++] = $dt['note'];
    $data_array[$i][$j++] = $dt['call_duration'];
    $i++;
    $serial++;
   // print_r($data_array[$i-1]);
}

ClosedDBConnection($cn);


echo json_encode($data_array);