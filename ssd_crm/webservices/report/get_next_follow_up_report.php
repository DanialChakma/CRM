<?php
    /**
     * Created by PhpStorm.
     * User: Talemul
     * Date: 12/15/2015
     * Time: 4:11 PM
     */


    require_once "../lib/common.php";

    $cn = connectDB();

    $today = date("Y-m-d");

    $date_from = $today . " 00:00:00";
    $date_to = $today . " 23:59:59";

    if (isset($_REQUEST) && $_REQUEST) {
        $data = $_REQUEST['info'];
        $date_from = $data["date_from"];
        $date_to = $data["date_to"];
    }
    $user_role = $_SESSION['user_role'];
    $user_id = $_SESSION['user_id'];

    $stage_map = array();
    $stage_qry = "SELECT id, stage FROM select_stage";
    $rs = Sql_exec($cn,$stage_qry);
    while($row = Sql_fetch_array($rs)){
        $stage_map[trim($row['id'])] = $row['stage'];
    }
    Sql_Free_Result($rs);



    $select_qry = "SELECT contacts.next_call_date as 'next_call_date',contacts.stage_id, contacts.phone1 AS 'contacts',user_info.`user_name` AS 'user_name',contacts.`id` as 'id',
                  (SELECT feedback FROM call_history WHERE call_history.`contact_id`=contacts.`id` ORDER BY id DESC LIMIT 1) AS 'feedback'
                    FROM contacts INNER JOIN user_info ON user_info.user_id=contacts.update_by
                    WHERE next_call_date<>'0000-00-00 00:00:00' AND  next_call_date BETWEEN '$date_from' AND '$date_to'";
    if(strtolower($user_role)!='admin'){
        $select_qry=$select_qry.' and  assign_to='.$user_id;
    }
    $result = Sql_exec($cn, $select_qry);

    $data_array = array();
    $i = 0;


    while ($dt = Sql_fetch_array($result)) {
        $j = 0;
        $data_array[$i][$j++] = $i + 1;
        $data_array[$i][$j++] = $dt['contacts'];
        $data_array[$i][$j++] = $dt['user_name'];
        $data_array[$i][$j++] = date('Y-m-d', strtotime($dt['next_call_date']));
        $data_array[$i][$j++] = date(' h:i:s A', strtotime($dt['next_call_date']));
        $data_array[$i][$j++] = $dt['feedback'];
        $stage_name = (array_key_exists(trim($dt['stage_id']),$stage_map) ? $stage_map[trim($dt['stage_id'])]: "");
        $data_array[$i][$j++] = $stage_name;
        $i++;
    }

    ClosedDBConnection($cn);

    echo json_encode($data_array);