<?php

require_once "../lib/common.php";
require_once "fin_lib.php";
session_start();
date_default_timezone_set("Asia/Dhaka");
$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

$login_user_name = $_SESSION['user_name'];
$get_tasks_url = "http://103.218.27.138/radiusservices/user_details.php";
$cn = connectDB();

$customer_id    = mysql_real_escape_string(trim($datainfo['customer_id']));
$payment_method    = mysql_real_escape_string(trim($datainfo['payment_method']));

if( $payment_method != "Ecourier" ){
    $response_array['code'] = 0;
    $response_array['msg'] = "Payment method must be E-Courier.";
    echo json_encode($response_array);
    ClosedDBConnection($cn);
    exit;
}

try{
    $radius_user = "";
    $feedback = "";
    $qry = "SELECT c_cont.radius_user,c_con.collection_date,c_con.conversion_note
            FROM customer_conversion AS c_con INNER JOIN contacts AS c_cont ON c_con.contact_id = c_cont.id
            WHERE c_cont.id = '$customer_id';";
    $rs = Sql_exec($cn,$qry);
    $row_num = Sql_Num_Rows($rs);
    if( $row_num > 0 ){
        $dt = Sql_fetch_array($rs);
        $radius_user = trim($dt['radius_user']);
        $payment_date = trim($dt['collection_date']);
        $feedback = trim($dt['conversion_note']);

        if( empty($payment_date) ){
            $payment_date = date("Y-m-d");
        }
    }

    if( empty($radius_user) ){
        $response_array['code'] = 0;
        $response_array['msg'] = "Radius User Not Founds.";
        echo json_encode($response_array);
        ClosedDBConnection($cn);
        exit;
    }


    $data = array(
        "manager"       =>  "crmwebservice",
        "managerpass"   =>  "crm_doze",
        "user"          =>  $radius_user
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


        $qry = "INSERT INTO monthly_bill_call_list ( radius_user_name,radius_user_id,email,call_date,charging_due_date,next_renewal_date,package_price,package_price_tax,current_credits,due_amount,bill_type,generated_by,`status`,`payment_method`,`payment_date`,`update_count`,`remarks` )
                VALUES (
                    '$user_name',
                    '$doze_id',
                    '$email',
                     DATE(NOW()),
                    '$creation_date',
                    '$expiration_date',
                    '$package_unit_price',
                    '$package_unit_price_tax',
                    '$current_balance',
                    '$due_amount',
                    'DB',
                    '$login_user_name',
                    '3',
                    '3',
                    '$payment_date',
                    1,
                    '$feedback'
                );";
        $rs = Sql_exec($cn,$qry);
        if($rs){
            $qry_basic_info =  "INSERT INTO cgw_customers( radius_user_name, radius_user_id, customer_name, email, phone_no_p, present_address_1, permanent_address, city, package )
                               VALUES ('$user_name','$doze_id','$customer_full_name','$email','$user_mobile','$address','$address','$city','$package');";
            Sql_exec($cn,$qry_basic_info);
            $response_array['code'] = 1;
            $response_array['msg'] = "Bill Successfully Generated.";
        }else{
            $response_array['code'] = 0;
            $response_array['msg'] = "Failed to Generate Bill.";
        }

    }else{
        $response_array['code'] = 0;
        $response_array['msg'] = trim($response[1]);

    }


}catch(Exception $ex){
    $error_str = $ex.getMessage();
    $response_array['code'] = 0;
    $response_array['msg'] = $error_str;
    echo json_encode($response_array);
    ClosedDBConnection($cn);
    exit;
}

echo json_encode($response_array);

ClosedDBConnection($cn);

/*
//
$to  = 'danial@ssd-tech.com';
//subject
$subject = 'New Customer-Installation Bill collection';
//To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
//Additional headers
$headers .= 'To:' . "\r\n";
$headers .= 'From:' . "\r\n";
$headers .= 'Cc:' . "\r\n";
$headers .= 'Bcc:' . "\r\n";

$email = "<table><tbody>
            <tr><th>CustomerID</th><th>".$customer_id."</th></tr>
			<tr><td>Name</td><td>".$customer_name."</td></tr>
			<tr><td>Email</td><td>".$email_id."</td></tr>
			<tr><td>Mobile</td><td>".$phone_number."</td></tr>
			<tr><td>Collection Address</td><td>".$collection_address."</td></tr>
			<tr><td>Package</td><td>".$package."</td></tr>
			<tr><td>Amount</td><td>".$total_cost."</td></tr>
			<tr><td>Collection Date</td><td>".$collection_date."</td></tr>
			<tr><td>Collection Address</td><td>".$connection_address."</td></tr>
			<tr><td>Collection Do</td><td>".$collect_do."</td></tr>
			<tr><td>Remarks</td><td>".$remarks."</td></tr>
		  </tbody></table>";

if( $response_array['code'] == 1 ){
   mail($to, $subject, $email, $headers);
}
*/




