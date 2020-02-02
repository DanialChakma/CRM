<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 9/7/2015
 * Time: 7:38 PM
 */

require_once "../lib/common.php";

$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

$cn = connectDB();
$error = 0;
$group_id = $datainfo['user_list_id'];

if ($datainfo['action'] == 'Select') {


    $checked = $datainfo['checked'];
    $group_name = $datainfo['name'];

    $query = "select count(group_name) from groups where group_name='$group_name'";
    $res = Sql_exec($cn, $query);
    $name = Sql_fetch_array($res);
//echo $name['count(name)'];
    if ($name['count(name)'] == 0) {
        $query = "INSERT INTO groups (group_name) values ('$group_name')";

        try {
            $result = Sql_exec($cn, $query);
            $query = "select id from groups where group_name='$group_name'";
            $res = Sql_exec($cn, $query);
            $id = Sql_fetch_array($res);
            $group_id = $id['id'];
            $query = "INSERT INTO user_group (group_id,user_id) values ";
            for ($i = 0; $i < count($checked) - 1; $i++) {
                $query = $query . "( '$group_id'," . $checked[$i] . "),";
            }
            $query = $query . "( '$group_id'," . $checked[$i] . ")";
            try {
                $result = Sql_exec($cn, $query);

                if ($result) {
                    $error = 0;
                }
            } catch (Exception $e) {
                $error = 1;
                echo json_encode(array("status" => false, "message" => "Fail!"));
                exit;
            }
        } catch (Exception $e) {
            $error = 1;
            echo json_encode(array("status" => false, "message" => "Fail!"));
            exit;
        }
    } else {
        $error = 1;
        echo json_encode(array("status" => false, "message" => "Group Name Exist!"));
        exit;
    }
}

if ($error == 0) {
    $error = 1;
    $query = "update work_flow_details set list_id='$group_id' where id=" . $datainfo['node_name_list'];
    $res = Sql_exec($cn, $query);

    if ($res) {
        echo json_encode(array("status" => true, "message" => "Successfully Submitted!"));
    }else{
        echo json_encode(array("status" => false, "message" => "Fail!"));
    }
}
