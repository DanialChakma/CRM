<?php
    /**
     * Created by PhpStorm.
     * User: Nazibul
     * Date: 8/31/2015
     * Time: 5:57 PM
     */

    require_once "../lib/common.php";

    $datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

    $cn = connectDB();
//var_dump($_SESSION); exit;
    $reslt_data = array();
    $reslt_data['status'] = false;
    $reslt_data['msg'] = 'noMsg';

    $task_name = $datainfo["task_name"];
    $task_catagory = $datainfo["task_catagory"];
    $assignto = $datainfo["assignto"];
    $user_group_id = $datainfo["user_group_id"];
    $assignby = 'admin';
    $due_date = $datainfo["due_date"];
    $TaskAddDetails = $datainfo["TaskAddDetails"];
    $contact_id_list = $datainfo["contact_id_list"];
    $update_by = 'admin';

    $insert_qry2 = "";
    $insert_qry3 = "";
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
                $insert_qry3 = "UPDATE contacts SET assign_to=$assignto WHERE assign_to<=0 AND id IN " . $contact_ids;
                //echo 'po' . $insert_qry2;
                //  echo $insert_qry2.'|'.$insert_qry3;
                try {

                    $res2 = Sql_exec($cn, $insert_qry2);
                    $res3 = Sql_exec($cn, $insert_qry3);
                    $reslt_data['status'] = true;
                    $reslt_data['msg'] = 'task created';
                    $reslt_data['ins2'] = $insert_qry2;
                    $reslt_data['ins3'] = $insert_qry3;

                } catch (Exception $e) {

                    $reslt_data['status'] = false;
                    $reslt_data['msg'] = $e . '12';
                    $reslt_data['ins2'] = $insert_qry2;
                    $reslt_data['ins3'] = $insert_qry3;
                }
            } else {
                $reslt_data['status'] = true;
                $reslt_data['msg'] = 'Task Created . But this task has no contact to call.';
                $reslt_data['ins2'] = $insert_qry2;
                $reslt_data['ins3'] = $insert_qry3;
            }
        } else {

            $reslt_data['status'] = false;
            $reslt_data['msg'] = 'error' . '33';
            $reslt_data['ins2'] = $insert_qry2;
            $reslt_data['ins3'] = $insert_qry3;

        }
    } catch (Exception $e) {

        $reslt_data['status'] = false;
        $reslt_data['msg'] = $e . '44';
        $reslt_data['ins2'] = $insert_qry2;
        $reslt_data['ins3'] = $insert_qry3;

    }

    ClosedDBConnection($cn);

    echo json_encode($reslt_data);