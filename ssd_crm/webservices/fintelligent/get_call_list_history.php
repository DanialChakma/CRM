<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 1/16/2017
 * Time: 5:17 PM
 */

require_once "../lib/common.php";
require_once "fin_lib.php";
date_default_timezone_set("Asia/Dhaka");
$current_date = date("Y-m-d");
$date_array = array();

for($i=-7; $i<=5; $i++){
    $date_array[] = date('d-m-Y', strtotime($i.' day', strtotime($current_date)));
}



$qry = "SELECT id,DATE(next_renewal_date) as renew_date FROM monthly_bill_call_list WHERE `status` <> '3'
        GROUP BY DATE(next_renewal_date) LIMIT 20";

$cn = connectDB();

$rs = Sql_exec($cn,$qry);
$data = array();
$db_date_array = array();
$i=0;
while( $dt=Sql_fetch_array($rs) ){
    $renew_date = date("d-m-Y",strtotime($dt['renew_date']));
    $db_date_array[] = $renew_date;
}

$i = 0;
foreach($date_array as $key=>$value){
    $j = 0;
    $data[$i][$j++] = ($i+1);
    if( array_search($value,$db_date_array) !== FALSE  ){
        $data[$i][$j++] = '<button type="button" class="btn btn-default btn-sm">
                                    <span class="glyphicon glyphicon-ok"></span>
                        </button>';
    }else{
        $data[$i][$j++] = '<button type="button" class="btn btn-default btn-sm">
                                    <span class="glyphicon glyphicon-remove"></span>
                        </button>';
    }

    $data[$i][$j++] = date("jS F, Y",strtotime($value));
    $i++;
}

echo json_encode($data);

