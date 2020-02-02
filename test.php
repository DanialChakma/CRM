<?php 
$url = "http://103.218.27.138/radiusservices/user_details.php?manager=crmwebservice&managerpass=crm_doze&expiration=2017-07-01%2000:00:00";

$response = file_get_contents($url);
$lines = explode("\r\n",$response);
echo "<pre>";
//print_r($lines);
echo "</pre>";

foreach($lines as $key=>$value){

    $val_str = trim($value);
	$attributes = explode("|",$val_str);
	if( trim($attributes[0]) == "SUCCESS" ){
	
	}else if( trim($attributes[0]) == "FAILED" ){
	
	}else{
	     if( $key > 0 ){
			 $lines[$key-1] = $lines[$key-1]."\n".$lines[$key];
			 unset($lines[$key]);
		 }
	} 
}

echo "<pre>";
print_r($lines);
echo "</pre>";

foreach($lines as $key=>$value){
//$values = explode("|",$value);
echo "<pre>";
//print_r($value);
echo "</pre>";
}

//echo $response;

?>