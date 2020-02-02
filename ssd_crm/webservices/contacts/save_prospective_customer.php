<?php

/*
 *
Edited by Talemul Islam
*/
require_once "../lib/common.php";
checkSession();
$user_id = $_SESSION['user_id'];

$cn = connectDB();
//var_dump($_REQUEST); exit;

if (isset($_REQUEST)) {
    $action = $_REQUEST["action"];
    $action_id = $_REQUEST["action_id"];
    $contact_id = $_REQUEST["contact_id"];
    $next_call_date = $_REQUEST["next_call_date"];
    $first_name = $_REQUEST["first_name"];
    $last_name = $_REQUEST["last_name"];
    $radius_user = $_REQUEST['radius_user'];
    $lead_source = $_REQUEST["lead_source"];
    $address1 = mysql_real_escape_string($_REQUEST["address1"]);
    $address2 = mysql_real_escape_string($_REQUEST["address2"]);
    $phone1 = $_REQUEST["phone1"];
    $phone2 = $_REQUEST["phone2"];
    $email = $_REQUEST["email"];
    $area = mysql_real_escape_string($_REQUEST["area"]);
    $collection_person_name  =   trim( $_REQUEST['collection_person'] );
    $collection_person_do    =   trim( $_REQUEST['collect_person_do'] );
    $collection_person_phone =   trim( $_REQUEST['collection_person_phone'] );
    $zone                    =   trim( $_REQUEST['zone'] );
    $date_of_birth = isset($_REQUEST["date_of_birth"]) ? $_REQUEST["date_of_birth"] : 0;
    $date_of_birth = $date_of_birth ? date('Y-m-d', $date_of_birth) : '0000-00-00';
    $upload_id = intval($_REQUEST["upload_id"]);
    $stage_id = $_REQUEST["stage"];
    $note_id = intval($_REQUEST["note_id"]);
    //$status = $_REQUEST["status"];
    //$final_status = $_REQUEST["final_status"];
    $do_area = $_REQUEST["do_area"];
    //$customer_type = $_REQUEST["type"];
    $client_type=$_REQUEST["client_type"];
    $note = $_REQUEST["note"];
    $assign_agent = isset($_REQUEST["assign_agent"]) ? $_REQUEST["assign_agent"] : $user_id;
    $date_time = isset($_REQUEST["date_time"]) ? $_REQUEST["date_time"] : date('Y-m-d H:i:s');

    if( empty($collection_person_name) ){
        $collection_person_name = $first_name." ".$last_name;
    }

    if( empty($collection_person_do) ){
        $collection_person_do = $do_area;
    }

    if( empty($collection_person_phone) ){
        $collection_person_phone = $phone1.( !empty($phone2) ? ",".$phone2: "");
    }

    if( empty($address2) ){
        $address2 = $address1;
    }
}


$is_error = 0;

$select_qry = "SELECT * FROM contacts WHERE id='$contact_id' AND stage_id=7";
$result = Sql_exec($cn,$select_qry);
if(Sql_Num_Rows($result) > 0 && $_SESSION["user_id"] != '52' ){
    $is_error = 1;
    echo $is_error;
    exit;
}

$contact_qry = "update contacts set area='$area', contact_type='$client_type', next_call_date='$next_call_date',lead_source='$lead_source',do_area='$do_area',assign_to='$assign_agent', update_date= '" . $date_time . "',update_by='" . $user_id . "',upload_id='$upload_id',date_of_birth='$date_of_birth',stage_id='$stage_id',note_id='$note_id',`collection_person`='$collection_person_name',`collection_person_do`='$collection_person_do',`collection_person_phone`='$collection_person_phone',`zone`='$zone' where id='$contact_id'";
try {
    // $contact_qry;
    $res = Sql_exec($cn, $contact_qry);
} catch (Exception $e) {
    $is_error = 1;
}

if ($is_error == 0) {
    $details_qry = "update contacts set first_name='$first_name', contact_type='$client_type',last_name='$last_name', radius_user='$radius_user', email='$email',phone1='$phone1',phone2='$phone2',address1='$address1',address2='$address2',note='$note', update_date= '" . $date_time . "',update_by='" . $user_id . "',upload_id='$upload_id',date_of_birth='$date_of_birth',stage_id='$stage_id',note_id='$note_id'  where id='$contact_id'";
    try {
        $res = Sql_exec($cn, $details_qry);
    } catch (Exception $e) {
        $is_error = 2;
    }
}

try {
    //   save_change_stage_history($user_id, $contact_id, $stage_id);
} catch (Exception $e) {
    echo 'Message: ' . $e->getMessage();
}

ClosedDBConnection($cn);

echo $is_error;
/**
 * @param $user_id
 * @param $contact_id
 * @param $stage_id
 */
function save_change_stage_history($user_id, $contact_id, $stage_id)
{
    $current_date = date('Y-m-d H:i:s');
    $stage_id = intval($stage_id);
    $user_id = intval($user_id);
    $contact_id = intval($contact_id);
    $cn = connectDB();
    $query = "select * from select_stage where id=$stage_id ";
    $res = Sql_exec($cn, $query);

    while ($dt = Sql_fetch_array(($res))) {
        $coumn_name = $dt['column_name'];
    }
    if (strlen($coumn_name) < 3) {
        $coumn_name = 'attempted';
    }
    $query = "Select * From change_stage_history where user_id= $user_id and contact_id= $contact_id ";
    $res = Sql_exec($cn, $query);
    $id = 0;
    while ($dt = Sql_fetch_array(($res))) {
        $id = $dt['id'];
    }
    $id = intval($id);
    if ($id == 0) {
        $query = "INSERT INTO change_stage_history 	(user_id, 	contact_id, call_start_date, 	attempted, 	attempted_date)VALUES	('$user_id', 	'$contact_id', 	'$current_date',	'1', '$current_date' );";
        $res = Sql_exec($cn, $query);
        $id = Sql_insert_id($cn);

    }
    $query_update = "UPDATE change_stage_history SET ";

    if ($stage_id == '2') {
        $query = $query_update . " connected =1, connected_date = '$current_date' where id='$id' and connected<>1 ;";
        $res = Sql_exec($cn, $query);

    } elseif ($stage_id == '4') {
        $query = $query_update . " interested =1, interested_date = '$current_date' where id='$id' and interested<>1 ;";
        $res = Sql_exec($cn, $query);
        $query = $query_update . " connected =1, connected_date = '$current_date' where id='$id' and connected<>1 ;";
        $res = Sql_exec($cn, $query);
    } elseif ($stage_id == '6') {
        $query = $query_update . " verbally_confirmed =1, verbally_confirmed_date = '$current_date' where id='$id' and verbally_confirmed<>1 ;";
        $res = Sql_exec($cn, $query);
        $query = $query_update . " interested =1, interested_date = '$current_date' where id='$id' and interested<>1 ;";
        $res = Sql_exec($cn, $query);
        $query = $query_update . " connected =1, connected_date = '$current_date' where id='$id' and connected<>1 ;";
        $res = Sql_exec($cn, $query);
    } elseif ($stage_id == '7') {
        $query = $query_update . " sales_done =1, sales_done_date = '$current_date' where id='$id' and sales_done<>1 ;";
        $res = Sql_exec($cn, $query);
        $query = $query_update . " verbally_confirmed =1, verbally_confirmed_date = '$current_date' where id='$id' and verbally_confirmed<>1 ;";
        $res = Sql_exec($cn, $query);
        $query = $query_update . " interested =1, interested_date = '$current_date' where id='$id' and interested<>1 ;";
        $res = Sql_exec($cn, $query);
        $query = $query_update . " connected =1, connected_date = '$current_date' where id='$id' and connected<>1 ;";
        $res = Sql_exec($cn, $query);
    } elseif ($stage_id == '8') {
        $query = $query_update . " delivered =1, delivered_date = '$current_date' where id='$id' and delivered<>1 ;";
        $res = Sql_exec($cn, $query);
        $query = $query_update . " sales_done =1, sales_done_date = '$current_date' where id='$id' and sales_done<>1 ;";
        $res = Sql_exec($cn, $query);
        $query = $query_update . " verbally_confirmed =1, verbally_confirmed_date = '$current_date' where id='$id' and verbally_confirmed<>1 ;";
        $res = Sql_exec($cn, $query);
        $query = $query_update . " interested =1, interested_date = '$current_date' where id='$id' and interested<>1 ;";
        $res = Sql_exec($cn, $query);
        $query = $query_update . " connected =1, connected_date = '$current_date' where id='$id' and connected<>1 ;";
        $res = Sql_exec($cn, $query);
    }


    //	interested, 	interested_date, , 	connected, 	connected_date
    // verbally_confirmed, 	verbally_confirmed_date, 	sales_done, 	sales_done_date, 	delivered, 	delivered_date

    ClosedDBConnection($cn);
}