<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 8/12/2016
 * Time: 6:01 PM
 */

require_once "../lib/common.php";
$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

$cn = connectDB();
$customer_id = mysql_real_escape_string(trim($datainfo['customer_id']));

$qry = "SELECT
CONCAT( contact.first_name,contact.last_name ) AS customer_name,
contact.email AS email,
CONCAT_WS(\", \", NULLIF(contact.phone1, \"\"), NULLIF(contact.phone2, \"\")) AS phone,
contact.address1 AS address1,
contact.address2 AS address2,
convertion.install_cost AS install_cost,
convertion.monthly_cost AS monthly_cost,
convertion.month_number AS month_number,
convertion.package AS package,
convertion.collection_date AS collection_date,
convertion.transaction_id AS transaction_id
FROM contacts AS contact INNER JOIN customer_conversion AS convertion ON  contact.id = convertion.contact_id
WHERE contact_id ='".$customer_id."';";

$rs = Sql_exec($cn,$qry);
$row = Sql_fetch_array($rs);

$table_html = "";
if( count($row)>0 ){

    $table_html = "<table class=\"table table-bordered\"><tbody>";
    $table_html.="<tr><td>"."Customer Name:"."</td>"."<td>".$row["customer_name"]."</td></tr>";
    $table_html.="<tr><td>"."Email:"."</td>"."<td>".$row["email"]."</td></tr>";
    $table_html.="<tr><td>"."Contact No:"."</td>"."<td>".$row["phone"]."</td></tr>";
    $table_html.="<tr><td>"."Primary Adress:"."</td>"."<td>".$row["address1"]."</td></tr>";
    $table_html.="<tr><td>"."Secondary Address:"."</td>"."<td>".$row["address2"]."</td></tr>";
    $table_html.="<tr><td>"."Installation Cost:"."</td>"."<td>".$row["install_cost"]."</td></tr>";
    $table_html.="<tr><td>"."Monthly Cost:"."</td>"."<td>".$row["monthly_cost"]."</td></tr>";
    $table_html.="<tr><td>"."Month Number:"."</td>"."<td>".$row["month_number"]."</td></tr>";
    $table_html.="<tr><td>"."Package:"."</td>"."<td>".$row["package"]."</td></tr>";
    $table_html.="<tr><td>"."Collection Date:"."</td>"."<td>".$row["collection_date"]."</td></tr>";
    $table_html.="<tr><td>"."Transaction ID:"."</td>"."<td>".$row["transaction_id"]."</td></tr>";
    $table_html.="</tbody></table>";
}

echo $table_html;

