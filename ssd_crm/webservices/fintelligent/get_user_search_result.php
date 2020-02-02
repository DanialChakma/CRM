<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 6/7/2017
 * Time: 5:39 PM
 */

require_once "../lib/common.php";
require_once "fin_lib.php";
session_start();
date_default_timezone_set("Asia/Dhaka");
$user_name = $_SESSION['user_name'];
$params = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
$user_id = trim($params['user_id']);

$get_tasks_url = "http://103.218.27.138/radiusservices/user_details.php";

$cn = connectDB();
$qry = "SELECT mb.id,mb.radius_user_name,mb.next_renewal_date,mb.due_amount,usrinfo.customer_name,usrinfo.email,usrinfo.phone_no_p,usrinfo.permanent_address,usrinfo.package FROM monthly_bill_call_list AS mb INNER JOIN cgw_customers AS usrinfo ON mb.radius_user_name = usrinfo.radius_user_name
WHERE mb.radius_user_name = '$user_id' AND mb.`status` <> 'PAID';";
try{

    $rs = Sql_exec($cn,$qry);
    $num_rows = Sql_Num_Rows($rs);
    if( $num_rows > 0 ){

        $dt = Sql_fetch_array($rs);
        $mb_id = $dt['id'];
        $radius_user_name = $dt['radius_user_name'];
        $customer_name = $dt['customer_name'];
        $customer_email = $dt['email'];
        $customer_phone_no_p = $dt['phone_no_p'];
        $permanent_address = $dt['permanent_address'];
        $expiration_date = trim($dt['next_renewal_date']);
        $expiration_date = date("jS F, g:i A, Y",strtotime($expiration_date));
        $package = $dt['package'];
        $due_amount = $dt['due_amount'];
        $table_html = "<form id=\"user_details\"><input type=\"hidden\" name=\"mbcl_id\" id=\"mbcl_id\" value=\"".$mb_id."\" />";
        $table_html .="<table class=\"table table-bordered\"><tbody>";
        $table_html .="<tr><td>"."Customer Name:"."</td>"."<td>".$customer_name."</td></tr>";
        $table_html .="<tr><td>"."Email:"."</td>"."<td>".$customer_email."</td></tr>";
        $table_html .="<tr><td>"."Contact No:"."</td>"."<td>".$customer_phone_no_p."</td></tr>";
        $table_html .="<tr><td>"."Expiration Date:"."</td>"."<td>".$expiration_date."</td></tr>";
        $table_html .="<tr><td>"."Package:"."</td>"."<td>".$package."</td></tr>";
        $table_html .="<tr><td>"."Permanent Address:"."</td>"."<td>".htmlspecialchars_decode($permanent_address)."</td></tr>";
        $table_html .="<tr><td>"."Due Amount:"."</td>"."<td>".$due_amount."</td></tr>";
        $table_html .="<tr><td>"."Verbal Payment Date:"."</td>"."<td><input type=\"text\" name=\"payment_date\" id=\"payment_date\" /></td></tr>";
        $table_html .="</tbody></table></form>";

        echo $table_html;
    }else{


        $data = array(
                        "manager"       =>  "crmwebservice",
                        "managerpass"   =>  "crm_doze",
                        "user"          =>  $user_id
                );

        $req_method = "GET";
        $response = curl_request($get_tasks_url,$req_method,$data);
        $response = explode("|",$response);
        $status = trim($response[0]);
        if( $status == "SUCCESS" ){

            $user_name = trim($response[1]);
            $doze_id = trim($response[2]);
            $customer_full_name = trim(trim($response[3])." ".trim($response[4]));
            $user_mobile = trim($response[5]);
            $address = trim($response[6]);
            $city = trim($response[7]);
            $email = trim($response[9]);
            $package = trim($response[10]);
            $creation_date = trim($response[11]);
           // $creation_date = date("jS F, g:i A, Y",strtotime($creation_date));
            $expiration_date = trim($response[12]);
           // $expiration_date = date("jS F, g:i A, Y",strtotime($expiration_date));
            $current_balance = round(floatval(trim($response[8])),4);
            $package_unit_price = round(floatval(trim($response[13])),4);
            $package_unit_price_tax = round(floatval(trim($response[14])),4);
            $due_amount = round(( ($package_unit_price+$package_unit_price_tax) - $current_balance ),4);

            $table_html = "<form id=\"user_details\"><input type=\"hidden\" name=\"user_name\" id=\"user_name\" value=\"".$user_name."\" />";
            $table_html .= "<input type=\"hidden\" name=\"user_id\" id=\"user_id\" value=\"".$doze_id."\" />";
            $table_html .= "<input type=\"hidden\" name=\"customer_full_name\" id=\"customer_full_name\" value=\"".$customer_full_name."\" />";
            $table_html .= "<input type=\"hidden\" name=\"phone_no\" id=\"phone_no\" value=\"".$user_mobile."\" />";
            $table_html .= "<input type=\"hidden\" name=\"address\" id=\"address\" value=\"".$address."\" />";
            $table_html .= "<input type=\"hidden\" name=\"city\" id=\"city\" value=\"".$city."\" />";
            $table_html .= "<input type=\"hidden\" name=\"email\" id=\"email\" value=\"".$email."\" />";
            $table_html .= "<input type=\"hidden\" name=\"package\" id=\"package\" value=\"".$package."\" />";
            $table_html .= "<input type=\"hidden\" name=\"creation_date\" id=\"creation_date\" value=\"".$creation_date."\" />";
            $table_html .= "<input type=\"hidden\" name=\"expiration_date\" id=\"expiration_date\" value=\"".$expiration_date."\" />";
            $table_html .= "<input type=\"hidden\" name=\"current_balance\" id=\"current_balance\" value=\"".$current_balance."\" />";
            $table_html .= "<input type=\"hidden\" name=\"package_unit_price\" id=\"package_unit_price\" value=\"".$package_unit_price."\" />";
            $table_html .= "<input type=\"hidden\" name=\"package_unit_price_tax\" id=\"package_unit_price_tax\" value=\"".$package_unit_price_tax."\" />";
            $table_html .= "<input type=\"hidden\" name=\"due_amount\" id=\"due_amount\" value=\"".$due_amount."\" />";
            $table_html .="<table class=\"table table-bordered\"><tbody>";
            $table_html .="<tr><td>"."Customer Name:"."</td>"."<td>".$customer_full_name."</td></tr>";
            $table_html .="<tr><td>"."Email:"."</td>"."<td>".$email."</td></tr>";
            $table_html .="<tr><td>"."Contact No:"."</td>"."<td>".$user_mobile."</td></tr>";
            $table_html .="<tr><td>"."Expiration Date:"."</td>"."<td>".$expiration_date."</td></tr>";
            $table_html .="<tr><td>"."Package:"."</td>"."<td>".$package."</td></tr>";
            $table_html .="<tr><td>"."Permanent Address:"."</td>"."<td>".htmlspecialchars_decode($address)."</td></tr>";
            $table_html .="<tr><td>"."Due Amount:"."</td>"."<td>".$due_amount."</td></tr>";
            $table_html .="<tr><td>"."Verbal Payment Date:"."</td>"."<td><input type=\"text\" name=\"payment_date\" id=\"payment_date\" /></td></tr>";
            $table_html .="</tbody></table></form>";

            echo $table_html;

        }else{
            echo "<strong>".trim($response[1])."</strong>";
        }

    }
}catch (Exception $ex){
    $errors_msg = $ex.getMessage();
    echo "<strong>".trim($errors_msg)."</strong>";
    ClosedDBConnection($cn);
}


ClosedDBConnection($cn);
