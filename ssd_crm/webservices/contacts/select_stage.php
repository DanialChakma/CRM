<?php
/**
 * Created by PhpStorm.
 * User: Talemul
 * Date: 11/27/2015
 * Time: 3:33 PM
 */


require_once "../lib/common.php";

$cn = connectDB();


$options = '';

$select_qry = "SELECT *   FROM select_stage";

$res = Sql_exec($cn, $select_qry);

while($dt = Sql_fetch_array(($res))){
    $options .= '<option value="'.trim($dt['id']).'">'.trim($dt['stage'])."</option>";
}


ClosedDBConnection($cn);

echo $options;