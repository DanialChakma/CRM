<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 8/16/2016
 * Time: 4:45 PM
 */

error_reporting(E_ALL);
$file = "D:\/\/"."response.txt";
echo $file."\n";
$content_as_string = file_get_contents($file);
$json_arr = json_decode($content_as_string,TRUE);

print_r($json_arr);

$len = count($json_arr);
for( $i=0;$i<$len;$i++ ){
    $bill_id = $json_arr['BillID'];
    $status  =  $json_arr['Status'];

    $qry = "SELECT CONCAT( contact.first_name,contact.last_name ) AS customer_name,
            CONCAT_WS(\",\", NULLIF(contact.phone1,\"\"), NULLIF(contact.phone2, \"\")) AS phone,
            cus_conv.collection_date
            FROM customer_conversion AS cus_conv INNER JOIN contacts AS contact ON cus_conv.contact_id = contact.id
            WHERE cus_conv.transaction_id = '$bill_id'";



}