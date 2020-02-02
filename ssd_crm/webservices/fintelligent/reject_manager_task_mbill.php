<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 8/17/2016
 * Time: 4:03 PM
 */

require_once "../lib/common.php";
require_once "fin_lib.php";
date_default_timezone_set("Asia/Dhaka");
$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
$cn = connectDB();
$mb_id = mysql_real_escape_string(trim($datainfo['mb_id']));
$qry = "UPDATE monthly_bill_call_list
        SET `collection_status` = 'cancel'
        WHERE `id` ='$mb_id'";
$rs = Sql_exec($cn,$qry);

if($rs){
    echo json_encode(
        array(
            "code"=>1,
            "msg"=>"Operation Successful"
        )
    );
}else{
    echo json_encode(
        array(
            "code"=>2,
            "msg"=>"Failed to Update Bill."
        )
    );
}

ClosedDBConnection($cn);


