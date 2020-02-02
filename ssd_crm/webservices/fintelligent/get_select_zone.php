<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 8/11/2016
 * Time: 7:16 PM
 */

require_once "../lib/common.php";

$cn = connectDB();

$qry = "SELECT `value`,`zone_name` FROM select_zone";

$rs = Sql_exec($cn,$qry);
$options_string = "";
while($row = mysql_fetch_assoc($rs)){
    $options_string .= '<option value="'.$row['value'].'" >'.trim($row['zone_name']) .'</option>';
}
ClosedDBConnection($cn);
echo $options_string;