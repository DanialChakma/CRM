<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 9/25/2016
 * Time: 6:32 PM
 */
require_once "../lib/common.php";
$params = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
$cn = connectDB();
$cheque_book_id = mysql_real_escape_string(trim($params['cheque_book_id']));
$qry = "SELECT DISTINCT InvoiceNo,`status` FROM chequebooks
        WHERE InvoiceBookID='$cheque_book_id';";
$rs = Sql_exec($cn,$qry);
$option_str = '<option value="">--Select--</option>';
while( $dt=Sql_fetch_array($rs) ){
    $page = trim($dt['InvoiceNo']);
    $status = trim($dt['status']);
    if( $status == "ISSUED" ){
        $option_str .= '<option disabled value="'.$page.'">'.$page.'</option>';
    }else{
        $option_str .= '<option value="'.$page.'">'.$page.'</option>';
    }

}

echo $option_str;