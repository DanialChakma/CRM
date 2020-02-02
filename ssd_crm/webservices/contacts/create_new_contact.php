<?php
    /*
     *
    Edited by Talemul Islam
    */
    require_once "../lib/common.php";
    checkSession();
    $user_id = $_SESSION['user_id'];
    $cn = connectDB();
//var_dump($_REQUEST['info']); exit;

    if (isset($_REQUEST['info'])) {

        $first_name = $_REQUEST['info']["first_name"];
        $last_name = $_REQUEST['info']["last_name"];
        $lead_source = $_REQUEST['info']["lead_source"];
        $address1 = $_REQUEST['info']["address1"];
        $address2 = $_REQUEST['info']["address2"];
        $phone1 = $_REQUEST['info']["phone1"];
        $phone2 = $_REQUEST['info']["phone2"];
        $email = $_REQUEST['info']["email"];
        $status = $_REQUEST['info']["status"];
        $final_status = $_REQUEST['info']["final_status"];
        $do_area = $_REQUEST['info']["do_area"];
        $customer_type = "lead";
        $next_call_date = date('Y-m-d H:i:s',strtotime($_REQUEST['info']["next_call_date"]));
        $feedback = $_REQUEST['info']["feedback"];
        $notifyme = $_REQUEST['info']["notifyme"];
        $phone1_tmp = $phone1;
        $phone1 = "880".$phone1;

    }



    $check_qry = "SELECT COUNT(id) AS 'number' FROM contacts WHERE SUBSTRING(TRIM(phone1),-10,10) = '$phone1_tmp';";
    $res = Sql_exec($cn, $check_qry);
    $dt = Sql_fetch_array($res);
    $count = intval($dt['number']);

    if( $count > 0 ){
        ClosedDBConnection($cn);
        echo json_encode(array("status"=>1,"msg"=>"Contact Number Already Exist!"));
        exit;
    }

//$select_qry = "select count(*) as `count` from contact_details where phone1='$phone1'";
//
//$result = Sql_exec($cn, $select_qry);
//
//$count = 0;
//
//while($data = Sql_fetch_array($result)){
//    $count = $data['count'];
//}
//
//if(($count !=0) ) {
//    echo 1;
//    exit;
//}

    $is_error = 0;
    $contact_qry = "INSERT INTO contacts (customer_type,lead_source,status,final_status, next_call_date,do_area,first_name,last_name,email,phone1, phone2,address1,address2,update_date,update_by) "
        . "VALUES ('$customer_type','$lead_source','$status','$final_status','$next_call_date','$do_area','$first_name','$last_name','$email','$phone1','$phone2','$address1','$address2','" . date('Y-m-d H:i:s') . "','" . $user_id . "')";

    try {
        $res = Sql_exec($cn, $contact_qry);
    } catch (Exception $e) {
        $is_error = 1;
    }
    /*
    if ($is_error == 0) {
        $details_qry = "update contact_details set first_name='$first_name',last_name='$last_name',email='$email',phone1='$phone1',phone2='$phone2',address1='$address1',address2='$address2',note='$note' where contact_id='$contact_id'";
        try {
            $res = Sql_exec($cn, $details_qry);
        } catch (Exception $e) {
            $is_error = 2;
        }
    }
    */
    $query = mysql_query("SELECT MAX(id) FROM `contacts`");
    $results = mysql_fetch_array($query);
    $contact_id = $results['MAX(id)'];

//if ($is_error == 0) {
//
//
//    $details_qry = "INSERT INTO contact_details (contact_id,first_name,last_name,email,phone1, phone2,address1,address2,update_date,update_by) "
//        . "VALUES ('$contact_id','$first_name','$last_name','$email','$phone1','$phone2','$address1','$address2','".date('Y-m-d H:i:s') . "','".$user_id."')";
//    try {
//        $res = Sql_exec($cn, $details_qry);
//    } catch (Exception $e) {
//        $is_error = 1;
//    }
//}

    if ($is_error == 0 && isset($_REQUEST['info']["feedback"]) && trim($feedback) != '') {

        $action_id = $contact_id;
        $id = $_SESSION["user_id"];
        $name = $_SESSION["first_name"] . ' ' . $_SESSION["last_name"];
        $date = date('Y-m-d H:i:s');

        $contact_qry = "insert into call_history(`contact_id`,`call_date`,`call_agent_name`,`call_agent`,`feedback`,update_date,update_by,notifyme) values ($action_id,'$date','$name',$id,'$feedback','" . date('Y-m-d H:i:s') . "','" . $user_id . "','$notifyme')";
        try {
            $res = Sql_exec($cn, $contact_qry);
        } catch (Exception $e) {
            $is_error = 1;
        }


    }


    ClosedDBConnection($cn);

    if( $is_error == 0 ){
        echo json_encode(array("status"=>$is_error,"msg"=>"Operation Successful."));
    }else{
        echo json_encode(array("status"=>$is_error,"msg"=>"Operation Failed!."));
    }