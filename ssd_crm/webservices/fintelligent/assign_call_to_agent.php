<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 9/7/2016
 * Time: 7:10 PM
 */

require_once "../lib/common.php";
date_default_timezone_set("Asia/Dhaka");
$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
$current_date_time = date("Y-m-d H:i:s");
$assign_to = trim($datainfo['assign_id']);
$ids = $datainfo['ids'];
$cn = connectDB();
$in_clause = "";
$len = count($ids);
for($i=0;$i<$len;$i++){
    if( $in_clause != "" ){
        $in_clause .=",".$ids[$i];
    }else{
        $in_clause = $ids[$i];
    }
}
if( $len >0 ){
    $qry = "UPDATE monthly_bill_call_list SET `assign_to`='$assign_to',`call_date`='$current_date_time' WHERE id IN (".$in_clause.");";
    try{
       $rs = Sql_exec($cn,$qry);
       if($rs){
           echo json_encode(
               array("status"=>1,"msg"=>"Successfully Updated")
           );
       }
    }catch (Exception $e){
        echo json_encode(
            array("status"=>0,"msg"=>"Updated failed!")
         );
    }

}

ClosedDBConnection($cn);
