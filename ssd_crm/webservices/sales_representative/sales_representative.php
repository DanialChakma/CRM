<?php


    include_once "../lib/common.php";

    $data_req = $_REQUEST['info'];

    $cn = connectDB();

    $arrayInput = array();

    $query = "SELECT * FROM user_info  where user_status=0";

    $result = Sql_exec($cn, $query);

    if (!$result) {
        echo "err+" . $query . " in line " . __LINE__ . " of file" . __FILE__;
        exit;
    }
    $data = array();
    $i = 0;
//Sql_Num_Rows($result);
    for (; $i < Sql_Num_Rows($result); $i++) {
        $row = Sql_fetch_array($result);
        $j = 0;
        $data[$i][$j++] = $row['user_id'];
        $data[$i][$j++] = $row['first_name'];
        $data[$i][$j++] = $row['last_name'];
        $data[$i][$j++] = $row['user_address'];
        $data[$i][$j++] = $row['user_phone'];;
        $u_info = json_encode($row);
        // $info = '' . Sql_Result($row, "id") . '|' . Sql_Result($row, "gre_name") . '|' . Sql_Result($row, "peer_outer") . '|' . Sql_Result($row, "peer_inner") . '|' . Sql_Result($row, "my_inner");
        $data[$i][$j++] = "<a href='#' title='Details' class='text_green'  onclick='update_user_info($u_info)' style='color: #0a8e03'>Details</a>"."<span style='padding-left:20px;'></span>       <a href='#' title='Details' class='text_green'  onclick='delete_user_info(".$row['user_id'].");' style='color:red'>Delete</a>";
    }


    Sql_Free_Result($result);

    ClosedDBConnection($cn);

    echo json_encode($data);

?>