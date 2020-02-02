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

   if(trim($sales_representative) !='-1'){
        $select_qry="SELECT 
    HOUR(call_history.call_date) AS Call_Time,
    COUNT(call_history.id) AS Total_Calls,
    COUNT(IF(vw_callType.CallType = 'NewCall',
        vw_callType.T_id,
        NULL)) AS NewCalls,
    COUNT(IF(call_history.previous_stage_id = 5,
        call_history.id,
        NULL)) AS PreviousNotInterestedCalls,
    COUNT(IF(call_history.previous_stage_id = 4,
        call_history.id,
        NULL)) AS PreviousInterestedCalls,
    COUNT(IF(call_history.previous_stage_id = 6,
        call_history.id,
        NULL)) AS PreviousVCCalls,
    COUNT(DISTINCT IF(vw_callType.CallType = 'NewCall',
            vw_callType.T_contact_id,
            NULL)) AS NewContacts,
    COUNT(DISTINCT IF(call_history.previous_stage_id = 5,
            call_history.contact_id,
            NULL)) AS PreviousNotInterestedContacts,
    COUNT(DISTINCT IF(call_history.previous_stage_id = 4,
            call_history.contact_id,
            NULL)) AS PreviousInterestedContacts,
    COUNT(DISTINCT IF(call_history.previous_stage_id = 6,
            call_history.contact_id,
            NULL)) AS PreviousVCContacts,
    COUNT(DISTINCT IF(call_history.stage_id = 5,
            call_history.contact_id,
            NULL)) AS NotInterestedContacts,
    COUNT(DISTINCT IF(call_history.stage_id = 4,
            call_history.contact_id,
            NULL)) AS InterestedContacts,
    COUNT(DISTINCT IF(call_history.stage_id = 6,
            call_history.contact_id,
            NULL)) AS VCContacts,
    COUNT(DISTINCT IF(call_history.stage_id = 7,
            call_history.contact_id,
            NULL)) AS SalesDone,
    COUNT(DISTINCT call_history.call_agent_name) AS AgentCount
FROM
    call_history
        INNER JOIN
    vw_callType ON call_history.id = vw_callType.T_id
WHERE
    call_history.stage_id <> '3'
        AND call_history.call_agent = '$sales_representative'
        AND call_history.call_date BETWEEN '$date_from' AND '$date_to'
GROUP BY HOUR(call_history.call_date)";
   }
else {
    $select_qry="SELECT 
    HOUR(call_history.call_date) AS Call_Time,
    COUNT(call_history.id) AS Total_Calls,
    COUNT(IF(vw_callType.CallType = 'NewCall',
        vw_callType.T_id,
        NULL)) AS NewCalls,
    COUNT(IF(call_history.previous_stage_id = 5,
        call_history.id,
        NULL)) AS PreviousNotInterestedCalls,
    COUNT(IF(call_history.previous_stage_id = 4,
        call_history.id,
        NULL)) AS PreviousInterestedCalls,
    COUNT(IF(call_history.previous_stage_id = 6,
        call_history.id,
        NULL)) AS PreviousVCCalls,
    COUNT(DISTINCT IF(vw_callType.CallType = 'NewCall',
            vw_callType.T_contact_id,
            NULL)) AS NewContacts,
    COUNT(DISTINCT IF(call_history.previous_stage_id = 5,
            call_history.contact_id,
            NULL)) AS PreviousNotInterestedContacts,
    COUNT(DISTINCT IF(call_history.previous_stage_id = 4,
            call_history.contact_id,
            NULL)) AS PreviousInterestedContacts,
    COUNT(DISTINCT IF(call_history.previous_stage_id = 6,
            call_history.contact_id,
            NULL)) AS PreviousVCContacts,
    COUNT(DISTINCT IF(call_history.stage_id = 5,
            call_history.contact_id,
            NULL)) AS NotInterestedContacts,
    COUNT(DISTINCT IF(call_history.stage_id = 4,
            call_history.contact_id,
            NULL)) AS InterestedContacts,
    COUNT(DISTINCT IF(call_history.stage_id = 6,
            call_history.contact_id,
            NULL)) AS VCContacts,
    COUNT(DISTINCT IF(call_history.stage_id = 7,
            call_history.contact_id,
            NULL)) AS SalesDone,
    COUNT(DISTINCT call_history.call_agent_name) AS AgentCount
FROM
    call_history
        INNER JOIN
    vw_callType ON call_history.id = vw_callType.T_id
WHERE
    call_history.stage_id <> '3'
        AND call_history.call_date BETWEEN '$date_from' AND '$date_to'
GROUP BY HOUR(call_history.call_date)";
}


$result = Sql_exec($cn,$select_qry);
//echo $select_qry;
$data_array = array();
$i = 0;
$serial = 1;
while($dt = Sql_fetch_array($result)){
    $j = 0;
    $data_array[$i][$j++] = $dt['Call_Time'].':00 to '.$dt['Call_Time'].':59';
    $data_array[$i][$j++] = date('d-M-Y', strtotime($date_from));   
    $data_array[$i][$j++] = $dt['Total_Calls'];
    $data_array[$i][$j++] = $dt['NewCalls'];
    $data_array[$i][$j++] = $dt['PreviousNotInterestedCalls'];
    $data_array[$i][$j++] = $dt['PreviousInterestedCalls'];
    $data_array[$i][$j++] = $dt['PreviousVCCalls'];
    
    $data_array[$i][$j++] = $dt['NewContacts'];
    $data_array[$i][$j++] = $dt['PreviousNotInterestedContacts'];
    $data_array[$i][$j++] = $dt['PreviousInterestedContacts'];
    $data_array[$i][$j++] = $dt['PreviousVCContacts'];
    
    $data_array[$i][$j++] = $dt['NotInterestedContacts'];
    $data_array[$i][$j++] = $dt['InterestedContacts'];
    $data_array[$i][$j++] = $dt['VCContacts'];
    $data_array[$i][$j++] = $dt['SalesDone'];
    $data_array[$i][$j++] = $dt['AgentCount'];
    $i++;
    $serial++;
    
}

  ClosedDBConnection($cn);

 echo json_encode($data_array);