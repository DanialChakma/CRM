<?php
    /**
     * Created by PhpStorm.
     * User: Talemul
     * Date: 08-Apr-16
     * Time: 4:52 PM
     */

    require_once "../lib/common.php";

    $cn = connectDB();


    $options = '';


    $select_qry = "SELECT id, connection_type  FROM select_connection_type";

    $res = Sql_exec($cn, $select_qry);

    while($dt = Sql_fetch_array(($res))){
        $options .= '<option value="'.trim($dt['id']).'">'.trim($dt['connection_type'])."</option>";
    }

    ClosedDBConnection($cn);

    echo $options;