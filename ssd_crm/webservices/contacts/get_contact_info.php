<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 6/9/2015
 * Time: 4:49 PM
 */

require_once "../lib/common.php";

$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

$cn = connectDB();
//var_dump($_SESSION); exit;

$action_id = $datainfo["action_id"];

$reslt_data=array();

$select_qry = "SELECT * FROM contacts WHERE id=$action_id";

$res = Sql_exec($cn, $select_qry);

if($dt = Sql_fetch_array(($res))){
    echo json_encode($dt);
}

ClosedDBConnection($cn);
