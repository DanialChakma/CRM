<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 11/9/2016
 * Time: 2:48 PM
 */

require_once "../lib/common.php";
set_time_limit(0);
date_default_timezone_set("Asia/Dhaka");
$cn = connectDB();
$months = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
$current_year = date('Y');
$current_month = date('M');


$agents = array();

$talesales_users_qry = "SELECT user_id,CONCAT_WS(\" \",first_name,last_name ) AS 'usr_name' FROM user_info WHERE user_role = 'Sales Representative' OR user_role='Retail' AND user_status = 0 LIMIT 6";
$rs = Sql_exec($cn,$talesales_users_qry);
while($dt = mysql_fetch_assoc($rs)){
   $agents[$dt['user_id']] = trim($dt['usr_name']);
}
Sql_Free_Result($rs);
//print_r($agents);
$agent_wise_data = array();
$customer_type = "customer";

foreach($agents as $key => $value ){


    foreach( $months as $month ){

        if( $month == $current_month ){


            $day  =  date('d',strtotime($month.",".$current_year));
            $int_day = intval($day);

            if( $int_day == 1 ){
                $first_day_current_month_start = date("Y-m-01 00:00:00",strtotime( $month.",". $current_year ));
                $first_day_current_month_current = date("Y-m-01 H:m:i",strtotime( $month.",". $current_year ));
                $f_day_qry = "SELECT assign_to, COUNT(*) AS number FROM contacts
                              WHERE  customer_type='$customer_type' AND assign_to = $key AND
                                      update_date >= STR_TO_DATE('$first_day_current_month_start','%Y-%m-%d %H:%i:%s') AND update_date <= STR_TO_DATE('$first_day_current_month_current','%Y-%m-%d %H:%i:%s') AND stage_id = 7
                              ;";

             //   $f_day_qry = "CALL agent_wise_customer_count (".$key.",'".$first_day_current_month_start."','".$first_day_current_month_current."');";

                $rs = Sql_exec($cn,$f_day_qry);
                $dt = mysql_fetch_assoc($rs);
                $count = intval($dt['number']);
                $agent_wise_data[$key]["Today"] = $count;
               // Sql_Free_Result($rs);
            }else if( $int_day == 2 ){

                $first_day_current_month_start = date("Y-m-01 00:00:00",strtotime( $month.",". $current_year ));
                $first_day_current_month_end = date("Y-m-01 23:59:59",strtotime( $month.",". $current_year ));
                $f_day_qry = "SELECT assign_to, COUNT(*) AS number FROM contacts
                              WHERE  customer_type='$customer_type' AND assign_to = $key AND
                                      update_date >= STR_TO_DATE('$first_day_current_month_start','%Y-%m-%d %H:%i:%s') AND update_date <= STR_TO_DATE('$first_day_current_month_end','%Y-%m-%d %H:%i:%s') AND stage_id = 7
                              ;";

             //   $f_day_qry = "CALL agent_wise_customer_count (".$key.",'".$first_day_current_month_start."','".$first_day_current_month_end."');";

                $rs = Sql_exec($cn,$f_day_qry);
                $dt = mysql_fetch_assoc($rs);

                $count = intval($dt['number']);
                $agent_wise_data[$key][$month] = $count;
               // Sql_Free_Result($rs);

                $second_day_current_month_start = date("Y-m-02 00:00:00",strtotime( $month.",". $current_year ));
                $second_day_current_month_end = date("Y-m-d H:m:i",strtotime( $month.",". $current_year ));
                $s_day_qry = "SELECT assign_to, COUNT(*) AS number FROM contacts
                              WHERE  customer_type='$customer_type' AND assign_to = $key AND
                                      update_date >= STR_TO_DATE('$second_day_current_month_start','%Y-%m-%d %H:%i:%s') AND update_date <= STR_TO_DATE('$second_day_current_month_end','%Y-%m-%d %H:%i:%s') AND stage_id = 7
                              ;";

            //    $s_day_qry = "CALL agent_wise_customer_count (".$key.",'".$second_day_current_month_start."','".$second_day_current_month_end."');";

                $rs = Sql_exec($cn,$s_day_qry);
                $dt = mysql_fetch_assoc($rs);

                $count = intval($dt['number']);
                $agent_wise_data[$key]["Today"] = $count;
              //  Sql_Free_Result($rs);

            }else{

                if( $int_day <= 10 ){
                    $previous_day = "0".($int_day-1);
                } else{
                    $previous_day = $int_day-1;
                }
                $first_day_current_month_start = date("Y-m-01 00:00:00",strtotime( $month.",". $current_year ));
                $previous_day_current_month_end = date("Y-m-$previous_day 23:59:59",strtotime( $month.",". $current_year ));

                $f_day_qry = "SELECT assign_to, COUNT(*) AS number FROM contacts
                              WHERE  customer_type='$customer_type' AND assign_to = $key AND
                                      update_date >= STR_TO_DATE('$first_day_current_month_start','%Y-%m-%d %H:%i:%s') AND update_date <= STR_TO_DATE('$previous_day_current_month_end','%Y-%m-%d %H:%i:%s') AND stage_id = 7
                              ;";
              //  $f_day_qry = "CALL agent_wise_customer_count (".$key.",'".$first_day_current_month_start."','".$previous_day_current_month_end."');";
                $rs = Sql_exec($cn,$f_day_qry);
                $dt = mysql_fetch_assoc($rs);

                $count = intval($dt['number']);
                $agent_wise_data[$key][$month] = $count;
               // Sql_Free_Result($rs);

                $today_current_month_start = date("Y-m-d 00:00:00",strtotime( $month.",". $current_year ));
                $today_current_month_end = date("Y-m-d H:m:i",strtotime( $month.",". $current_year ));
                $s_day_qry = "SELECT assign_to, COUNT(*) AS number FROM contacts
                              WHERE  customer_type='$customer_type' AND assign_to = $key AND
                                      update_date >= STR_TO_DATE('$today_current_month_start','%Y-%m-%d %H:%i:%s') AND update_date <= STR_TO_DATE('$today_current_month_end','%Y-%m-%d %H:%i:%s') AND stage_id = 7
                              ;";
              //  $s_day_qry = "CALL agent_wise_customer_count (".$key.",'".$today_current_month_start."','".$today_current_month_end."');";
                $rs = Sql_exec($cn,$s_day_qry);
                $dt = mysql_fetch_assoc($rs);

                $count = intval($dt['number']);
                $agent_wise_data[$key]["Today"] = $count;
               // Sql_Free_Result($rs);
            }


            break;

        }else{

            $first_day_current_month = date("Y-m-01 00:00:00",strtotime( $month.",". $current_year ));
            $last_day_current_month  = date("Y-m-t 23:59:59",strtotime($month.",".$current_year));

            $qry = "SELECT assign_to, COUNT(*) AS number FROM contacts
            WHERE  customer_type='$customer_type' AND assign_to = $key AND
                   update_date >= STR_TO_DATE('$first_day_current_month','%Y-%m-%d %H:%i:%s') AND update_date <= STR_TO_DATE('$last_day_current_month','%Y-%m-%d %H:%i:%s') AND stage_id = 7
                   ;";
          //  $qry = "CALL agent_wise_customer_count (".$key.",'".$first_day_current_month."','".$last_day_current_month."');";
            $rs = Sql_exec($cn,$qry);
            $dt = Sql_fetch_array($rs);
            $count = intval($dt['number']);
            $agent_wise_data[$key][$month] = $count;
          //  Sql_Free_Result($rs);
        }


    }



}


$data = array();
$sl_number = 0;
$i=0;
foreach( $agents as $key=>$value ){

    $sl_number++;

    $j=0;
    $data[$i][$j++]=$sl_number;
    $data[$i][$j++]=$value;

    $m_key=array_search($current_month,$months);
    $day  =  date('d',strtotime($current_month.",".$current_year));
    $int_day = intval($day);
    $today_value = $agent_wise_data[$key]["Today"];
    $month_value = $agent_wise_data[$key][$current_month];
    if( empty($today_value) ){
        $today_value = 0;
    }
    if(empty($month_value)){
        $month_value = 0;
    }

    if( $int_day == 1 ){
        $data[$i][$j++] =  $today_value;
    }else{
        $data[$i][$j++] =  $today_value;
        $data[$i][$j++] =  $month_value;
    }

    for($m_key--;$m_key>=0;$m_key--){
        $m_val = $months[$m_key];
        $count_value = $agent_wise_data[$key][$m_val];
        if(empty($count_value)){
            $count_value = 0;
        }
        $data[$i][$j++] =  $count_value;
    }

    $i++;
}



$m_key=array_search($current_month,$months);

$today_total = 0;
foreach( $agents as $a_k=>$val ){
    $count_val = $agent_wise_data[$a_k]["Today"];
    if(empty($count_val)){
        $count_value = 0;
    }
    $today_total += $count_value;
}

$j=0;
$data[$i][$j++] = "Total";
$data[$i][$j++] = "";
$data[$i][$j++] = $today_total;

for(;$m_key>=0;$m_key--) {
    $month = $months[$m_key];
    $month_total = 0;
    foreach( $agents as $a_k=>$val ){
        $count_val = $agent_wise_data[$a_k][$month];
        if(empty($count_val)){
            $count_val = 0;
        }
        $month_total += $count_val;
    }

    $data[$i][$j++] = $month_total;
}

ClosedDBConnection($cn);
unset($agent_wise_data);
unset($agents);
$file = dirname(__FILE__)."/"."output.txt";
echo $string_output = json_encode($data);

file_put_contents($file,$string_output);