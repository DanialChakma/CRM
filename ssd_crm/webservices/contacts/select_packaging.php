<?php
    /**
     * Created by PhpStorm.
     * User: Talemul
     * Date: 08-Apr-16
     * Time: 4:51 PM
     */
    require_once "../lib/common.php";

    $cn = connectDB();


    $options = '';


    $select_qry = "SELECT id, packaging  FROM select_packaging";

    $res = Sql_exec($cn, $select_qry);

    while($dt = Sql_fetch_array(($res))){
        $options .= '<option value="'.trim($dt['id']).'">'.trim($dt['packaging'])."</option>";
    }

    ClosedDBConnection($cn);

    echo $options;