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
$customer_id = mysql_real_escape_string(trim($datainfo['customer_id']));
$mb_id = mysql_real_escape_string(trim($datainfo['mb_id']));

$mb_qry = "SELECT id,package_price,due_amount,next_renewal_date
           FROM monthly_bill_call_list WHERE id='$mb_id';";
$rs = Sql_exec($cn,$mb_qry);
$mb_dt = Sql_fetch_array($rs);

$mb_id = $mb_dt['id'];
$package_price = $mb_dt['package_price'];
$renew_date = date("jS F, g:i A, Y", strtotime($mb_dt['next_renewal_date']));
$due_amount = floatval($mb_dt['due_amount']);
$due_amount = empty($due_amount)? 0.0:$due_amount;


$qry = "SELECT customer_name, email, phone_no_p,package, present_address_1, permanent_address,gender,city
        FROM cgw_customers WHERE email='$customer_id';";

$rs = Sql_exec($cn,$qry);
$row = Sql_fetch_array($rs);

$table_html = "";
if( count($row)>0 ){
    $table_html = "<input type=\"hidden\" id=\"mbcl_id\" value=\"".$mb_id."\" />";
    $table_html .= "<table class=\"table table-bordered\"><tbody>";
    $table_html.="<tr><td>"."Customer Name:"."</td>"."<td>".$row["customer_name"]."</td></tr>";
    // $table_html.="<tr><td>"."Email:"."</td>"."<td>".$row["email"]."</td></tr>";
    $table_html.="<tr><td>"."Contact No:"."</td>"."<td>".$row["phone_no_p"]."</td></tr>";
    $table_html.="<tr><td>"."Renew Date:"."</td>"."<td>".$renew_date."</td></tr>";
    $table_html.="<tr><td>"."Package:"."</td>"."<td>".$row["package"]."</td></tr>";
    $table_html.="<tr><td>"."Present Address:"."</td>"."<td>".htmlspecialchars_decode($row["present_address_1"])."</td></tr>";
    $table_html.="<tr><td>"."Permanent Address:"."</td>"."<td>".htmlspecialchars_decode($row["permanent_address"])."</td></tr>";

 //   $table_html.="<tr><td>"."Installation Charge:"."</td>"."<td>".$row["install_cost"]."</td></tr>";
    $real_ip_cost = floatval($row["real_ip_cost"]);
    $real_ip_cost = empty($real_ip_cost) ? 0.0:$real_ip_cost;
  //  $table_html.="<tr><td>"."Other Charge:"."</td>"."<td>".$row["additional_cost"]."</td></tr>";
    $table_html.="<tr><td>"."Monthly Cost:"."</td>"."<td>".$package_price."</td></tr>";
    $table_html.="<tr><td>"."RealIP Charge:"."</td>"."<td>".$real_ip_cost."</td></tr>";
    $table_html.="<tr><td>"."Due Amount:"."</td>"."<td>".$due_amount."</td></tr>";
 //   $table_html.="<tr><td>"."Month Number:"."</td>"."<td>".$row["month_number"]."</td></tr>";


    $total_monthly_cost = $due_amount+$real_ip_cost;
    $table_html.="<tr><td>"."Total Cost:"."</td>"."<td>".$total_monthly_cost."</td></tr>";

    $table_html.="<tr><td>"."Call Staus:"."</td>"."<td>".
        "<select onchange=\"call_status_change();\" id=\"call_status\">
         <option value=\"\">--select--</option>
         <option value=\"Connected\">Connected</option>
         <option value=\"Unreachable\">Unreachable</option>
         <option value=\"UnAnswered\">UnAnswered</option>
        </select></td></tr>";
    $table_html.="<tr style=\"display:none\" class=\"outcome\"><td>"."Outcome:"."</td>"."<td>".
        "<select onchange=\"outcome_change();\" id=\"outcome\">
         <option value=\"\">--select--</option>
         <option value=\"1\">Followup</option>
         <option value=\"2\">Not Interested to pay</option>
         <option value=\"3\">Pay Today</option>
         <option value=\"4\">Pay Later</option>
        </select></td></tr>";

    $table_html.="<tr style=\"display:none\" class=\"paymentmethod\"><td>"."Payment Method:"."</td>"."<td>".
        "<select onchange=\"\" id=\"paymentmethod\">
         <option value=\"1\">Online</option>
         <option value=\"2\">Bank</option>
         <option value=\"3\">eCourier</option>
        </select></td></tr>";
    $table_html.="<tr style=\"display:none\" class=\"payment_date\"><td>"."Payment Date:"."</td>"."<td><input class=\"calendarPickerDate\" type=\"text\" id=\"payment_date\"/></td></tr>";
    $table_html.="<tr style=\"display:none\" class=\"followup\"><td>"."Followup Date:"."</td>"."<td><input class=\"calendarPickerDate\" type=\"text\" id=\"follow_up_date\"/></td></tr>";
    $table_html.="<tr><td>"."Remarks:"."</td>"."<td><textarea id=\"remarks\" value=\"\" /></td></tr>";


    $table_html.="</tbody></table>";
}

echo $table_html;

