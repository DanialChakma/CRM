<?php
    /**
     * Created by PhpStorm.
     * User: Talemul
     * Date: 12/17/2015
     * Time: 3:29 PM
     */

    require_once "../lib/common.php";

    $cn = connectDB();

    $today = date("Y-m-d H:i:s");

    $returnValue = array();
    $returnValue['status'] = false;
    $returnValue['data'] = 'admin';

    $user_id = $_SESSION['user_id'];
    $user_role=$_SESSION['user_role'];

    $select_qry = "SELECT * FROM contacts INNER JOIN user_info ON user_info.user_id=contacts.assign_to
WHERE assign_to = '$user_id' and customer_type IN ('lead','prospect','closed') AND next_call_date<>'0000-00-00 00:00:00' AND  next_call_date < '$today' limit 5";
    $result = Sql_exec($cn, $select_qry);

    $data_array = array();
    $i = 0;


    while ($dt = Sql_fetch_array($result)) {
        $j = 0;

        $data_array[$i][$j++] = $dt['id'];
        $data_array[$i][$j++] = $dt['first_name']. ' '.$dt['last_name'];
        $data_array[$i][$j++] = $dt['phone1'];
        $data_array[$i][$j++] = date('Y-m-d', strtotime($dt['next_call_date']));
        $data_array[$i][$j++] = date(' h:i:s A', strtotime($dt['next_call_date']));
        $i++;
    }

    ClosedDBConnection($cn);

if(strtolower('Retail')==strtolower($user_role)){
    $returnValue['status'] = true;
    $returnValue['data'] = $data_array;

}else{
    $returnValue['status'] = false;
    $returnValue['data'] = 'admin';
}

    echo json_encode($returnValue);
