<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 8/11/2016
 * Time: 12:08 PM
 */


function update_or_insert_cgw_customers($cn,$datas){
    $users = array();
    $qry_emails = "SELECT DISTINCT radius_user_name FROM cgw_customers";
    $rs = Sql_exec($cn,$qry_emails);
    while($dt= Sql_fetch_array($rs)){
        $users[] = trim($dt['radius_user_name']);
    }

    $count = count($datas);
    for( $indx = 0; $indx < $count; $indx++ ){
        $user = trim($datas[$indx][1]);
        $user_id = trim($datas[$indx][2]);
        $first_name = mysql_real_escape_string(htmlspecialchars($datas[$indx][3]));
        $last_name = mysql_real_escape_string(htmlspecialchars($datas[$indx][4]));
        $full_name = trim($first_name." ".$last_name);
        $email = mysql_real_escape_string(htmlspecialchars($datas[$indx][9]));
        $contact_no = $datas[$indx][5];
        $package = trim($datas[$indx][10]);
        $permanent_address = mysql_real_escape_string(htmlspecialchars($datas[$indx][6]));
        $city = mysql_real_escape_string(htmlspecialchars($datas[$indx][7]));

        if( array_search($user,$users) != false ){
                // update
            $qry = "UPDATE cgw_customers SET
                                    radius_user_id = '$user_id',
			                        customer_name = '$full_name',
			                        phone_no_p = '$contact_no',
			                        package = '$package',
			                        present_address_1 = '$permanent_address',
			                        permanent_address = '$permanent_address',
			                        city = '$city',
			                        email = '$email'
		                      WHERE radius_user_name = '$user';";
        }else{
            // insert
            $qry = "INSERT INTO cgw_customers ( radius_user_name,radius_user_id, customer_name, email, phone_no_p, package, present_address_1, permanent_address, city )
                    VALUES ('$user','$user_id','$full_name', '$email', '$contact_no', '$package', '$permanent_address', '$permanent_address', '$city' );";

        }

      //  echo $qry;
       Sql_exec($cn,$qry);
    }
}


function get_client_ip(){
    $ip = "";
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = trim($_SERVER['HTTP_CLIENT_IP']);
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = trim($_SERVER['HTTP_X_FORWARDED_FOR']);
    } else {
        $ip = trim($_SERVER['REMOTE_ADDR']);
    }
    return $ip;
}

function curl_request($url,$method,$data){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
   // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    if( $method == "GET" ){
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"GET");
    }else if( $method == "POST" ){
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"POST");
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    }else{
        // Default Method
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"GET");
    }

    $url_to_hit = "";

    if( $method == "GET" ){
        $url_to_hit = $url."?".http_build_query($data);
    }else{
        $url_to_hit = $url;
    }

    //echo $url_to_hit;

    curl_setopt($ch, CURLOPT_URL, $url_to_hit);

   // echo $url_to_hit."\n\n";
    $response = "";
    if( ($response = curl_exec($ch))=== FALSE ){
        $response = curl_error($ch);
        // echo "ERROR:". curl_error($ch);
    }
    curl_close($ch);
    return $response;
}

function initiate_fin_bill($cn,$params){


}

function find_appropriate_agent($cn,$contact_id){
    $customer_agent_qry = "SELECT user_info.user_name,customer_cont.do_area
                               FROM
                                    contacts AS customer_cont INNER JOIN user_info AS user_info ON customer_cont.assign_to = user_info.user_id
                               WHERE customer_cont.uid = '$contact_id'";
    $rs = Sql_exec($cn,$customer_agent_qry);
    $dt = Sql_fetch_array($rs);
    $assigned_to = isset($dt['user_name']) ? trim($dt['user_name']): "admin";
    $assigned_to = trim($assigned_to);
    $customer_do_area = trim($dt['do_area']);
    $agents = array();
    $agent_qry = "SELECT user_name,user_address FROM user_info
                      WHERE user_role LIKE 'account%' AND user_address LIKE '%".$customer_do_area."%';";
    $exact_match_agent = "";
    $rs = Sql_exec($cn,$agent_qry);
    while($dt = Sql_fetch_array($rs)){
        $usr_name = trim($dt['user_name']);
        $usr_address = trim($dt['user_address']);
        if( $usr_address == $customer_do_area ){
            $exact_match_agent = $usr_name;
            break;
        }
        $agents[] = array($usr_name,$usr_address);
    }

    if( empty($exact_match_agent) && count($agents) > 0 ){
        $len = count($agents);
        $rndx = rand(0,$len-1);
        $exact_match_agent = $agents[$rndx][0];
    }

    if( empty($exact_match_agent) ){
        $exact_match_agent = "account_admin";
    }

    return array( 'BillInserter' => $assigned_to, 'Agent' => $exact_match_agent );
}

function get_current_due_amount($cn,$mb_id){
    $mb_qry = "SELECT email,package_price,due_amount
                  FROM monthly_bill_call_list WHERE `id` = '$mb_id';";
    $rs = Sql_exec($cn,$mb_qry);
    $mb_dt = Sql_fetch_array($rs);
    Sql_Free_Result($rs);
    $due_amount = floatval($mb_dt['due_amount']);
    $due_amount = empty($due_amount)? 0.0:$due_amount;
    return $due_amount;
}

function get_customer_details($cn,$contact_id){
    $qry = "SELECT
                      customer.email,
                      mb.id AS mbcl_id,
                      customer.customer_name AS f_name,
                      customer.present_address_1,
                      customer.permanent_address,
                      customer.phone_no_p AS p_no,
                      customer.package AS package,
                      mb.package_price AS pkg_price,
                      mb.due_amount AS due_amount
                FROM cgw_customers customer INNER JOIN monthly_bill_call_list mb ON customer.email = mb.email
                WHERE mb.email = '$contact_id'";
    $rs = Sql_exec($cn,$qry);
    $dt = Sql_fetch_array($rs);
    Sql_Free_Result($rs);
    $payment_collection_address = trim($dt['present_address_1']);
    $net_connection_address = trim($dt['permanent_address']);
    $customer_name = trim($dt['f_name']);
    $phone_number = trim($dt['p_no']);
    $package = trim($dt['package']);
    $email = trim($dt['email']);

    $details = array(
                    'Name'=>$customer_name,
                    'Mobile'=>$phone_number,
                    'EmailID'=>$email,
                    'PaymentCollectionAddress'=>$payment_collection_address,
                    'Package'=>$package,
                    'ConnectionAddress'=>$net_connection_address
                );
    return $details;
}

function check_transition_id_exist($cn,$mb_id,$contact_id){
    $qry = "SELECT transaction_id FROM monthly_bill_call_list WHERE id='$mb_id' AND email='$contact_id';";
    $rs = Sql_exec($cn,$qry);
    $dt = Sql_fetch_array($rs);
    Sql_Free_Result($rs);
    //$actual_contact_id = $dt['contact_id'];
    $db_transaction_id = trim($dt['transaction_id']);
    if( !empty($db_transaction_id) ) return $db_transaction_id;
    else return false;
}