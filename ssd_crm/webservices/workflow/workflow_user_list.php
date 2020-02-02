<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 9/7/2015
 * Time: 6:55 PM
 */

require_once "../lib/common.php";

$cn = connectDB();

$html = '';

$select_qry = "select * from user_info";

$res = Sql_exec($cn, $select_qry);

while ($dt = Sql_fetch_array(($res))) {
    $html .= '<div style="width: 300px; border-bottom: 1px solid #ffffff"><input type="checkbox" class="subjectid" value="' .$dt['user_id'].'"> '.$dt['first_name'].' '.$dt['last_name'].'</div>';
}

ClosedDBConnection($cn);

echo $html;