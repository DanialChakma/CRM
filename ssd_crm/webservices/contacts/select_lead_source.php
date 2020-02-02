<?php
/**
 * Created by PhpStorm.
 * User: Talemul
 * Date: 7/7/2015
 * Time: 12:29 PM
 */

require_once "../lib/common.php";

$cn = connectDB();


$options = '';

$select_qry = "SELECT DISTINCT lead_source  FROM contacts";

$res = Sql_exec($cn, $select_qry);

while($dt = Sql_fetch_array(($res))){
    $options .= '<option value="'.trim($dt['lead_source']).'">'.trim($dt['lead_source'])."</option>";
}


ClosedDBConnection($cn);

echo $options;