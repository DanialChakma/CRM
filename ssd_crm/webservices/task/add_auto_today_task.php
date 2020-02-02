<?php
    /**
     * Created by PhpStorm.
     * User: Talemul
     * Date: 12/18/2015
     * Time: 12:36 PM
     */
    require_once "../lib/common.php";

    //  $datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

    $cn = connectDB();
    $user_role = $_SESSION['user_role'];
//var_dump($_SESSION); exit;
    $reslt_data = array();
    $reslt_data['status'] = false;


    if (strtolower('Retail') != strtolower($user_role)) {
        $reslt_data['msg'] = 'This user is ' . $user_role . '. | Task is not assign.';
        echo json_encode($reslt_data);
        exit;

    }

    $task_name = $_SESSION['user_name'] . '_' . date('Y_m_d_H_i_s');
    $task_catagory = 1;
    $assignto = $_SESSION['user_id'];
    $user_group_id = 0;
    $assignby = 'auto';
    $due_date = date('Y-m-d H:i:s');
    $TaskAddDetails = 'Atuo Task assign on ' . date('Y-m-d H:i:s') . ' .';
    $contact_id_list = '';//$datainfo["contact_id_list"];
    $update_by = 'admin';
    $query_check = "SELECT * FROM work_task WHERE work_task.assign_by='auto' AND work_task.assign_to=$assignto  AND DATE_FORMAT( work_task.assign_date,'%Y-%d-%m')=DATE_FORMAT( NOW(),'%Y-%d-%m')";
    $res = Sql_exec($cn, $query_check);
    $check_id = -1;
    while ($dt = Sql_fetch_array(($res))) {
        $check_id = $dt['id'];
        break;
    }
    if ($check_id > 0) {
        $reslt_data['msg'] = 'This user task is already assign.';
        echo json_encode($reslt_data);
        exit;
    }
    $query_contact_list = "(SELECT * FROM contacts WHERE customer_type IN ('lead','prospect') AND assign_to='$assignto' AND next_call_date <NOW()) UNION (SELECT * FROM contacts WHERE customer_type IN ('lead','prospect') AND assign_to<=0  AND next_call_date <NOW()) LIMIT 20";
    $res = Sql_exec($cn, $query_contact_list);
    $ids = '';
    $pipe='';
    while ($dt = Sql_fetch_array(($res))) {
        $contact_id_list = $contact_id_list .$pipe. $dt['id'];
        $pipe='|';
    }
    $select_qry = "SELECT
  id        AS `ids`,
  node_name
FROM work_flow_details
WHERE work_flow_id = '$task_catagory'
    AND work_flow_serial = (SELECT
                              MIN(work_flow_serial)
                            FROM work_flow_details
                            WHERE work_flow_id = '$task_catagory')";
//echo $select_qry.' ';

    $res = Sql_exec($cn, $select_qry);
    $ids = '';
    while ($dt = Sql_fetch_array(($res))) {
        $ids = $dt['ids'];
    }

    if ($ids == null || $ids == '') {

        $insert_qry = "insert into `work_task`(`task_title`,`work_flow_id`,`task_description`,`task_status`,`assign_by`,`assign_to`,`user_group_id`,`assign_date`,`due_date`,`member_node_id`,`update_date`,`update_by`) values ('$task_name','$task_catagory','$TaskAddDetails','new','$assignby','$assignto','$user_group_id',NOW(),'$due_date','',NOW(),'$update_by')";
    } else {

        $insert_qry = "insert into `work_task`(`task_title`,`work_flow_id`,`task_description`,`task_status`,`assign_by`,`assign_to`,`user_group_id`,`assign_date`,`due_date`,`member_node_id`,`update_date`,`update_by`) values ('$task_name','$task_catagory','$TaskAddDetails','new','$assignby','$assignto','$user_group_id',NOW(),'$due_date',$ids,NOW(),'$update_by')";
    }

    try {

        $res = Sql_exec($cn, $insert_qry);
        $affected_row = mysql_insert_id();

        //print_r($affected_row);
        if ($res && $affected_row > 0) {
            $vaues = '';
            $contact_ids = '(';
            $seperator = '';
            if (trim($contact_id_list) != '' && $contact_id_list != null) {

                $list = array();
                $list = explode('|', $contact_id_list);
                //print_r($list);
                foreach ($list as $idd) {
                    $vaues .= $seperator . "('$affected_row','$idd')";
                    $contact_ids .= $seperator . $idd;
                    $seperator = ',';
                }
                $contact_ids .= ')';

                $insert_qry2 = "insert into work_task_contact(`work_task_id`,`contact_id`) values $vaues";
                $insert_qry3 = "UPDATE contacts SET assign_to=$assignto WHERE id IN " . $contact_ids;

                try {
                     $insert_qry2;
                    $res2 = Sql_exec($cn, $insert_qry2);
                    $res3 = Sql_exec($cn, $insert_qry3);
                    $reslt_data['status'] = true;
                    $reslt_data['msg'] = 'task created';
                    $reslt_data['ins2'] = $insert_qry2;
                    $reslt_data['ins3'] = $insert_qry3;

                } catch (Exception $e) {

                    $reslt_data['status'] = false;
                    $reslt_data['msg'] = $e;
                }
            }
        } else {

            $reslt_data['status'] = false;
            $reslt_data['msg'] = 'error';

        }
    } catch (Exception $e) {

        $reslt_data['status'] = false;
        $reslt_data['msg'] = $e;

    }

    ClosedDBConnection($cn);

    echo json_encode($reslt_data);