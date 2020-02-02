<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 9/18/2016
 * Time: 8:33 PM
 */
/*
<option value="1">Followup</option>
<option value="2">Not Interested</option>
<option value="3">Payment via Bank</option>
<option value="4">Payment via Online</option>
<option value="5">Payment via eCourier</option>
*/

require_once "../lib/common.php";
require_once "fin_lib.php";

date_default_timezone_set("Asia/Dhaka");
$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

$cn = connectDB();

$transaction_id = substr(uniqid("DB",true),0,16);
$current_date_time = date("Y-m-d H:i:s");
$user_name    = trim($_SESSION['user_name']);
$mbcl_id      = $datainfo['mbcl_id'];
$contact_id   = $datainfo['contact_id'];// contact_id is email
$call_status  = $datainfo['call_status'];
$outcome      = $datainfo['outcome'];
$follow_up_date = $datainfo['follow_up_date'];
$payment_mode   = $datainfo['paymentmethod'];
$payment_date   = $datainfo['payment_date'];
$feedback       = mysql_real_escape_string(nl2br($datainfo['feedback']));
$outcome = !empty($outcome) ? $outcome : "";
$follow_up_date = !empty($follow_up_date) ? $follow_up_date : "0000-00-00 00:00:00";
$payment_date   = !empty($payment_date) ? $payment_date : "0000-00-00 00:00:00";
$feedback = $feedback ? $feedback:"";


$db_transaction_id = check_transition_id_exist($cn,$mbcl_id,$contact_id);

if( $db_transaction_id ){
    $transaction_id = $db_transaction_id;
}


$err_status = 0;
if( !empty($outcome) ){

    if( $outcome == "1" ){
        // outcome 1 means follow up date
        $qry_monthly_call = "UPDATE monthly_bill_call_list
                             SET  `call_date` = '$follow_up_date',
                                  `update_count` = IFNULL(update_count, 0)+1,
                                  `status` = '$outcome',
                                  `remarks` = '$feedback'
                             WHERE `email` = '$contact_id' AND `id` = '$mbcl_id';";
    }else if( $outcome == "3" || $outcome == "4" ){

        if( $outcome == "3" ){
            // payment today
            $payment_date = date("Y-m-d")." 00:00:00";
        }
        if( $payment_mode != "3" ){
            // payment via (bank,online) other than eCourier
            $transaction_id = "";
        }

        $qry_monthly_call = "UPDATE monthly_bill_call_list
                             SET  `transaction_id` = '$transaction_id',
                                  `update_count` = IFNULL(update_count, 0)+1,
                                  `payment_date` = '$payment_date',
                                  `status` = '$outcome',
                                  `remarks` = '$feedback',
				                  `payment_method`='$payment_mode'
                             WHERE `email` = '$contact_id' AND `id` = '$mbcl_id';";
    }else{
        $qry_monthly_call = "UPDATE monthly_bill_call_list
                             SET
                                 `update_count` = IFNULL(update_count, 0)+1,
                                 `status` = '$outcome',
                                 `remarks` = '$feedback'
                             WHERE `email` = '$contact_id' AND id = '$mbcl_id';";
    }

    $rs = Sql_exec($cn,$qry_monthly_call);
    if($rs){
        $err_status = 1;
    }else{
        $err_status = 3;
    }
   
}else{
    $err_status = 1;
}


$qry = "INSERT INTO monthly_bill_call_list_history ( mbcl_id,email, agent_name, feedback, call_date, next_follow_up_date, call_status, call_outcome,payment_method,payment_date )
        VALUES ( '$mbcl_id','$contact_id', '$user_name', '$feedback','$current_date_time','$follow_up_date','$call_status','$outcome','$payment_mode','$payment_date' );";

if( $err_status === 1 ){
    $rs = Sql_exec($cn,$qry);
    if($rs){
        $err_status = 1;
    }else{
        $err_status = 4;
    }
}else{
    $err_status = 5;
}


if( $err_status === 1 ){
    echo json_encode(array(
        "status"=>$err_status,
        "msg"=>"Operation Successful"
    ));
}else{
    echo json_encode(array(
        "status"=>$err_status,
        "msg"=>"Operation Failed!"
    ));
}

ClosedDBConnection($cn);