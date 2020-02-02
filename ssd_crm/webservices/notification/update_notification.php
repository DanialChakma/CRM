<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 9/9/2015
 * Time: 2:31 PM
 */

require_once "../lib/common.php";


$cn = connectDB();

$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
$notification_id = $datainfo['id'];
$type = $datainfo['type'];

if($type=='multiple'){
    $id_array = explode('|',$notification_id);
    $notification_id = implode(',',$id_array);
}

$update_qry = "UPDATE call_history SET notifyme='no' WHERE id IN(".$notification_id.")";

$res = Sql_exec($cn, $update_qry);
$data_array = array();
if($res){
    $data_array = array('status' => true, 'message' =>'Successfully Deleted!');
} else {
    $data_array = array('status' => false, 'message' =>'Deletion Failed!');
}

ClosedDBConnection($cn);

echo json_encode($data_array);