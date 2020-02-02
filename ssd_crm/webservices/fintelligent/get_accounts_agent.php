<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 8/18/2016
 * Time: 6:21 PM
 */

require_once "../lib/common.php";
$params = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
$agent_type = trim($params['agent_type']);
$cn = connectDB();

$qry = "SELECT user_id,user_name,CONCAT_WS(\" \",first_name,last_name) AS agent_name
        FROM user_info WHERE user_role='$agent_type';";

$rs = Sql_exec($cn,$qry);
$options_string = "<option value=\"\">"."--Select--"."</option>";
while($dt=Sql_fetch_array($rs)){
    $options_string .= "<option value=\"".trim($dt['user_name'])."\">".trim($dt['agent_name'])."</option>";
}
ClosedDBConnection($cn);
echo $options_string;



