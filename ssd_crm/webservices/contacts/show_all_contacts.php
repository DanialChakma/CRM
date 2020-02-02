<?php

require('../lib/DataTableServerSideCustom.php');
//print_r($_REQUEST);
//exit;
// DB table to use
//$table = 'users';
//$table = '`molpay_api` LEFT JOIN `users` ON (`molpay_api`.`uid` = `users`.`username` OR `molpay_api`.`uid` = CONCAT(`users`.`username`, \'@dhakagate.com\'))';
$stage_map = array();
$agent_map = array();
$cn_instance = myDatabaseConnector::getinstance();
$qry = "SELECT uinfo.user_id, TRIM(CONCAT_WS(' ',uinfo.first_name,uinfo.last_name)) AS 'agent' FROM user_info uinfo";
$rs = $cn_instance->Sql_exec($qry);
while($row = $cn_instance->Sql_fetch_array($rs)){
    $agent_map[trim($row['user_id'])] = $row['agent'];
}

$cn_instance->Sql_Free_Result($rs);

$stage_qry = "SELECT id, stage FROM select_stage";
$rs = $cn_instance->Sql_exec($stage_qry);
while($row = $cn_instance->Sql_fetch_array($rs)){
    $stage_map[trim($row['id'])] = $row['stage'];
}
$cn_instance->Sql_Free_Result($rs);
$cn_instance->ClosedDBConnection();


$table = 'contacts';
// Table's primary key
$primaryKey = 'id';


//$condition=' customer_type="lead" ';
//print_r($_REQUEST);
$condition = $_REQUEST['info']['page'];
if ($condition == 'l7') {
    if (!isset($_REQUEST['info']['alpha'])) {
        $d = strtotime("-7 Days");
        $date = date("Y-m-d", $d);
        $condition = " assign_to <=0 and id  IN (SELECT DISTINCT(contact_id) FROM call_history WHERE call_date > " . $date . ") ";
    } else {
        $alpha = $_REQUEST['info']['alpha'];
        $alphalow = chr(ord($alpha) + 32);
        // echo $alpha.$alphalow;
        $d = strtotime("-7 Days");
        $date = date("Y-m-d", $d);
        $condition = " assign_to <=0 and id  IN (SELECT DISTINCT(contact_id) FROM call_history WHERE call_date > " . $date . ")
        AND (first_name LIKE '" . $alphalow . "%' OR first_name LIKE'" . $alpha . "%' )
        OR ((first_name = '' OR first_name = NULL)AND (last_name LIKE '" . $alphalow . "%' OR last_name LIKE '" . $alpha . "%')) ";
    }
    $_REQUEST['info']['qryCondition'] = $condition;
} else if ($condition) {
    //echo "ok ".$condition; exit;
    $_REQUEST['info']['qryCondition'] = $condition;
    //echo $condition;
}
$columns = array(
    array('db' => 'id', 'dt' => 'part1', 'formatter' => function ($d, $row) {
        if ($d == null || $d == '')
            return ' ';
        else {
            if ( $row['first_name'] == "" && $row['last_name'] == "" )
                $ret = '<div class="check-box chk-area">'
                    . '<input class="subjectid" name="SubjectID" value="' . $d . '" type="checkbox">'
                    . '</div>'
                    . '<div class="contact-box">'
                    . '<a class="noselect" href="#">'
                    . '<img class="align-left" src="https://d30chhj7mra175.cloudfront.net/img/icon-list-contact.png" alt="" height="32" width="32"></a>'
                    . '<div class="link-box">'
                    . '<strong class="title">'
                    . '<a onclick=\'show_detail_lead(' . $d . ')\' class="link-person noselect" href="#">' . $row['phone1'] . ' ('.$d.')</a>'
                    . ' <i class="icon-user opacity02" title="System User"></i>'
                    . '</strong>'
                    . '<div class="contact-text">'
                    . '<a class="nopjax" target="_blank" href="#">' . $row['email'] . '</a> <span class="text">(' . $row['phone1'] . ')</span>'
                    . ' </div>'
                    . '</div>'
                    . ' </div>';
            else
                $ret = '<div class="check-box chk-area">'
                    . '<input class="subjectid" name="SubjectID" value="' . $d . '" type="checkbox">'
                    . '</div>'
                    . '<div class="contact-box">'
                    . '<a class="noselect" href="#">'
                    . '<img class="align-left" src="https://d30chhj7mra175.cloudfront.net/img/icon-list-contact.png" alt="" height="32" width="32"></a>'
                    . '<div class="link-box">'
                    . '<strong class="title">'
                    . '<a onclick=\'show_detail_lead(' . $d . ')\' class="link-person noselect" href="#">' . $row['first_name'] . ' ' . $row['last_name'] . ' (' . $d . ')</a>'
                    . ' <i class="icon-user opacity02" title="System User"></i>'
                    . '</strong>'
                    . '<div class="contact-text">'
                    . '<a class="nopjax" target="_blank" href="#">' . $row['email'] . '</a> <span class="text">(' . $row['phone1'] . ')</span>'
                    . ' </div>'
                    . '</div>'
                    . ' </div>';
            return $ret;
        }
    }),
    array('db' => 'phone1', 'dt' => 'part2', 'formatter' => function ($d, $row) {
        if ($d == null || $d == '')
            return ' ';
        else {
            $ret = '<div style="float:left;">'

                . '<span class="contact-text">'
                . $row['address1'] . '(' . $row['lead_source'] . ')'
                . '</span>'
                . ' </div>';
            return $ret;
        }
    }),
    array('db' => 'assign_to', 'dt' => 'part3','formatter' => function ($d, $row) {
        global $agent_map;
        if ($d == null || $d == '')
            return ' ';
        else {
            $ret = '<div style="float:left;">'

                . '<span class="contact-text">'
                . (array_key_exists(trim($row['assign_to']),$agent_map) ? $agent_map[trim($row['assign_to'])]: "")
                . '</span>'
                . ' </div>';
            return $ret;
        }
    }),
    array('db' => 'stage_id', 'dt' => 'part4','formatter' => function ($d, $row) {
        global $stage_map;
        if ($d == null || $d == '')
            return ' ';
        else {
            $ret = '<div style="float:left;">'

                . '<span class="contact-text">'
                . (array_key_exists(trim($row['stage_id']),$stage_map) ? $stage_map[trim($row['stage_id'])]: "")
                . '</span>'
                . ' </div>';
            return $ret;
        }
    })
);
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line. NewDozeCrm
 */
   //$_REQUEST['info']['qryCondition']=$_REQUEST['info']['qryCondition'].'888';
$input_data = isset($_REQUEST) ? $_REQUEST : exit;
//print_r($input_data);
$dataTableServer = new DataTableServer();
echo json_encode(
    $dataTableServer->simplePagination($input_data, $table, $columns)
);

unset($agent_map);
unset($stage_map);