<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 11/1/2016
 * Time: 3:33 PM
 */
require_once "../lib/common.php";
date_default_timezone_set("Asia/Dhaka");
$cn = connectDB();

/*
Channel:
Abul Hasan Jewel => 100
Musfiq-Ur- Rahaman => 98
S.M. Mazidul Islam =>102
Rajib Chakraborty=>
Doze Comilla Comilla=>121
RomotiQ:
Doze Admin=>52
Others(agents) Telesales:

*/

//$months = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
$current_year = date('Y');
$current_month = date('M');

$first_day_current_month = date("Y-m-01 00:00:00",strtotime($current_month.",".$current_year));
$current_month_current_day_start = date("Y-m-t 00:00:00",strtotime($current_month.",".$current_year));
$current_month_current_day = date("Y-m-t H:i:s",strtotime($current_month.",".$current_year));
$customer_type = "customer";


$channel_users = array();
$telesales_users = array();
$romoti_users = array();
$channel_user_qry = "SELECT user_id FROM user_info WHERE user_role = 'Channel' AND user_status = 0";
$rs = Sql_exec($cn,$channel_user_qry);
while($dt = mysql_fetch_assoc($rs)){
    $channel_users[]= $dt['user_id'];
}

$telesales_user_qry = "SELECT user_id FROM user_info WHERE user_role = 'Sales Representative' OR user_role='Retail' AND user_status = 0";
$rs = Sql_exec($cn,$telesales_user_qry);
while($dt = mysql_fetch_assoc($rs)){
    $telesales_users[] = $dt['user_id'];
}

$romotiq_qry = "SELECT user_id FROM user_info WHERE user_role = 'Admin' AND user_status = 0";
$rs = Sql_exec($cn,$romotiq_qry);
while( $dt = mysql_fetch_assoc($rs) ){
    $romoti_users[] = $dt['user_id'];
}

$channel_user_str = implode( ",",$channel_users );
$qry_channel = "SELECT zone,COUNT(*) AS number FROM contacts
            WHERE  customer_type='$customer_type' AND assign_to IN(".$channel_user_str.") AND
                   update_date >= STR_TO_DATE('$first_day_current_month','%Y-%m-%d %H:%i:%s') AND update_date <= STR_TO_DATE('$current_month_current_day','%Y-%m-%d %H:%i:%s') AND stage_id = 7
            GROUP BY `zone`;";

$rs = Sql_exec($cn,$qry_channel);

$connections = array();

while($row = Sql_fetch_array($rs)) {

    if( trim($row['zone']) == "1" || $row['zone'] == 1 ){
        if( empty($connections['Channel']['Dhaka']) ){
            $connections['Channel']['Dhaka'] = 1;
        } else{
            $connections['Channel']['Dhaka']= $connections['Channel']['Dhaka']+1;
        }
    }

    if( trim($row['zone']) == "2" || $row['zone'] == 2){
        if( empty($connections['Channel']['Comilla']) ){
            $connections['Channel']['Comilla'] = 1;
        } else{
            $connections['Channel']['Comilla']= $connections['Channel']['Comilla']+1;
        }
    }

    if( trim($row['zone']) == "3" || $row['zone'] == 3 ){
        if( empty($connections['Channel']['Chittagong']) ){
            $connections['Channel']['Chittagong'] = 1;
        } else{
            $connections['Channel']['Chittagong']= $connections['Channel']['Chittagong']+1;
        }
    }

}


$telesales_user_str = implode( ",",$telesales_users );
$qry_telesales = "SELECT zone,COUNT(*) AS number FROM contacts
                  WHERE  customer_type='$customer_type' AND assign_to IN(".$telesales_user_str.") AND
                   update_date >= STR_TO_DATE('$first_day_current_month','%Y-%m-%d %H:%i:%s') AND update_date <= STR_TO_DATE('$current_month_current_day','%Y-%m-%d %H:%i:%s') AND stage_id = 7
            GROUP BY `zone`;";

$rs = Sql_exec($cn,$qry_telesales);
while($row = Sql_fetch_array($rs)) {
  //  print_r($row);
    if( trim($row['zone']) == "1" || $row['zone'] == 1){
        if( empty($connections['Telesales']['Dhaka']) ){
            $connections['Telesales']['Dhaka'] = 1;
        } else{
            $connections['Telesales']['Dhaka']= $connections['Telesales']['Dhaka']+1;
        }
    }

    if( trim($row['zone']) == "2" || $row['zone'] == 2 ){
        if( empty($connections['Telesales']['Comilla']) ){
            $connections['Telesales']['Comilla'] = 1;
        } else{
            $connections['Telesales']['Comilla']= $connections['Telesales']['Comilla']+1;
        }
    }

    if( trim($row['zone']) == "3" || $row['zone'] == 3 ){
        if( empty($connections['Telesales']['Chittagong']) ){
            $connections['Telesales']['Chittagong'] = 1;
        } else{
            $connections['Telesales']['Chittagong']= $connections['Telesales']['Chittagong']+1;
        }
    }

}

$romoti_user_str = implode(",",$romoti_users);
$qry_remotiq = "SELECT zone,COUNT(*) AS number FROM contacts
            WHERE  customer_type='$customer_type' AND assign_to IN(".$romoti_user_str.") AND
                   update_date >= STR_TO_DATE('$first_day_current_month','%Y-%m-%d %H:%i:%s') AND update_date <= STR_TO_DATE('$current_month_current_day','%Y-%m-%d %H:%i:%s') AND stage_id = 7
            GROUP BY `zone`;";

$rs = Sql_exec($cn,$qry_remotiq);
while($row = Sql_fetch_array($rs)) {

    if( trim($row['zone']) == "1" || $row['zone'] == 1 ){
        if( empty($connections['Remotiq']['Dhaka']) ){
            $connections['Remotiq']['Dhaka'] = 1;
        } else{
            $connections['Remotiq']['Dhaka']= $connections['Remotiq']['Dhaka']+1;
        }
    }

    if( trim($row['zone']) == "2" || $row['zone'] == 2 ){
        if( empty($connections['Remotiq']['Comilla']) ){
            $connections['Remotiq']['Comilla'] = 1;
        } else{
            $connections['Remotiq']['Comilla']= $connections['Remotiq']['Comilla']+1;
        }
    }

    if( trim($row['zone']) == "3" || $row['zone'] == 3 ){
        if( empty($connections['Remotiq']['Chittagong']) ){
            $connections['Remotiq']['Chittagong'] = 1;
        } else{
            $connections['Remotiq']['Chittagong']= $connections['Remotiq']['Chittagong']+1;
        }
    }

}


if( empty($connections['Remotiq']['Chittagong']) ){
    $connections['Remotiq']['Chittagong'] = 0;
}

if( empty($connections['Remotiq']['Comilla']) ){
    $connections['Remotiq']['Comilla'] = 0;
}

if( empty($connections['Remotiq']['Dhaka']) ){
    $connections['Remotiq']['Dhaka'] = 0;
}

if( empty($connections['Telesales']['Chittagong']) ){
    $connections['Telesales']['Chittagong'] = 0;
}

if( empty($connections['Telesales']['Comilla']) ){
    $connections['Telesales']['Comilla'] = 0;
}

if( empty($connections['Telesales']['Dhaka']) ){
    $connections['Telesales']['Dhaka'] = 0;
}

if( empty($connections['Channel']['Chittagong']) ){
    $connections['Channel']['Chittagong'] = 0;
}

if( empty($connections['Channel']['Comilla']) ){
    $connections['Channel']['Comilla'] = 0;
}

if( empty($connections['Channel']['Dhaka']) ){
    $connections['Channel']['Dhaka'] = 0;
}


$qry_channel = "SELECT zone,COUNT(*) AS number FROM contacts
            WHERE  customer_type='$customer_type' AND assign_to IN(".$channel_user_str.") AND
                   update_date >= STR_TO_DATE('$current_month_current_day_start','%Y-%m-%d %H:%i:%s') AND update_date <= STR_TO_DATE('$current_month_current_day','%Y-%m-%d %H:%i:%s') AND stage_id = 7
            GROUP BY `zone`;";
$rs = Sql_exec($cn,$qry_channel);

$connections_today = array();

while($row = Sql_fetch_array($rs)) {

    if( trim($row['zone']) == "1" || $row['zone'] == 1){
        if( empty($connections_today['Channel']['Dhaka']) ){
            $connections_today['Channel']['Dhaka'] = 1;
        } else{
            $connections_today['Channel']['Dhaka']= $connections_today['Channel']['Dhaka']+1;
        }
    }

    if( trim($row['zone']) == "2" || $row['zone'] == 2 ){
        if( empty($connections_today['Channel']['Comilla']) ){
            $connections_today['Channel']['Comilla'] = 1;
        } else{
            $connections_today['Channel']['Comilla']= $connections_today['Channel']['Comilla']+1;
        }
    }

    if( trim($row['zone']) == "3" || $row['zone'] == 3 ){
        if( empty($connections_today['Channel']['Chittagong']) ){
            $connections_today['Channel']['Chittagong'] = 1;
        } else{
            $connections_today['Channel']['Chittagong']= $connections_today['Channel']['Chittagong']+1;
        }
    }

}

$qry_telesales = "SELECT zone,COUNT(*) AS number FROM contacts
            WHERE  customer_type='$customer_type' AND assign_to IN(".$telesales_user_str.") AND
                   update_date >= STR_TO_DATE('$current_month_current_day_start','%Y-%m-%d %H:%i:%s') AND update_date <= STR_TO_DATE('$current_month_current_day','%Y-%m-%d %H:%i:%s') AND stage_id = 7
            GROUP BY `zone`;";

$rs = Sql_exec($cn,$qry_telesales);
while($row = Sql_fetch_array($rs)) {

    if( trim($row['zone']) == "1" || $row['zone'] == 1){
        if( empty($connections_today['Telesales']['Dhaka']) ){
            $connections_today['Telesales']['Dhaka'] = 1;
        } else{
            $connections_today['Telesales']['Dhaka']= $connections_today['Telesales']['Dhaka']+1;
        }
    }

    if( trim($row['zone']) == "2" || $row['zone'] == 2 ){
        if( empty($connections_today['Telesales']['Comilla']) ){
            $connections_today['Telesales']['Comilla'] = 1;
        } else{
            $connections_today['Telesales']['Comilla']= $connections_today['Telesales']['Comilla']+1;
        }
    }

    if( trim($row['zone']) == "3" || $row['zone'] == 3 ){
        if( empty($connections_today['Telesales']['Chittagong']) ){
            $connections_today['Telesales']['Chittagong'] = 1;
        } else{
            $connections_today['Telesales']['Chittagong']= $connections_today['Telesales']['Chittagong']+1;
        }
    }

}


$qry_remotiq = "SELECT zone,COUNT(*) AS number FROM contacts
            WHERE  customer_type='$customer_type' AND assign_to IN(".$romoti_user_str.") AND
                   update_date >= STR_TO_DATE('$current_month_current_day_start','%Y-%m-%d %H:%i:%s') AND update_date <= STR_TO_DATE('$current_month_current_day','%Y-%m-%d %H:%i:%s') AND stage_id = 7
            GROUP BY `zone`;";

$rs = Sql_exec($cn,$qry_remotiq);
while($row = Sql_fetch_array($rs)) {

    if( trim($row['zone']) == "1" || $row['zone'] == 1 ){
        if( empty($connections_today['Remotiq']['Dhaka']) ){
            $connections_today['Remotiq']['Dhaka'] = 1;
        } else{
            $connections_today['Remotiq']['Dhaka']= $connections_today['Remotiq']['Dhaka']+1;
        }
    }

    if( trim($row['zone']) == "2" || $row['zone'] == 2 ){
        if( empty($connections_today['Remotiq']['Comilla']) ){
            $connections_today['Remotiq']['Comilla'] = 1;
        } else{
            $connections_today['Remotiq']['Comilla']= $connections_today['Remotiq']['Comilla']+1;
        }
    }

    if( trim($row['zone']) == "3" || $row['zone'] == 3 ){
        if( empty($connections_today['Remotiq']['Chittagong']) ){
            $connections_today['Remotiq']['Chittagong'] = 1;
        } else{
            $connections_today['Remotiq']['Chittagong']= $connections_today['Remotiq']['Chittagong']+1;
        }
    }

}

if( empty($connections_today['Remotiq']['Chittagong']) ){
    $connections_today['Remotiq']['Chittagong'] = 0;
}

if( empty($connections_today['Remotiq']['Comilla']) ){
    $connections_today['Remotiq']['Comilla'] = 0;
}

if( empty($connections_today['Remotiq']['Dhaka']) ){
    $connections_today['Remotiq']['Dhaka'] = 0;
}

if( empty($connections_today['Telesales']['Chittagong']) ){
    $connections_today['Telesales']['Chittagong'] = 0;
}

if( empty($connections_today['Telesales']['Comilla']) ){
    $connections_today['Telesales']['Comilla'] = 0;
}

if( empty($connections_today['Telesales']['Dhaka']) ){
    $connections_today['Telesales']['Dhaka'] = 0;
}

if( empty($connections_today['Channel']['Chittagong']) ){
    $connections_today['Channel']['Chittagong'] = 0;
}

if( empty($connections_today['Channel']['Comilla']) ){
    $connections_today['Channel']['Comilla'] = 0;
}

if( empty($connections_today['Channel']['Dhaka']) ){
    $connections_today['Channel']['Dhaka'] = 0;
}


$data = array();
$i = 0;
$j = 0;
$data[$i][$j++] = "Dhaka";
$data[$i][$j++] = $connections_today['Telesales']['Dhaka'];
$data[$i][$j++] = $connections_today['Channel']['Dhaka'];
$data[$i][$j++] = $connections_today['Remotiq']['Dhaka'];
$data[$i][$j++] = $connections['Telesales']['Dhaka'];
$data[$i][$j++] = $connections['Channel']['Dhaka'];
$data[$i][$j++] = $connections['Remotiq']['Dhaka'];
$data[$i][$j++] = $connections['Telesales']['Dhaka']+$connections['Channel']['Dhaka']+$connections['Remotiq']['Dhaka'];
$i++;
$j=0;
$data[$i][$j++] = "Comilla";
$data[$i][$j++] = $connections_today['Telesales']['Comilla'];
$data[$i][$j++] = $connections_today['Channel']['Comilla'];
$data[$i][$j++] = $connections_today['Remotiq']['Comilla'];
$data[$i][$j++] = $connections['Telesales']['Comilla'];
$data[$i][$j++] = $connections['Channel']['Comilla'];
$data[$i][$j++] = $connections['Remotiq']['Comilla'];
$data[$i][$j++] = $connections['Telesales']['Comilla']+$connections['Channel']['Comilla']+$connections['Remotiq']['Comilla'];
$i++;
$j=0;
$data[$i][$j++] = "Chittagong";
$data[$i][$j++] = $connections_today['Telesales']['Chittagong'];
$data[$i][$j++] = $connections_today['Channel']['Chittagong'];
$data[$i][$j++] = $connections_today['Remotiq']['Chittagong'];
$data[$i][$j++] = $connections['Telesales']['Chittagong'];
$data[$i][$j++] = $connections['Channel']['Chittagong'];
$data[$i][$j++] = $connections['Remotiq']['Chittagong'];
$data[$i][$j++] =  $connections['Telesales']['Chittagong'] + $connections['Channel']['Chittagong'] + $connections['Remotiq']['Chittagong'];

$i++;
$j=0;

$data[$i][$j++] = "Total";
$data[$i][$j++] = "";
$data[$i][$j++] = "";
$data[$i][$j++] = "";
$data[$i][$j++] =($total_telesales = $connections['Telesales']['Dhaka']+$connections['Telesales']['Comilla']+$connections['Telesales']['Chittagong']);
$data[$i][$j++] =($toal_channel = $connections['Channel']['Dhaka']+$connections['Channel']['Comilla']+$connections['Channel']['Chittagong']);
$data[$i][$j++] =($toal_remotiq = $connections['Remotiq']['Dhaka']+$connections['Remotiq']['Comilla']+$connections['Remotiq']['Chittagong']);
$data[$i][$j++] = ($total_telesales+$toal_channel+$toal_remotiq);

echo json_encode($data);

ClosedDBConnection($cn);