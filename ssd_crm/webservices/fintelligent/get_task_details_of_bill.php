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
$bill_id = mysql_real_escape_string(trim($datainfo['transaction_id']));

$qry = "SELECT  DISTINCT InvoiceBookID FROM chequebooks";
$rs = Sql_exec($cn,$qry);
$cheque_book_option_str = '<option value="">--Select--</option>';
while( $dt=Sql_fetch_array($rs) ){
    $book_id = trim($dt['InvoiceBookID']);
    $cheque_book_option_str .= '<option value="'.$book_id.'">'.$book_id.'</option>';
}


$payment_qry = "SELECT pay.cash_receive,pay.receipt_number,pay.nid,pay.photo,pay.saf,pay.remarks FROM
                customer_conversion AS c_conv INNER JOIN payments AS pay ON c_conv.contact_id = pay.contact_id
                WHERE c_conv.transaction_id = '$bill_id';";
$rs = Sql_exec($cn,$payment_qry);
$paydt = Sql_fetch_array($rs);

$collect_amount = $paydt['cash_receive'] ? trim($paydt['cash_receive']): 0;
$receipt_number = $paydt['receipt_number'] ? trim($paydt['receipt_number']): "";
$nid            = $paydt['nid'] ? trim($paydt['nid']): "";
$photo          = $paydt['photo'] ? trim($paydt['photo']): "";
$sap            = $paydt['saf'] ? trim($paydt['saf']): "";
$remarks        = $paydt['remarks'] ? trim($paydt['remarks']): "";

$qry = "SELECT
uinfo.user_name AS user_agent,
CONCAT_WS(\" \",NULLIF(uinfo.first_name,\"\"),NULLIF(uinfo.last_name,\"\")) AS agent_name,
convertion.contact_id as customer_id,
CONCAT( contact.first_name,contact.last_name ) AS customer_name,
contact.email AS email,
CONCAT_WS(\", \", NULLIF(contact.phone1, \"\"), NULLIF(contact.phone2, \"\")) AS phone,
contact.address1 AS address1,
contact.address2 AS address2,
convertion.install_cost AS install_cost,
convertion.monthly_cost AS monthly_cost,
convertion.month_number AS month_number,
convertion.real_ip_cost AS real_ip_cost,
convertion.additional_cost AS additional_cost,
convertion.collection_amount AS total_cost,
convertion.package AS package,
convertion.collection_date AS collection_date,
convertion.transaction_id AS transaction_id
FROM contacts AS contact INNER JOIN customer_conversion AS convertion ON  contact.id = convertion.contact_id
INNER JOIN user_info As uinfo ON contact.assign_to = uinfo.user_id
WHERE convertion.transaction_id ='".$bill_id."';";

$rs = Sql_exec($cn,$qry);
$row = Sql_fetch_array($rs);

$table_html = "";
if( count($row)>0 ){

    $customer_id = $row["customer_id"];
    $customer_hidden_input_str = '<input id="customer_id" type="hidden" value="'.$customer_id.'"/>';

    $table_html = "<table class=\"table table-bordered\"><tbody>";
    $table_html.="<tr>$customer_hidden_input_str<td>"."CustomerID:"."</td>"."<td>".$row["customer_id"]."</td></tr>";
    $table_html.="<tr><td>"."Customer Name:"."</td>"."<td>".$row["customer_name"]."</td></tr>";
    // $table_html.="<tr><td>"."Email:"."</td>"."<td>".$row["email"]."</td></tr>";

    $table_html.="<tr><td>"."Connection Adress:"."</td>"."<td>".$row["address1"]."</td></tr>";
    $table_html.="<tr><td>"."Collection Address:"."</td>"."<td>".$row["address2"]."</td></tr>";
    $table_html.="<tr><td>"."Contact No:"."</td>"."<td>".$row["phone"]."</td></tr>";
    $table_html.="<tr><td>"."Package:"."</td>"."<td>".$row["package"]."</td></tr>";
    $table_html.="<tr><td>"."Agent Name:"."</td>"."<td>".$row["agent_name"]."</td></tr>";
    $table_html.="<tr><td>"."Collection Date:"."</td>"."<td>".$row["collection_date"]."</td></tr>";

    $table_html.="<tr><td>"."Installation Charge:"."</td>"."<td>".$row["install_cost"]."</td></tr>";
    $table_html.="<tr><td>"."RealIP Charge:"."</td>"."<td>".$row["real_ip_cost"]."</td></tr>";
    $table_html.="<tr><td>"."Other Charge:"."</td>"."<td>".$row["additional_cost"]."</td></tr>";
    $table_html.="<tr><td>"."Monthly Cost:"."</td>"."<td>".$row["monthly_cost"]."</td></tr>";
    $table_html.="<tr><td>"."Month Number:"."</td>"."<td>".$row["month_number"]."</td></tr>";
    $table_html.="<tr><td>"."Total Price:"."</td>"."<td>".$row["total_cost"]."</td></tr>";

    // $table_html.="<tr><td>"."Transaction ID:"."</td>"."<td>".$row["transaction_id"]."</td></tr>";

    $table_html.="<tr><td>"."Collected Amount:"."</td>"."<td><input id=\"collected_amount\" type=\"text\" value=\"".$collect_amount."\" /></td></tr>";
    $table_html.="<tr><td>"."Receipt Books:"."</td>"."<td><select onchange=\"get_chequebook_pages();\" class=\"chosen-select\" id=\"cheque_book\">".
        $cheque_book_option_str
        ."</select></td></tr>";
    $table_html.="<tr class=\"cheque_pages\" style=\"display:none\"><td>"."Available Pages:"."</td>"."<td>".
        "<select class=\"chosen-select\" id=\"cheque_book_pages\"></select></td></tr>";

    // $table_html.="<tr><td>"."Receipt Number:"."</td>"."<td><input id=\"receipt_number\" type=\"text\" value=\"".$receipt_number."\" /></td></tr>";
    $table_html.="<tr><td>"."Remarks:"."</td>"."<td><textarea id=\"remarks\" value=\"\">".$remarks."</textarea></td></tr>";

    $yes_option = $nid == "yes" ? "<option selected value=\"yes\">Yes</option>": "<option value=\"yes\">Yes</option>";
    $no_option  = $nid == "no" ? "<option selected value=\"no\">No</option>": "<option value=\"no\">No</option>";

    $photo_yes_option = $photo == "yes" ? "<option selected value=\"yes\">Yes</option>": "<option value=\"yes\">Yes</option>";
    $photo_no_option  = $photo == "no" ? "<option selected value=\"no\">No</option>": "<option value=\"no\">No</option>";

    $sap_yes_option = $sap == "yes" ? "<option selected value=\"yes\">Yes</option>": "<option value=\"yes\">Yes</option>";
    $sap_no_option  = $sap == "no" ? "<option selected value=\"no\">No</option>": "<option value=\"no\">No</option>";

    $table_html.="<tr><td>"."NID:"."</td>"."<td>".
        "<select id=\"nid\">
         <option value=\"\">--select--</option>".
        $no_option.
        $yes_option.
        "</select></td></tr>";
    $table_html.="<tr><td>"."Photo:"."</td>"."<td>".
        "<select id=\"photo\">
         <option value=\"\">--select--</option>".
        $photo_no_option.
        $photo_yes_option.
        "</select></td></tr>";
    $table_html.="<tr><td>"."SAP:"."</td>"."<td>".
        "<select id=\"sap\">
         <option value=\"\">--select--</option>"
        .$sap_no_option.
        $sap_yes_option.
        "</select></td></tr>";
    $table_html.="</tbody></table>";
}

echo $table_html;

