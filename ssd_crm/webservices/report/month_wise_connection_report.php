<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 11/1/2016
 * Time: 3:33 PM
 */
require_once "../lib/common.php";
date_default_timezone_set("Asia/Dhaka");
$cn = connectDB();
$months = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
$current_year = date('Y');
$data = array();

$channel_users = array();
$telesales_and_romoti_users = array();
$channel_user_qry = "SELECT user_id FROM user_info WHERE user_role = 'Channel' AND user_status = 0";
$rs = Sql_exec($cn,$channel_user_qry);
while($dt = mysql_fetch_assoc($rs)){
    $channel_users[]= $dt['user_id'];
}

$telesales_user_qry = "SELECT user_id FROM user_info WHERE user_role = 'Sales Representative' OR user_role='Retail' OR user_role = 'Admin' AND user_status = 0";
$rs = Sql_exec($cn,$telesales_user_qry);
while($dt = mysql_fetch_assoc($rs)){
    $telesales_and_romoti_users[] = $dt['user_id'];
}
$channel_user_str = implode(",",$channel_users);
$telesales_user_str = implode(",",$telesales_and_romoti_users);

$i=0;
foreach($months as $month){

    $first_day_of_month = date("Y-m-01 00:00:00",strtotime($month.",".$current_year));
    $last_day_of_month = date("Y-m-t 23:59:59",strtotime($month.",".$current_year));
    $customer_type = "customer";
    $qry = "SELECT `zone`,COUNT(*) AS number FROM contacts
            WHERE  customer_type='$customer_type' AND assign_to IN(".$telesales_user_str.") AND
                   update_date >= STR_TO_DATE('$first_day_of_month','%Y-%m-%d %H:%i:%s') AND update_date <= STR_TO_DATE('$last_day_of_month','%Y-%m-%d %H:%i:%s') AND stage_id = 7
            GROUP BY `zone`;";


    $do_wise_data = array();
    $rs = Sql_exec($cn,$qry);
    while($row = Sql_fetch_array($rs)){

        if( trim($row['zone']) == "2" ){
            $do_wise_data['Comilla'] = intval($row['number']);
        }

        if(  trim($row['zone']) == "3" ){
            $do_wise_data['Chittagong'] = intval($row['number']);
        }

        if( trim($row['zone']) == "1" ){
            if( empty($do_wise_data['Dhaka']) ){
                $do_wise_data['Dhaka']  = intval($row['number']);
            } else{
                $do_wise_data['Dhaka']  =  $do_wise_data['Dhaka'] + intval($row['number']);
            }
        }
    }

    $qry = "SELECT COUNT(*) AS number FROM contacts
            WHERE  customer_type='$customer_type' AND assign_to IN(".$channel_user_str.") AND
                   update_date >= STR_TO_DATE('$first_day_of_month','%Y-%m-%d %H:%i:%s') AND update_date <= STR_TO_DATE('$last_day_of_month','%Y-%m-%d %H:%i:%s') AND stage_id = 7";
    $rs = Sql_exec($cn,$qry);
    $dt = Sql_fetch_array($rs);
    $channel_num = $dt['number'];
    $do_wise_data['Channel'] = empty($channel_num) ? 0: $channel_num;
    $do_wise_data['Comilla'] = empty($do_wise_data['Comilla']) ? 0: $do_wise_data['Comilla'];
    $do_wise_data['Chittagong'] = empty($do_wise_data['Chittagong'])?0:$do_wise_data['Chittagong'];
    $do_wise_data['Dhaka'] = empty($do_wise_data['Dhaka'])?0:$do_wise_data['Dhaka'];
    $do_wise_data['total'] = $do_wise_data['Comilla'] +  $do_wise_data['Chittagong'] + $do_wise_data['Dhaka'] + $do_wise_data['Channel'];
    $do_wise_data['month'] = $month."-".substr($current_year,strlen($current_year)-2) ;
/*    echo "<pre>";
    print_r($do_wise_data);
    echo "</pre>";
    echo "First:".$first_day_of_month." "."Last:".$last_day_of_month."\n"; */
    $j=0;
    $data[$i][$j++] = $do_wise_data['month'];
    $data[$i][$j++] = $do_wise_data['total'];
    $data[$i][$j++] = $do_wise_data['Dhaka'];
    $data[$i][$j++] = $do_wise_data['Chittagong'];
    $data[$i][$j++] = $do_wise_data['Comilla'];
    $data[$i][$j++] = $do_wise_data['Channel'];
    $i++;

}

echo json_encode($data);

ClosedDBConnection($cn);