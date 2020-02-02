<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 9/25/2016
 * Time: 6:49 PM
 */

require_once "../lib/common.php";
$data = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
$cn = connectDB();
$cheque_book_id = trim($data['cheque_book_id']);

$i = 0;

$qry = "SELECT InvoiceAutoID, InvoiceBookID, InvoiceInitialID, InvoiceNo, Branch, `Status`, Remarks, UserID, LastUpdate FROM chequebooks
       WHERE InvoiceBookID = '$cheque_book_id';";

$datas = array();
$rs = Sql_exec($cn,$qry);

while( $dt=Sql_fetch_array($rs) ){
    $j=0;
    $datas[$i][$j++] = $dt['InvoiceBookID'];
    $datas[$i][$j++] = $dt['InvoiceInitialID'];
    $datas[$i][$j++] = $dt['InvoiceNo'];
    $datas[$i][$j++] = $dt['Branch'];
    $datas[$i][$j++] = $dt['Status'];
    $datas[$i][$j++] = $dt['Remarks'];
    $datas[$i][$j++] = $dt['LastUpdate'];
    $i++;
}

ClosedDBConnection($cn);
echo json_encode($datas);