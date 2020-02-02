<?php
/**
 * Created by PhpStorm.
 * User: Talemul
 * Date: 12/8/2015
 * Time: 4:47 PM
 */


require_once "../lib/common.php";

$cn = connectDB();

$today = date("Y-m-d");

$date_from = $today . " 00:00:00";
$date_to = $today . " 23:59:59";

if (isset($_REQUEST) && $_REQUEST) {
    $data = $_REQUEST['info'];
    $date_from = $data["date_from"];
    $date_to = $data["date_to"];
}


$select_qry = "SELECT 
	(select first_name from user_info where user_id = contacts.assign_to) AS AgentName,
    table1.contact_id,
    contacts.lead_source AS Lead_Source,
    contacts.phone1 AS Lead_Contact_No,
    TRIM(CONCAT(contacts.first_name,' ',contacts.last_name)) AS CustomerName,
    MAX(Connected) AS ConnectedDate,
    MAX(Interested) AS InterestedDate,
    MAX(VC) AS VCDate,
    MAX(Sales_done) AS Sales_doneDate
FROM
    (SELECT 
        contact_id,
            CASE
                WHEN Dates = 'Connected' THEN Call_date
                ELSE ''
            END AS Connected,
            CASE
                WHEN Dates = 'Interested' THEN Call_date
                ELSE ''
            END AS Interested,
            CASE
                WHEN Dates = 'VC' THEN Call_date
                ELSE ''
            END AS VC,
            CASE
                WHEN Dates = 'Sales Done' THEN Call_date
                ELSE ''
            END AS Sales_done
    FROM
        vw_conversion_cycle
    WHERE
        contact_id IN (SELECT DISTINCT
                contact_id
            FROM
                call_history
            WHERE
                stage_id = 7
                    AND call_date BETWEEN '$date_from' and '$date_to')) AS table1
        LEFT JOIN
    contacts ON table1.contact_id = contacts.id
GROUP BY contact_id ";

$result = Sql_exec($cn, $select_qry);

$data_array = array();
$i = 0;
$serial = 0;

while ($dt = Sql_fetch_array($result)) {
    $j = 0;
    
    $data_array[$i][$j++] = date('d-M-Y', strtotime($dt['Sales_doneDate'])); //1 Sales Done Date
    $data_array[$i][$j++] = $dt['AgentName']; //2 Agent Name
    $data_array[$i][$j++] = $dt['Lead_Source'];  //3 Lead Source
    $data_array[$i][$j++] = $dt['Lead_Contact_No']; //4 Lead contact no
    $data_array[$i][$j++] = $dt['CustomerName']; //5 Lead Customer Name
    $data_array[$i][$j++] = date('d-M-Y', strtotime($dt['ConnectedDate'])); //6 Connection date
    
    //7 duration dates between first call date and interested date
    if(($dt['InterestedDate']) == '')
        $data_array[$i][$j++] = '0';
    else{
        //$data_array[$i][$j++] = date_diff(date_create($dt['InterestedDate']), date_create($dt['ConnectedDate']))->format("%a");
        $data_array[$i][$j++] =(strtotime(date('d-M-Y', strtotime($dt['ConnectedDate']))) -strtotime(date('d-M-Y', strtotime($dt['ConnectedDate']))))/(60*60*24);
    }


    //8 first interested date
    if(($dt['InterestedDate']) <> '')
        $data_array[$i][$j++] = date('d-M-Y', strtotime($dt['InterestedDate']));
    else
        $data_array[$i][$j++] = date('d-M-Y', strtotime($dt['ConnectedDate']));
  
    
    //9 duration dates between previous state and VC date  -VC Day
    if(($dt['VCDate']) <> '' && ($dt['InterestedDate']) <> '')
        //$data_array[$i][$j++] = date_diff(date_create($dt['VCDate']), date_create($dt['InterestedDate']))->format("%a");
        $data_array[$i][$j++] =(strtotime(date('d-M-Y', strtotime($dt['VCDate']))) -strtotime(date('d-M-Y', strtotime($dt['InterestedDate']))))/(60*60*24);
    elseif(($dt['VCDate']) <> '' && ($dt['InterestedDate']) == '')
        $data_array[$i][$j++] =(strtotime(date('d-M-Y', strtotime($dt['VCDate']))) -strtotime(date('d-M-Y', strtotime($dt['ConnectedDate']))))/(60*60*24);
        //$data_array[$i][$j++] = date_diff(date_create($dt['VCDate']), date_create($dt['ConnectedDate']))->format("%a");
        
    else
        $data_array[$i][$j++] = '0';
    
    //10 VC date
    if($dt['VCDate'] <> ''){
        $data_array[$i][$j++] = date('d-M-Y', strtotime($dt['VCDate']));
    }
    elseif($dt['InterestedDate'] <> '')
        $data_array[$i][$j++] = date('d-M-Y', strtotime($dt['InterestedDate']));
    else
        $data_array[$i][$j++] = date('d-M-Y', strtotime($dt['ConnectedDate']));
    
    
    //11 duration dates between previous state and Sales done

    if($dt['VCDate'] <> '')
        //$data_array[$i][$j++] = date_diff(date_create($dt['Sales_doneDate']), date_create($dt['VCDate']))->format("%a");
        $data_array[$i][$j++] =(strtotime(date('d-M-Y', strtotime($dt['Sales_doneDate']))) -strtotime(date('d-M-Y', strtotime($dt['VCDate']))))/(60*60*24);
    elseif($dt['InterestedDate'] <> '')
        $data_array[$i][$j++] =(strtotime(date('d-M-Y', strtotime($dt['Sales_doneDate']))) -strtotime(date('d-M-Y', strtotime($dt['InterestedDate']))))/(60*60*24);
        //$data_array[$i][$j++] = date_diff(date_create($dt['Sales_doneDate']), date_create($dt['InterestedDate']))->format("%a");
    else
        //$data_array[$i][$j++] = date_diff(date_create($dt['Sales_doneDate']), date_create($dt['ConnectedDate']))->format("%a");
        $data_array[$i][$j++] =(strtotime(date('d-M-Y', strtotime($dt['Sales_doneDate']))) -strtotime(date('d-M-Y', strtotime($dt['ConnectedDate']))))/(60*60*24);

        
    
    
    //12 Sales Done date
    $data_array[$i][$j++] = date('d-M-Y', strtotime($dt['Sales_doneDate']));
    
    //13 Total Date takes to convert a full cycle
    
    $data_array[$i][$j++] =(strtotime(date('d-M-Y', strtotime($dt['Sales_doneDate']))) -strtotime(date('d-M-Y', strtotime($dt['ConnectedDate']))))/(60*60*24);
        
    

    $i++;
    $serial++;
}

ClosedDBConnection($cn);

echo json_encode($data_array);