<?php

require_once "../lib/common.php";
$cn = connectDB();
$checked = $_REQUEST['info']['checked'];
$group_name = $_REQUEST['info']['name'];
$action = $_REQUEST['info']['action'];

$query = "select count(group_name) from groups where group_name='$group_name'";
$res = Sql_exec($cn, $query);
$name = Sql_fetch_array($res);
//echo $action; exit;

if ($name['count(group_name)'] == 0 || $action=='update') {
    if($action!='update'){
    $query = "INSERT INTO groups (group_name) values ('$group_name')";
    }
    else {
        $query="DELETE user_group FROM user_group INNER JOIN groups ON groups.id=user_group.group_id WHERE group_name='$group_name'";
    }
    try {
        $result = Sql_exec($cn, $query);
        $query = "select id from groups where group_name='$group_name'";
        $res = Sql_exec($cn, $query);
        $id = Sql_fetch_array($res);
        $group_id=$id['id'];
        $query = "INSERT INTO user_group (group_id,user_id) values ";
        for ($i = 0; $i < count($checked) - 1; $i++) {
            $query = $query . "( '$group_id'," . $checked[$i] . "),";
        }
        $query = $query . "( '$group_id'," . $checked[$i] . ")";
        try {
            $result = Sql_exec($cn, $query);
            echo json_encode(array("status" => "yes", "message" => "Successful"));
        } catch (Exception $e) {
            echo json_encode(array("status" => "no", "message" => "Failure"));
        }
    } catch (Exception $e) {
        echo json_encode(array("status" => "no", "message" => "Failure"));
    }
} else {

    echo json_encode(array("status" => "no", "message" => "Exist"));
}