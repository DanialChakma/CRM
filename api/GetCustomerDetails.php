<?php
    /**
     * Created by PhpStorm.
     * User: Talemul
     * Date: 25-Jan-16
     * Time: 11:35 AM
     */
    require_once "../ssd_crm/webservices/lib/common.php";
    $cn=connectDB();
    $phone_no = isset($_REQUEST['phone']) ? $_REQUEST['phone'] : exit;
    $phone_no = strrev($phone_no);
    $rev_string = '';
    $get_ten = 1;
    for ($i = 0; $i < strlen($phone_no); $i++) {
        if (is_numeric($phone_no[$i])) {
            $rev_string = $rev_string . $phone_no[$i];
            if ($get_ten == 10) {
                break;
            } else {
                $get_ten++;
            }


        }
    }

    $phone_no = strrev($rev_string);

    $qry = 'SELECT * FROM contacts WHERE phone1 like "%' . $phone_no . '%" limit 1';
    $res = Sql_exec($cn, $qry);
    $datainfo = "Faild|Data not found.";
    while ($dt = Sql_fetch_array(($res))) {
        $datainfo = "+OK|" . $dt['first_name'] . ' ' . $dt['last_name'] . "|" . $dt['address1'];

    }

    echo $datainfo;