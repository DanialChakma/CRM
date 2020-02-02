<?php
    /**
     * Created by PhpStorm.
     * User: Talemul
     * Date: 12-Apr-16
     * Time: 5:22 PM
     */
    require_once "../lib/common.php";

    $datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

    $cn = connectDB();
//var_dump($_SESSION); exit;


    $action_id = $datainfo["action_id"];
    // $notifyme = $datainfo['notifyme'];
    $id = $_SESSION["user_id"];
    $assign_to = $_SESSION['user_id'];
    $name = $_SESSION["first_name"] . ' ' . $_SESSION["last_name"];
    date_default_timezone_set("Asia/Dhaka");
//$date=date('Y-m-d H:i:s');
    $date = isset($datainfo["time"]) ? $datainfo["time"] : '';
    $industry_seg = isset($datainfo['industry_seg']) ? $datainfo['industry_seg'] : '';
    $corporate_stage = isset($datainfo['corporate_stage']) ? $datainfo['corporate_stage'] : '';
    $packaging = isset($datainfo['packaging']) ? $datainfo['packaging'] : '';
    $connection_type = isset($datainfo['connection_type']) ? $datainfo['connection_type'] : '';
    $other_service_charge = isset($datainfo['other_service_charge']) ? $datainfo['other_service_charge'] : '';
    $distributor_name = isset($datainfo['distributor_name']) ? $datainfo['distributor_name'] : '';
    $distributor_address = isset($datainfo['distributor_address']) ? $datainfo['distributor_address'] : '';
    $distributor_contact_no = isset($datainfo['distributor_contact_no']) ? $datainfo['distributor_contact_no'] : '';
    $retailer_name = isset($datainfo['retailer_name']) ? $datainfo['retailer_name'] : '';
    $retailer_address = isset($datainfo['retailer_address']) ? $datainfo['retailer_address'] : '';
    $retailer_contact_no = isset($datainfo['retailer_contact_no']) ? $datainfo['retailer_contact_no'] : '';

    $is_error = 0;

    $select_qry = "select count(*) as `count` from additional_field where contact_id='$action_id'";

    $result = Sql_exec($cn, $select_qry);

    $count = 0;


    while ($data = Sql_fetch_array($result)) {
        $count = $data['count'];
    }
    if ($count == 0) {
        $contact_qry = "INSERT INTO `additional_field` (  `contact_id`,  `industry_seg`,  `corporate_stage`,
  `packaging`,  `connection_type`,  `other_service_charge`,  `distributor_name`,  `distributor_address`,  `distributor_contact_no`,  `retailer_name`,  `retailer_address`,  `retailer_contact_no`,  `update_date`,  `updated_by`,  `assign_to`,`call_agent_name`) VALUES  (    
    '$action_id',    '$industry_seg',    '$corporate_stage',    '$packaging',    '$connection_type',    '$other_service_charge',    '$distributor_name',    '$distributor_address',    '$distributor_contact_no',
    '$retailer_name',    '$retailer_address',    '$retailer_contact_no',  '$date',
    '$assign_to',    '$assign_to' ,'$name' ) ;";
    } else {
        $contact_qry = "UPDATE `additional_field` SET `call_agent_name` = '$name',  `industry_seg` = '$industry_seg',  `corporate_stage` = '$corporate_stage',  `packaging` = '$packaging',  `connection_type` = '$connection_type',  `other_service_charge` = '$other_service_charge',  `distributor_name` = '$distributor_name',
  `distributor_address` = '$distributor_address',  `distributor_contact_no` = '$distributor_contact_no',
  `retailer_name` = '$retailer_name',  `retailer_address` = '$retailer_address',  `retailer_contact_no` = '$retailer_contact_no',   `update_date` = '$date',  `updated_by` = '$assign_to',
  `assign_to` = '$assign_to' where contact_id='$action_id'";
    }

    try {
        $res = Sql_exec($cn, $contact_qry);
    } catch (Exception $e) {
        $is_error = 1;
    }

    $contact_qry = "INSERT INTO `additional_field_history` (  `contact_id`,  `industry_seg`,  `corporate_stage`,
  `packaging`,  `connection_type`,  `other_service_charge`,  `distributor_name`,  `distributor_address`,  `distributor_contact_no`,  `retailer_name`,  `retailer_address`,  `retailer_contact_no`,  `update_date`,  `updated_by`,  `assign_to`,`call_agent_name`) VALUES  (    
    '$action_id',    '$industry_seg',    '$corporate_stage',    '$packaging',    '$connection_type',    '$other_service_charge',    '$distributor_name',    '$distributor_address',    '$distributor_contact_no',
    '$retailer_name',    '$retailer_address',    '$retailer_contact_no',  '$date',
    '$assign_to',    '$assign_to' ,'$name' ) ;";
    try {
        $res = Sql_exec($cn, $contact_qry);
    } catch (Exception $e) {
        $is_error = 1;
    }
    /*` industry_seg `,
        ` corporate_stage `,
        ` packaging `,
        ` connection_type `,
        ` other_service_charge `,
        ` distributor_name `,
        ` distributor_address `,
        ` distributor_contact_no `,
        ` retailer_name `,
        ` retailer_address `,
        ` retailer_contact_no `,
        ` create_date `,
        ` update_date `,
        ` updated_by `,
        ` assign_to `*/
    $explore = 0;
    $establish = 0;
    $evaluate = 0;
    $execute = 0;
    if ($corporate_stage == 1) {
        $explore = 1;
    } elseif ($corporate_stage == 2) {
        $establish = 1;
    } elseif ($corporate_stage == 3) {
        $evaluate = 1;
    } elseif ($corporate_stage == 4) {
        $execute = 1;
    }
    $large_company = 0;
    $bank_insurance = 0;
    $mnc = 0;
    $it_software_firm = 0;
    if ($industry_seg == 1) {
        $large_company = 1;
    } elseif ($industry_seg == 2) {
        $bank_insurance = 1;
    } elseif ($industry_seg == 3) {
        $mnc = 1;
    } elseif ($industry_seg == 4) {
        $it_software_firm = 1;
    }
    $select_qry = "select count(*) as 'count' from call_history where contact_id='$action_id'";

    $result = Sql_exec($cn, $select_qry);

    $count = 0;

    while ($data = Sql_fetch_array($result)) {
        $count = $data['count'];
    }

    $sum_of_total_call = 1;
    if ($count == 1) {
        $sum_of_new_visit = 1;
        $sum_of_follow_up = 0;

    } else {
        $sum_of_follow_up = 1;
        $sum_of_new_visit = 0;

    }

    $select_qry = "SELECT * FROM hourly_visit_report WHERE  agent='$assign_to' AND  DATE_FORMAT( hourly_visit_report.entry_date_time,'%Y-%m-%d %H')=DATE_FORMAT('$date','%Y-%m-%d %H')";

    $result = Sql_exec($cn, $select_qry);

    $id = 0;

    while ($data = Sql_fetch_array($result)) {
        $id = $data['id'];
    }
    if($id==0){
        $contact_qry = "INSERT INTO `hourly_visit_report` (    `entry_date_time`,  `agent_name`,  `sum_of_follow_up`,
  `sum_of_new_visit`,  `sum_of_total_call`,  `explore`,  `establish`,  `evaluate`,  `execute`,  `large_company`,  `bank_insurance`,  `mnc`,  `it_software_firm`,  `update_time`,  `agent`) 
VALUES  (     '$date',    '$name',    '$sum_of_follow_up',    '$sum_of_new_visit',    '$sum_of_total_call',
    '$explore',    '$establish',    '$evaluate',    '$execute',    '$large_company',    '$bank_insurance',    '$mnc',    '$it_software_firm',    '$date',    '$assign_to'  ) ;";
    }else{
        $contact_qry="UPDATE   `hourly_visit_report` SET  `sum_of_follow_up` = sum_of_follow_up+'$sum_of_follow_up',  
`sum_of_new_visit` = sum_of_new_visit+'$sum_of_new_visit',  `sum_of_total_call` = sum_of_total_call+'$sum_of_total_call',  `explore` = explore+'$explore',  `establish` = establish+'$establish',  `evaluate` =evaluate+ '$evaluate',  `execute` =`execute`+ '$execute',  `large_company` = large_company+'$large_company',
  `bank_insurance` =bank_insurance+ '$bank_insurance',  `mnc` = mnc+'$mnc',  `it_software_firm` = it_software_firm+'$it_software_firm', `update_time` = '$date' WHERE `id` = '$id' ";
    }

    try {
        $res = Sql_exec($cn, $contact_qry);
    } catch (Exception $e) {
        $is_error = 1;
    }

    ClosedDBConnection($cn);

    echo $is_error;