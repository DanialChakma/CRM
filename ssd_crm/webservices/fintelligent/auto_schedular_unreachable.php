<?php
/**
 Auto Scheduler script will be executed by cron job at the end of the day. At the night around 11 PM to 11:59 PM
 Purpose of auto Scheduler is to update monthly call list table row.
 **/
require_once "../lib/common.php";
require_once "fin_lib.php";
session_destroy();

ignore_user_abort(true);
set_time_limit(0);
date_default_timezone_set("Asia/Dhaka");
$current_date = date("Y-m-d");
$next_date = date('Y-m-d', strtotime(' +1 day'))." "."00:00:00";
//$call_status = "Unreachable";
$cn = connectDB();

$qry = "SELECT grouped_mbt.*,MAX(grouped_mbt.call_date)
              FROM (
                      SELECT id,mbcl_id,contact_id,call_status,call_outcome,next_follow_up_date,call_date
                      FROM monthly_bill_call_list_history
                      WHERE  DATE( call_date ) = '$current_date'
                      ORDER BY call_date DESC
              ) AS grouped_mbt
        GROUP BY grouped_mbt.mbcl_id
        ORDER BY grouped_mbt.call_date DESC;";

$update_founds = array();
$rs = Sql_exec($cn,$qry);
while($dt = Sql_fetch_array($rs)){

    $mb_id = $dt['mbcl_id'];
    $contact_id = $dt['contact_id'];
    $call_status = trim($dt['call_status']);
    $call_outcome = trim($dt['call_outcome']);
    $f_up_date = trim($dt['next_follow_up_date']);

    $update_founds[] = array(
                              'mb_id'    =>  $mb_id,
                              'contact'  =>  $contact_id,
                              'call_status' => $call_status,
                              'call_outcome' => $call_outcome,
                              'f_up_date'   => $f_up_date
                        );
}

Sql_Free_Result($rs);

$count = count($update_founds);
$execution_messages = array();
for( $i=0; $i<$count; $i++ ){

        $mb_id = $update_founds[$i]['mb_id'];
        $contact = $update_founds[$i]['contact'];
        $call_status = $update_founds[$i]['call_status'];
        $call_outcome = $update_founds[$i]['call_outcome'];
        $f_up_date = $update_founds[$i]['f_up_date'];
        $qry_str = "";
        if( $call_status == "Connected" ){
            if( $call_outcome == "1" ){
                // call_outcome 1 means follow up. Set call_date = follow_up_date
                 $qry_str = "UPDATE monthly_bill_call_list SET `call_date` = '$f_up_date' WHERE `id` = '$mb_id' AND `contact_id` = '$contact';";
            }
        }else if( $call_status == "Unreachable" ){
                $qry_str = "UPDATE monthly_bill_call_list SET `call_date` = '$next_date' WHERE `id` = '$mb_id' AND `contact_id` = '$contact';";
        }else{}

        if( $qry_str != "" ){
            $rs = Sql_exec_continue($cn,$qry_str);
            if( $rs ){
                $execution_messages[]= array("msg"=>"Success","qry"=>$qry_str);
            }else{
                $execution_messages[]= array("msg"=>"Error","qry"=>$qry_str);
            }
        }
        unset($update_founds[$i]);
}

unset($update_founds);

echo "<pre>";
print_r($execution_messages);
echo "</pre>";
unset($execution_messages);
ClosedDBConnection($cn);
exit;