<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 7/2/2015
 * Time: 3:08 PM
 */

require_once "../lib/common.php";

$cn = connectDB();

$options = '<option value="">--select--</option>';

$select_qry = "SELECT user_name FROM user_info WHERE LOWER(user_role) != 'admin'";
$result = Sql_exec($cn,$select_qry);

while($dt = Sql_fetch_array($result)){
    $options .= '<option value="'.trim($dt['user_name']).'">'.trim($dt['user_name'])."</option>";
}

ClosedDBConnection($cn);

echo $options;
