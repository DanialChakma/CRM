<?php
/**
Auto Scheduler script will be executed by cron job at the end of the day. At the night around 11 PM to 11:59 PM
Purpose of auto Scheduler is to mail today's unreachable call list.
 **/
$dirname = dirname(__FILE__);
$include_file = $dirname."/../lib/common.php";
include_once($include_file);
require_once "fin_lib.php";
session_destroy();
ignore_user_abort(true);
set_time_limit(0);
date_default_timezone_set("Asia/Dhaka");

$cn = connectDB();

$email_names_maps = array();
$qry = "SELECT email, customer_name, phone_no_p FROM cgw_customers WHERE email IN ( SELECT DISTINCT email FROM cgw_customers);";
$rs = Sql_exec($cn,$qry);
while( $dt = Sql_fetch_array($rs) ){
    $email_names_maps[trim($dt['email'])] = array(  "name"  => trim($dt['customer_name']),
                                                    "phone" => trim($dt['phone_no_p'])
                                            );
}

$unreachables = array();
$status_not_cond = "PAID";
$qry = "SELECT result_t.mbcl_id,mbcl.email,result_t.call_status FROM monthly_bill_call_list AS mbcl,
                  ( SELECT hist.mbcl_id,hist.call_status FROM monthly_bill_call_list_history AS hist,
	                ( SELECT mbcl_id, MAX(call_date) AS 'date' FROM monthly_bill_call_list_history WHERE DATE(call_date) = DATE(NOW()) GROUP BY mbcl_id ) AS temp
	                WHERE temp.mbcl_id = hist.mbcl_id AND temp.date = hist.call_date
                  ) AS result_t
                WHERE mbcl.id = result_t.mbcl_id AND mbcl.status <> 'PAID';";
$rs = Sql_exec($cn,$qry);
while( $dt = Sql_fetch_array($rs) ){
    $status = trim($dt['call_status']);
    if( $status === "Unreachable" ){
        $unreachables[] = trim($dt['email']);
    }
}

ClosedDBConnection($cn);



//
$to  = 'support@dozeinternet.com,abuzafar@ssd-tech.com,riasat@ssd-tech.com';
//subject
$subject = 'Today\'s Unreachable Doze Customer Call list.';
//To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
//Additional headers
$headers .= 'To:' . "\r\n";
$headers .= 'From: DozeCRM Auto Schedular.' . "\r\n";
$headers .= 'Cc:mustafa@ssd-tech.com,danial@ssd-tech.com' . "\r\n";
$headers .= 'Bcc:' . "\r\n";
//support@dozeinternet.com
//abuzafar@ssd-tech.com
//riasat@ssd-tech.com

$email_body = "<table><tbody><thead><tr><th>Name</th><th>Phone</th><th>Email</th></tr></thead>";

foreach( $unreachables as $email ){
    $name = $email_names_maps[$email]['name'];
    $phone = $email_names_maps[$email]['phone'];
    $email_body .= "<tr><td>".$name."</td><td>".$phone."</td><td>".$email."</td></tr>";
}

$email_body .= "</tbody></table>";

if( count($unreachables) > 0 ){
    mail($to, $subject, $email_body, $headers);
}

echo date('now')."<br>";


