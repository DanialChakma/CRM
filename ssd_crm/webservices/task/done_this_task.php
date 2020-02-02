<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 9/3/2015
 * Time: 3:58 PM
 */

require_once "../lib/common.php";

$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

$cn = connectDB();
//var_dump($_SESSION); exit;

delete_contact_task($datainfo['id'], $cn);

$reslt_data = array();
$result_data['status'] = false;

$select_qry = "UPDATE work_task SET update_date = NOW(), task_status = 'processing', work_task.member_node_id = (SELECT work_flow_details.node_id_nxt FROM work_flow_details WHERE work_flow_details.id = work_task.member_node_id  AND work_task.id='" . $datainfo['id'] . "') WHERE id = " . $datainfo['id'];


$result_data['query'] = $select_qry;

$res = Sql_exec($cn, $select_qry);

if ($res) {
    $update_qry2 = "UPDATE work_task SET task_status = 'done' WHERE member_node_id IS NULL AND id = " . $datainfo['id'];
    $result_data['query2'] = $update_qry2;
    $res = Sql_exec($cn, $update_qry2);

    if ($res) {
        $result_data['status'] = true;
    } else {
        $result_data['status'] = false;
    }
} else {
    $result_data['status'] = false;
}

ClosedDBConnection($cn);

echo json_encode($result_data);

function delete_contact_task($task_id, $cn)
{
    $select_qry = "SELECT * FROM work_task WHERE id=$task_id";

    $res = Sql_exec($cn, $select_qry);

    while ($dt = Sql_fetch_array($res)) {
        if (($dt['work_flow_id'] != -1 || $dt['work_flow_id'] != '-1') && ($dt['task_status'] != 'done')) {
            if ($dt['member_node_id'] == 3 || $dt['member_node_id'] == '3') {
                $delete_qry = "DELETE
                                FROM work_task_contact
                                WHERE work_task_id = '$task_id'
                                    AND contact_id IN(SELECT
                                                        contacts.id
                                                      FROM contacts
                                                        JOIN ((SELECT *
                                                               FROM work_task_contact
                                                               WHERE work_task_id = '$task_id') tmp)
                                                          ON (tmp.contact_id = contacts.id)
                                                      WHERE contacts.customer_type = 'lead'
                                                           OR contacts.customer_type = 'prospect')";

                $res = Sql_exec($cn, $delete_qry);

            } else if ($dt['member_node_id'] == 1 || $dt['member_node_id'] == '1') {
                $delete_qry = "DELETE
                                FROM work_task_contact
                                WHERE work_task_id = '$task_id'
                                    AND contact_id IN(SELECT
                                                        payments.contact_id
                                                      FROM payments
                                                        JOIN ((SELECT *
                                                               FROM work_task_contact
                                                               WHERE work_task_id = '$task_id') tmp)
                                                          ON (tmp.contact_id = payments.contact_id)
                                                      WHERE payments.collection_status != 'closed')";

                $res = Sql_exec($cn, $delete_qry);
            } else if ($dt['member_node_id'] == 2 || $dt['member_node_id'] == '2') {
                $delete_qry = "DELETE
                                FROM work_task_contact
                                WHERE work_task_id = '$task_id'
                                    AND contact_id IN(SELECT
                                                        otrs_ticket.contact_id
                                                      FROM otrs_ticket
                                                        JOIN ((SELECT *
                                                               FROM work_task_contact
                                                               WHERE work_task_id = '$task_id') tmp)
                                                          ON (tmp.contact_id = otrs_ticket.contact_id)
                                                      WHERE otrs_ticket.ticket_number = ''
                                                           OR otrs_ticket.ticket_number IS NULL)";

                $res = Sql_exec($cn, $delete_qry);
            }
        }
    }

}
