<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 8/12/2016
 * Time: 6:01 PM
 */

require_once "../lib/common.php";
$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

$cn = connectDB();
$bill_id = mysql_real_escape_string(trim($datainfo['mb_id']));
/*
$qry = "SELECT  DISTINCT InvoiceBookID FROM chequebooks";
$rs = Sql_exec($cn,$qry);
$cheque_book_option_str = '<option value="">--Select--</option>';
while( $dt=Sql_fetch_array($rs) ){
    $book_id = trim($dt['InvoiceBookID']);
    $cheque_book_option_str .= '<option value="'.$book_id.'">'.$book_id.'</option>';
} */


$payment_qry = "SELECT mb.id,mb.package_price,mb.due_amount,mb.delivery_cost,mb.email,mb.collection_date as collection_date,mb.cash_receive,mb.receipt_number,mb.invoice_book_id,mb.remarks
                FROM monthly_bill_call_list AS mb
                WHERE mb.id = '$bill_id';";
$rs = Sql_exec($cn,$payment_qry);
$paydt = Sql_fetch_array($rs);

$mb_id = $paydt['id'];
$package_price = floatval(trim($paydt['package_price']));
$package_price_tax = floatval(trim($paydt['package_price_tax']));

$package_total_price =  round(($package_price+$package_price_tax),4);
$delivery_cost   = trim($paydt['delivery_cost']);
$delivery_cost   = empty($delivery_cost) ? "":floatval($delivery_cost);
$due_amount = trim($paydt['due_amount']);
$due_amount = empty($due_amount) ? 0.0 : floatval($due_amount);

$contact_id = trim($paydt['email']);
$collection_date    = $paydt['collection_date'];
$collect_amount     = $paydt['cash_receive'] ? trim($paydt['cash_receive']): "";
$receipt_number     = $paydt['receipt_number'] ? trim($paydt['receipt_number']): "";
$book_id            = $paydt['invoice_book_id'] ? trim($paydt['invoice_book_id']): "";
$remarks            = $paydt['remarks'] ? trim($paydt['remarks']): "";


$assigned_client_qry = "SELECT uinfo.user_name AS user_agent,
                        CONCAT_WS(\" \",NULLIF(uinfo.first_name,\"\"),NULLIF(uinfo.last_name,\"\")) AS agent_name
                        FROM  user_info AS uinfo INNER JOIN contacts AS contact ON contact.assign_to = uinfo.user_id
                        WHERE contact.uid='$contact_id'";

$rs = Sql_exec($cn,$assigned_client_qry);
$agent_data = Sql_fetch_array($rs);
$agent_name = $agent_data['agent_name'] ? trim($agent_data['agent_name']):"";

$qry = "SELECT
          contact.email as customer_id,
          contact.customer_name AS customer_name,
          contact.email AS email,
          contact.phone_no_p AS phone,
          contact.present_address_1 AS address1,
          contact.permanent_address AS address2,
          contact.package AS package,
          mb.bill_type AS bill_type
        FROM cgw_customers AS contact INNER JOIN monthly_bill_call_list AS mb ON  contact.email = mb.email
        WHERE mb.id ='".$mb_id."';";

$rs = Sql_exec($cn,$qry);
$row = Sql_fetch_array($rs);

$table_html = "";
if( count($row)>0 ){

    $customer_id = $row["customer_id"];
    $mb_primary_id_str = '<input id="mb_primary_id" type="hidden" value="'.$mb_id.'"/>';
    $customer_hidden_input_str = '<input id="customer_id" type="hidden" value="'.$customer_id.'"/>';
    $table_html = "<table class=\"table table-bordered\"><tbody>$mb_primary_id_str";
    $table_html.="<tr>$customer_hidden_input_str<td>"."CustomerID:"."</td>"."<td>".$row["customer_id"]."</td></tr>";
    $table_html.="<tr><td>"."Customer Name:"."</td>"."<td>".utf8_encode($row["customer_name"])."</td></tr>";
    // $table_html.="<tr><td>"."Email:"."</td>"."<td>".$row["email"]."</td></tr>";

    $table_html.="<tr><td>"."Present Adress:"."</td>"."<td>".utf8_encode($row["address1"])."</td></tr>";
    $table_html.="<tr><td>"."Permanent Address:"."</td>"."<td>".utf8_encode($row["address1"])."</td></tr>";
    $table_html.="<tr><td>"."Contact No:"."</td>"."<td>".$row["phone"]."</td></tr>";
    $table_html.="<tr><td>"."Package:"."</td>"."<td>".utf8_encode($row["package"])."</td></tr>";
  //  $table_html.="<tr><td>"."Agent Name:"."</td>"."<td>".$agent_name."</td></tr>";
    $table_html.="<tr><td>"."Collection Date:"."</td>"."<td>".$collection_date."</td></tr>";
    $table_html.="<tr><td>"."Package Price(Unit Price+Tax Cost):"."</td>"."<td>".$package_total_price." Tk.</td></tr>";
 //   $real_ip_cost = empty($row["real_ip_cost"])? 0.0:floatval($row["real_ip_cost"]);
 //   $table_html.="<tr><td>"."RealIP Charge:"."</td>"."<td>".$real_ip_cost." Tk.</td></tr>";
    $table_html.="<tr><td>"."Due Amount:"."</td>"."<td>".$due_amount." Tk.</td></tr>";
//    $total_payment = $due_amount + $real_ip_cost;
    $table_html.="<tr><td>"."Total Payment(Due Amount):"."</td>"."<td>".$due_amount." Tk.</td></tr>";


    $table_html.="<tr><td>"."Collected Amount:"."</td>"."<td><input id=\"collected_amount\" type=\"text\" value=\"".$collect_amount."\" /> Tk.</td></tr>";
    $table_html.="<tr><td>"."Delivery Cost:"."</td>"."<td><input id=\"delivery_cost\" type=\"text\" value=\"".$delivery_cost."\" /> Tk.</td></tr>";
  /*  $table_html.="<tr><td>"."Cheque Books:"."</td>"."<td>".
        "<select onchange=\"get_chequebook_pages();\" class=\"chosen-select\" id=\"cheque_book\">".
        $cheque_book_option_str
        ."</select></td></tr>";
    $table_html.="<tr class=\"cheque_pages\" style=\"display:none\"><td>"."Available Pages:"."</td>"."<td>".
        "<select class=\"chosen-select\" id=\"cheque_book_pages\"></select></td></tr>"; */
    $table_html.="<tr><td>"."Remarks:"."</td>"."<td><textarea id=\"remarks\" value=\"\">".$remarks."</textarea></td></tr>";
    $table_html.="</tbody></table>";
}

echo $table_html;

