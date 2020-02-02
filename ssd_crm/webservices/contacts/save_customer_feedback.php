<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 6/9/2015
 * Time: 4:49 PM
 * Edited by Talemul Islam
 */

require_once "../lib/common.php";

$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

$cn = connectDB();
//var_dump($_SESSION); exit;


$feedback = mysql_real_escape_string($datainfo["feedback"]);
$action_id = $datainfo["action_id"];
$notifyme = $datainfo['notifyme'];
$id = $_SESSION["user_id"];
$user_id = $_SESSION['user_id'];
$name = $_SESSION["first_name"] . ' ' . $_SESSION["last_name"];
date_default_timezone_set("Asia/Dhaka");
//$date=date('Y-m-d H:i:s');
$date = $datainfo["time"];
$note_id = $datainfo['note_id'];
$is_error = 0;
$star_call_duration = $datainfo["star_call_duration"];
$end_call_duration = $datainfo["end_call_duration"];
$stage_id = isset($datainfo["stage_id"]) ? $datainfo["stage_id"] : 0;
$previous_stage_id = isset($datainfo["previous_stage_id"]) ? $datainfo["previous_stage_id"] : 0;
$total_duration = $end_call_duration - $star_call_duration;
$total_duration = intval($total_duration);

$contact_qry = "insert into call_history(`contact_id`,`call_date`,`call_agent_name`,`call_agent`,`feedback`, update_date,update_by,notifyme,star_call_duration,end_call_duration,total_duration,note_id,stage_id,previous_stage_id) values ($action_id, NOW(),'$name',$id,'$feedback', NOW(),'" . $user_id . "','$notifyme','$star_call_duration','$end_call_duration','$total_duration','$note_id','$stage_id','$previous_stage_id')";
try {
    $res = Sql_exec($cn, $contact_qry);
    adjust_previous_stages($cn,$action_id,$name,$id,$user_id,$stage_id);
} catch (Exception $e) {
    $is_error = 1;
}

ClosedDBConnection($cn);

echo $is_error;


function adjust_previous_stages($cn,$action_id,$name,$id,$user_id,$stage_id){
    $stage_array = array();
    if($stage_id==7){
        $select_qry = "SELECT DISTINCT stage_id FROM call_history WHERE contact_id='$action_id'";
        $result = Sql_exec($cn,$select_qry);
        while($dt = Sql_fetch_array($result)){
            array_push($stage_array,$dt['stage_id']);
        }

        if(!in_array(6,$stage_array)){
            $select_date = "SELECT DISTINCT MIN(call_date) AS call_date FROM call_history WHERE contact_id='$action_id' AND stage_id='7'";
            $result_date = Sql_exec($cn,$select_date);
            while($data = Sql_fetch_array($result_date)){
                $new_call_date = $data['call_date'];
            }
            $insert_query = "INSERT INTO call_history(`contact_id`,`call_date`,`call_agent_name`,`call_agent`,update_date,update_by,stage_id,previous_stage_id) VALUES($action_id, '$new_call_date','$name',$id,'$new_call_date',$user_id,'6','4')";
            Sql_exec($cn, $insert_query);
        }

        if(!in_array(4,$stage_array)){
            $select_date = "SELECT DISTINCT MIN(call_date) AS call_date FROM call_history WHERE contact_id='$action_id' AND stage_id='6'";
            $result_date = Sql_exec($cn,$select_date);
            while($data = Sql_fetch_array($result_date)){
                $new_call_date = $data['call_date'];
            }
            $insert_query = "INSERT INTO call_history(`contact_id`,`call_date`,`call_agent_name`,`call_agent`,update_date,update_by,stage_id,previous_stage_id) VALUES($action_id, '$new_call_date','$name',$id,'$new_call_date',$user_id,'4','2')";
            Sql_exec($cn, $insert_query);
        }
        if(!in_array(2,$stage_array)){
            $select_date = "SELECT DISTINCT MIN(call_date) AS call_date FROM call_history WHERE contact_id='$action_id' AND stage_id='4'";
            $result_date = Sql_exec($cn,$select_date);
            while($data = Sql_fetch_array($result_date)){
                $new_call_date = $data['call_date'];
            }
            $insert_query = "INSERT INTO call_history(`contact_id`,`call_date`,`call_agent_name`,`call_agent`,update_date,update_by,stage_id,previous_stage_id) VALUES($action_id, '$new_call_date','$name',$id,'$new_call_date',$user_id,'2','0')";
            Sql_exec($cn, $insert_query);
        }
    } else if($stage_id==6){
        $select_qry = "SELECT DISTINCT stage_id FROM call_history WHERE contact_id='$action_id'";
        $result = Sql_exec($cn,$select_qry);
        while($dt = Sql_fetch_array($result)){
            array_push($stage_array,$dt['stage_id']);
        }

        if(!in_array(4,$stage_array)){
            $select_date = "SELECT DISTINCT MIN(call_date) AS call_date FROM call_history WHERE contact_id='$action_id' AND stage_id='6'";
            $result_date = Sql_exec($cn,$select_date);
            while($data = Sql_fetch_array($result_date)){
                $new_call_date = $data['call_date'];
            }
            $insert_query = "INSERT INTO call_history(`contact_id`,`call_date`,`call_agent_name`,`call_agent`,update_date,update_by,stage_id,previous_stage_id) VALUES($action_id, '$new_call_date','$name',$id,'$new_call_date',$user_id,'4','2')";
            Sql_exec($cn, $insert_query);
        }
        if(!in_array(2,$stage_array)){
            $select_date = "SELECT DISTINCT MIN(call_date) AS call_date FROM call_history WHERE contact_id='$action_id' AND stage_id='4'";
            $result_date = Sql_exec($cn,$select_date);
            while($data = Sql_fetch_array($result_date)){
                $new_call_date = $data['call_date'];
            }
            $insert_query = "INSERT INTO call_history(`contact_id`,`call_date`,`call_agent_name`,`call_agent`,update_date,update_by,stage_id,previous_stage_id) VALUES($action_id, '$new_call_date','$name',$id,'$new_call_date',$user_id,'2','0')";
            Sql_exec($cn, $insert_query);
        }
    } else if($stage_id==4){
        $select_qry = "SELECT DISTINCT stage_id FROM call_history WHERE contact_id='$action_id'";
        $result = Sql_exec($cn,$select_qry);
        while($dt = Sql_fetch_array($result)){
            array_push($stage_array,$dt['stage_id']);
        }

        if(!in_array(2,$stage_array)){
            $select_date = "SELECT DISTINCT MIN(call_date) AS call_date FROM call_history WHERE contact_id='$action_id' AND stage_id='4'";
            $result_date = Sql_exec($cn,$select_date);
            while($data = Sql_fetch_array($result_date)){
                $new_call_date = $data['call_date'];
            }
            $insert_query = "INSERT INTO call_history(`contact_id`,`call_date`,`call_agent_name`,`call_agent`,update_date,update_by,stage_id,previous_stage_id) VALUES($action_id, '$new_call_date','$name',$id,'$new_call_date',$user_id,'2','0')";
            Sql_exec($cn, $insert_query);
        }
    }
}
