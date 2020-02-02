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
$bill_id = mysql_real_escape_string(trim($datainfo['mbid']));

$cgw_customer_info = array();
$qry_cgw_customers = "SELECT TRIM(email) AS email, customer_name, phone_no_p,package,present_address_1 FROM cgw_customers GROUP BY email;";
$rs = Sql_exec($cn,$qry_cgw_customers);
while($dt = Sql_fetch_array($rs)){
    $cgw_customer_info[$dt['email']] = array(
        "customer_name" => trim($dt['customer_name']),
        "contact_no" => trim($dt['phone_no_p']),
        "package" => trim($dt['package']),
        "address" =>trim($dt['present_address_1'])
    );
}



$qry = "SELECT bill_type,installation_cost, contact_id,email,cash_receive, receipt_number,collection_date,package_price,package_price_tax,due_amount, payment_date, delivery_cost,collection_status,collection_agent
                FROM monthly_bill_call_list
                WHERE id ='$bill_id';";
$rs = Sql_exec($cn,$qry);
$row = Sql_fetch_array($rs);

$table_html = "";
if( count($row)>0 ){
    $email = $row['email'];
    $bill_type = $row['bill_type'];
    $collection_status = $row['collection_status'];
    $receipt_number = $row['receipt_number'];
    $installation_cost = $row['installation_cost'];
    $collection_date = $row['collection_date'];
    $package_price = $row['package_price'];
    $package_price_tax = $row['package_price_tax'];
    $monthly_amount = floatval($package_price)+floatval($package_price_tax);
    $monthly_amount = round($monthly_amount,4);
    $collect_amount = $row['cash_receive'];
    $delivery_cost = $row['delivery_cost'];
    $due_amount = floatval($row['due_amount']);
    $customer_name = $cgw_customer_info[$email]['customer_name'];
    $contact_no = $cgw_customer_info[$email]['contact_no'];
    $address = $cgw_customer_info[$email]['address'];
    $package = $cgw_customer_info[$email]['package'];

    $contact_id = intval($dt['contact_id']);
    $real_ip_cost = 0.00;
    if( !empty($contact_id) && $contact_id != -1 ){
        $qry = "SELECT `real_ip_cost`
                FROM customer_conversion WHERE `contact_id` = '$contact_id';";
        $rs_ipCost = Sql_exec($cn,$qry);
        $dt_ip=Sql_fetch_array($rs_ipCost);
        $real_ip_cost_db = $dt_ip['real_ip_cost'];
        if( !empty($real_ip_cost_db) ){
            $real_ip_cost = floatval($real_ip_cost_db);
        }
    }


    $mb_primary_id_str = '<input type="hidden" id="bill_type" value=\"'.$bill_type.'\" /><input id="mb_primary_id" type="hidden" value="'.$bill_id.'"/>';
    $customer_hidden_input_str = '<input id="customer_id" type="hidden" value="'.$email.'"/>';
    $table_html = "<table class=\"table table-bordered\"><tbody>$mb_primary_id_str";
    $table_html.="<tr>$customer_hidden_input_str<td>"."Email:"."</td>"."<td>".$email."</td></tr>";
    $table_html.="<tr><td>"."Customer Name:"."</td>"."<td>".utf8_encode($customer_name)."</td></tr>";
    // $table_html.="<tr><td>"."Email:"."</td>"."<td>".$row["email"]."</td></tr>";

    $table_html.="<tr><td>"."Present Adress:"."</td>"."<td>".utf8_encode($address)."</td></tr>";
    $table_html.="<tr><td>"."Contact No:"."</td>"."<td>".$contact_no."</td></tr>";
    $table_html.="<tr><td>"."Package:"."</td>"."<td>".utf8_encode($package)."</td></tr>";
   // $table_html.="<tr><td>"."Agent Name:"."</td>"."<td>".$agent_name."</td></tr>";
    $table_html.="<tr><td>"."Collection Date:"."</td>"."<td>".$collection_date."</td></tr>";

    $table_html.="<tr><td>"."Monthly Cost:"."</td>"."<td>".$monthly_amount." Tk.</td></tr>";
    $table_html.="<tr><td>"."RealIP Charge:"."</td>"."<td>".$real_ip_cost." Tk.</td></tr>";
    $table_html.="<tr><td>"."Due Amount:"."</td>"."<td>".$due_amount." Tk.</td></tr>";
    $total_payment = $due_amount + $real_ip_cost;
    $table_html.="<tr><td>"."Total Payment(RealIP+Due Amount):"."</td>"."<td>".$total_payment." Tk.</td></tr>";

    $table_html.="<tr><td>"."Collected Amount:"."</td>"."<td><input id=\"collected_amount\" type=\"text\" value=\"".$collect_amount."\" /> Tk.</td></tr>";
    if( $bill_type == "DB" ){
        $table_html.="<tr><td>"."Installation Cost:"."</td>"."<td><input id=\"installation_cost\" type=\"text\" value=\"".$installation_cost."\" /> Tk.</td></tr>";
    }

    $table_html.="<tr><td>"."Delivery Cost:"."</td>"."<td><input id=\"delivery_cost\" type=\"text\" value=\"".$delivery_cost."\" /> Tk.</td></tr>";
    $table_html.="<tr><td>"."Receipt Number:"."</td>"."<td><input id=\"receipt_number\" type=\"text\" value=\"".$receipt_number."\" /></td></tr>";
    $table_html.="<tr><td style='color:red;text-align: center;' colspan='2'>*** Delivery Cost 100 Tk.***</td></tr>";
    $table_html.="</tbody></table>";

   // echo $table_html;
    echo json_encode(array("collection_status"=>$collection_status,"content"=>$table_html));

}else{
   echo json_encode(array("collection_status"=>"","content"=>""));
}





