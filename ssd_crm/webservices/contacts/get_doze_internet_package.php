<?php

require_once "../lib/common.php";

$cn = connectDB();


$options = '';

//$apiUrl = "http://selfcare.dozeinternet.com/selfcare/webservices/getinternetpackage.php?cmdparam";
//$response = file_get_contents($apiUrl);
//
//$dataPackage = explode("\n", $response);
//
//$arrayResult = array();
//if ($dataPackage[0] == '+OK') {
//    $result = explode("|", $dataPackage[2]);
//    $length = $dataPackage[1];
//
//    for ($x = 3; $x < ($length + 3); $x++) {
//        $value = explode("|", $dataPackage[$x]);
//
//        $arrayResult[$x - 3] = array();
//        for ($y = 0; $y < sizeof($value); $y++) {
//            $arrayResult[$x - 3][$result[$y]] = $value[$y];
//        }
//    }
//}
//
//foreach($arrayResult as $key => $value){
//    $options .= '<option value="'.trim($value['SubService']).'">'.$value['SubService']."</option>";
//}


$select_qry = "SELECT * FROM internet_package";
$result = Sql_exec($cn,$select_qry);

while($dt = Sql_fetch_array($result)){
    $options .= '<option value="'.trim($dt['bandwidth']).' '.trim($dt['volume']).'">'.trim($dt['bandwidth']).' '.trim($dt['volume'])."</option>";
}

ClosedDBConnection($cn);

echo $options;
