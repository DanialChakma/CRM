<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 9/25/2016
 * Time: 6:32 PM
 */
require_once "../lib/common.php";
$cn = connectDB();
$qry = "SELECT  DISTINCT InvoiceBookID FROM chequebooks";
$rs = Sql_exec($cn,$qry);
$option_str = '<option value="">--Select--</option>';
while( $dt=Sql_fetch_array($rs) ){
    $book_id = trim($dt['InvoiceBookID']);
    $option_str .= '<option value="'.$book_id.'">'.$book_id.'</option>';
}

echo $option_str;