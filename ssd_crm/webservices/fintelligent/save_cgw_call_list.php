<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 11/22/2016
 * Time: 9:56 PM
 */

require_once "../lib/common.php";
require_once "fin_lib.php";
date_default_timezone_set("Asia/Dhaka");
session_start();
$user_name = $_SESSION['user_name'];
//$user_role = $_SESSION['user_role'];

$request_data = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
$current_date = date("Y-m-d");
$renew_date = trim($request_data['renew_date']);
$ids = $request_data['ids'];


$cn = connectDB();


$qry = "SELECT tmcl.id FROM monthly_bill_call_list mcl INNER JOIN tmp_monthly_call_list tmcl
        ON mcl.contact_id=tmcl.contact_id
        WHERE DATE_FORMAT(tmcl.next_renewal_date,'%Y-%m-%d')='$renew_date'";
$mcl_ids = array();
$rs = Sql_exec($cn,$qry);
while($dt = Sql_fetch_array($rs)){
    $mcl_ids[]= $dt['id'];
}

$insert_ids = array_diff($ids,$mcl_ids);
$len = count($insert_ids);
$id_str = "";
foreach($insert_ids as $key=>$val){
    if($id_str == "" ){
        $id_str = $val;
    }else{
        $id_str .= ",".$val;
    }
}

if( $id_str != "" ){
    $qry = "SELECT contact_id, email, charging_due_date, next_renewal_date, `status`,package_price,due_amount
        FROM tmp_monthly_call_list WHERE id IN (".$id_str.") AND DATE_FORMAT(next_renewal_date,'%Y-%m-%d')='$renew_date';";

    $rs = Sql_exec($cn,$qry);
    $value_str = "";
    while( $dt= Sql_fetch_array($rs) ){
        if( $value_str == "" ){
            $value_str = "(".$dt['contact_id'].",'".$dt['email']."','".$dt['charging_due_date']."','".$dt['next_renewal_date']."','".
                $dt['status']."','". $dt['package_price']."','". $dt['due_amount']."','".$current_date."')";
        }else{
            $value_str .= ",". "(".$dt['contact_id'].",'".$dt['email']."','".$dt['charging_due_date']."','".$dt['next_renewal_date']."','".
                $dt['status']."','". $dt['package_price']."','". $dt['due_amount']."','".$current_date."')";
        }
    }

    if( $value_str != "" ){
        $call_list_qry = "INSERT INTO monthly_bill_call_list ( contact_id, email, charging_due_date, next_renewal_date,`status`, package_price, due_amount,call_date )
                          VALUES ".$value_str.";";
        $rs = Sql_exec($cn,$call_list_qry);
        if($rs){
            echo json_encode(array("status"=>0,"msg"=>"Call list generated successfully."));
        }else{
            echo json_encode(array("status"=>1,"msg"=>"Call list generation Failed."));
        }
    }
}else{
    echo json_encode(array("status"=>1,"msg"=>"You have not selected any Customer Or Nothing New to Insert."));
}


ClosedDBConnection($cn);