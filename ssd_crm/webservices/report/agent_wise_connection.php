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

$talesales_users_qry = "SELECT user_id,CONCAT_WS(\" \",first_name,last_name ) AS 'usr_name' FROM user_info WHERE user_role = 'Sales Representative' OR user_role='Retail' AND user_status = 0";
$rs = Sql_exec($cn,$talesales_users_qry);
while($dt = mysql_fetch_assoc($rs)){
    $agents[$dt['user_id']] = trim($dt['usr_name']);
}
Sql_Free_Result($rs);
//print_r($agents);
$agent_wise_data = array();
$customer_type = "customer";

$months_day_range = array();

foreach( $months as $month ){

    if( $month == $current_month ){

        $day  =  date('d',strtotime($month.",".$current_year));
        $int_day = intval($day);
        if( $int_day == 1 ){
            $first_day_current_month_start = date("Y-m-01 00:00:00",strtotime( $month.",". $current_year ));
            $first_day_current_month_current = date("Y-m-01 H:m:i",strtotime( $month.",". $current_year ));

            $months_day_range["Today"] = array( 'first' => $first_day_current_month_start,'last' => $first_day_current_month_current );
        } else if( $int_day == 2 ){
            $first_day_current_month_start = date("Y-m-01 00:00:00",strtotime( $month.",". $current_year ));
            $first_day_current_month_end = date("Y-m-01 23:59:59",strtotime( $month.",". $current_year ));

            $months_day_range[$month] = array( 'first' => $first_day_current_month_start,'last' => $first_day_current_month_end );

            $second_day_current_month_start = date("Y-m-d 00:00:00",strtotime( $month.",". $current_year ));
            $second_day_current_month_end = date("Y-m-d H:m:i",strtotime( $month.",". $current_year ));

            $months_day_range["Today"] = array( 'first' => $second_day_current_month_start,'last' => $second_day_current_month_end );
        }else{

            if( $int_day <= 10 ){
                $previous_day = "0".($int_day-1);
            } else{
                $previous_day = $int_day-1;
            }

            $first_day_current_month_start = date("Y-m-01 00:00:00",strtotime( $month.",". $current_year ));
            $previous_day_current_month_end = date("Y-m-$previous_day 23:59:59",strtotime( $month.",". $current_year ));
            $months_day_range[$month] = array( 'first' => $first_day_current_month_start,'last' => $previous_day_current_month_end );



            $today_current_month_start = date("Y-m-d 00:00:00",strtotime( $month.",". $current_year ));
            $today_current_month_end = date("Y-m-d H:m:i",strtotime( $month.",". $current_year ));
            $months_day_range["Today"] = array( 'first' => $today_current_month_start,'last' => $today_current_month_end );

        }
        break;
    }else{
        $first_day_current_month = date("Y-m-01 00:00:00",strtotime( $month.",". $current_year ));
        $last_day_current_month  = date("Y-m-t 23:59:59",strtotime($month.",".$current_year));
        $months_day_range[$month] = array('first'=>$first_day_current_month,'last'=>$last_day_current_month);
    }
}


$usr_ids = array_keys($agents);
$usr_id_str = implode(",",$usr_ids);

foreach( $months_day_range as $month=>$ranges ){
    $f_date = $ranges['first'];
    $l_date = $ranges['last'];
    if( $month == "Today" ){
        $qry = "SELECT assign_to, COUNT(*) AS number FROM contacts
                WHERE customer_type='$customer_type' AND assign_to IN(".$usr_id_str.") AND stage_id = 7 AND
                      update_date >= STR_TO_DATE('$f_date','%Y-%m-%d %H:%i:%s') AND update_date <= STR_TO_DATE('$l_date','%Y-%m-%d %H:%i:%s')
                GROUP BY assign_to;";
        $rs = Sql_exec($cn,$qry);
        while($dt = mysql_fetch_assoc($rs)){
            $count = intval($dt['number']);
            if(empty($count)){
                $count = 0;
            }
            $agent_wise_data[$dt['assign_to']][$month] = $count;
        }
    }else{
        $qry = "SELECT assign_to, COUNT(*) AS number FROM contacts
                WHERE customer_type='$customer_type' AND assign_to IN(".$usr_id_str.") AND stage_id = 7 AND
                      update_date >= STR_TO_DATE('$f_date','%Y-%m-%d %H:%i:%s') AND update_date <= STR_TO_DATE('$l_date','%Y-%m-%d %H:%i:%s')
                GROUP BY assign_to;";
        $rs = Sql_exec($cn,$qry);
        while($dt = mysql_fetch_assoc($rs)){
            $count = intval($dt['number']);
            if(empty($count)){
                $count = 0;
            }
            $agent_wise_data[$dt['assign_to']][$month] = $count;
        }
    }
}






$total_data = array();

$data = array();
$sl_number = 1;
$i=0;
$months_day_range = array_reverse($months_day_range);
foreach($agents as $usr_id=>$name){
    $j=0;
    $data[$i][$j++]= $sl_number;
    $data[$i][$j++]= $name;
    foreach( $months_day_range as $month=>$ranges ){
        $count = $agent_wise_data[$usr_id][$month];
        $data[$i][$j++]=$count;
        if(empty($total_data[$month])){
            $total_data[$month] = $count;
        }else{
            $total_data[$month] += $count;
        }
    }
    $i++;
    $sl_number++;
}

$j=0;
$data[$i][$j++]="Sub Total";
$data[$i][$j++]="";
foreach( $months_day_range as $month=>$ranges ){

    $data[$i][$j++]= $total_data[$month];
}

ClosedDBConnection($cn);
unset($total_data);
unset($agent_wise_data);
unset($agents);

echo $string_output = json_encode($data);
