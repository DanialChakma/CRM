<?php

require_once "../lib/common.php";
require_once "fin_lib.php";
//session_start();
//$user_name = $_SESSION['user_name'];
//$user_role = $_SESSION['user_role'];

$get_tasks_url = "http://localhost/dozecrm/ssd_crm/webservices/fintelligent/get_tasks.php";
$user_name = "";
$user_role = "";
$data = array(
    "user_name"=>trim($user_name),
    "user_role"=>$user_role
);

$param = http_build_query($data);
$url_to_hit = $get_tasks_url."?".$param;

$req_method = "GET";
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
curl_setopt($ch, CURLOPT_URL, $url_to_hit);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"GET");
$response = "";
if( ($response = curl_exec($ch)) === FALSE ){
    // echo "ERROR:". curl_error($ch);
}else{
    //  echo "Server Res:".$response;
}


$current_time_stamp = date('Y-m-d H:i:s');
//$client_ip = get_client_ip();
if( curl_errno($ch) )
{

}else{

    $res_data_arr = json_decode($response,TRUE);

    $count = count($res_data_arr);

    $dataset = array();
    $i = 0;
    for($k=0;$k<$count;$k++){
        $j = 0;
        $customer_id =  trim($res_data_arr[$k]["contact_id"]);
        $dataset[$i][$j++] = $res_data_arr[$k]["id"];
        $dataset[$i][$j++] = $res_data_arr[$k]["contact_id"];
        //  $dataset[$i][$j++] = $res_data_arr[$k]["conversion_date"];
        //  $dataset[$i][$j++] = $res_data_arr[$k]["install_cost"];
        //  $dataset[$i][$j++] = $res_data_arr[$k]["monthly_cost"];
        $action_string = '<a href="#" onclick="view_task_details(\''.$customer_id.'\')">Details</a>';
        $reassign_button = '<a href="#" onclick="reassign_task(\''.$customer_id.'\')">Reassign</a>';
  //      $approve_string = '<a href="#" onclick="approve_task(\''.$customer_id.'\')">Approve</a>';
  //      $hold_string = '<a href="#" onclick="hold_task(\''.$customer_id.'\')">Hold</a>';
 //       $reject_string = '<a href="#" onclick="hold_task(\''.$customer_id.'\')">Reject</a>';
        $dataset[$i][$j++] = $action_string;
        $dataset[$i][$j++] = $reassign_button;


        $i++;
    }


    echo json_encode($dataset);


}
