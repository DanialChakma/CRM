<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 11/23/2016
 * Time: 1:10 PM
 */
require_once "../lib/common.php";

$input_data = isset($_REQUEST['action']) ? $_REQUEST : exit;
$action = $input_data['action'];

if ($action == 'GETLIST') {
    $cn = connectDB();
//    $cn = mysql_connect('103.239.252.134', 'root', 'nopass');
//    mysql_select_db('doze_ssd_crm');

    if ($input_data['customer_email'] == '') {
        echo "<table class='gridtable'>
            <tr>
            <th>Sl No</th>
            <th>Customer Feedback</th>
            <th>Call Date</th>
            <th>Followup Date</th>
            <th>Call Status</th>
            <th>Call Outcome</th>
            </tr>
          </table>";
    } else {

//        $select_qry = "SELECT *
//FROM monthly_bill_call_list_history
//WHERE contact_id = (SELECT id
//                    FROM contacts
//                    WHERE email = '" . $input_data['customer_email'] . "' AND customer_type = 'customer')
//ORDER BY call_date DESC
//LIMIT 10";

        $select_qry = "SELECT * FROM monthly_bill_call_list_history WHERE mbcl_id = (SELECT MAX(id) FROM monthly_bill_call_list WHERE email = '" . $input_data['customer_email'] . "')";


//        $select_qry = "SELECT *
//FROM monthly_bill_call_list_history
//ORDER BY call_date DESC
//LIMIT 10";

        $result = Sql_exec($cn, $select_qry);

        $table_body = "";
        $serial = 1;
        $mbclid = '';

        while ($dt = Sql_fetch_array($result)) {
            $mbclid = $dt['mbcl_id'];
            $table_body .= "<tr>";
            $table_body .= "<td>" . $serial . "</td>";
            $table_body .= "<td>" . $dt['feedback'] . "</td>";
            $table_body .= "<td>" . $dt['call_date'] . "</td>";
            $table_body .= "<td>" . $dt['next_follow_up_date'] . "</td>";
            $table_body .= "<td>" . $dt['call_status'] . "</td>";
            $table_body .= "<td>" . getCallOutcome($dt['call_outcome']) . "</td>";
            $table_body .= "</tr>";
            $serial++;
        }

        ClosedDBConnection($cn);

        $full_table = "<table class='gridtable'>
<tr>
<td colspan='5'>Customer Call History</td>
<td><button onclick=\"generateTicket('" . $input_data['customer_email'] . "', '$mbclid')\">Generate Ticket</button></td>
</tr>
<tr>
<th>Sl No</th>
<th>Customer Feedback</th>
<th>Call Date</th>
<th>Followup Date</th>
<th>Call Status</th>
<th>Call Outcome</th>
</tr>
$table_body
</table>
<br/>
<script>
    function generateTicket(email, id){
//        if(id == ''){
//            alert('Billing information is not found. Please generate ticket manually !!');
//            return false;
//        }
        var person = prompt('Description for: ' + email, '');
        if (person != null && person != '') {
            window.location.replace('http://103.239.252.134/dozetest/ssd_crm/webservices/report/get_monthly_bill_call_history_support.php?action=GENERATETICKET&ticket_description='+person+'&table_id='+id+'&generate_ticket='+email);
        }
    }
</script>
";

        if ($table_body == "") {
            $full_table .= "No record found for: " . $input_data['customer_email'];
        }

        echo $full_table;
    }

} else if ($action == 'GENERATETICKET') {
    echo 'Customer Email : ' . $input_data['generate_ticket'];
    //echo "<br/>";
    //echo $input_data['table_id'];
    echo "<br/>";
    echo 'Ticket Description : ' . $input_data['ticket_description'];

    # param for ticket creation.........
    $title = urlencode('Billing Issue');
    $queue = urlencode('Billing Collection');
    $lock = urlencode('unlock');
    $priority = urlencode('5 very high');
    $state = urlencode('new');
    $type = urlencode('Problem');
    $owner = 13;
    $user = 13;

    # param for creating article.........
    $message_body = urlencode('[For customer:' . $input_data['generate_ticket'] . '] \n' . $input_data['ticket_description']);
    $articletype = urlencode('email-external');
    $sub = urlencode('Billing Collection');
    $sendertype = urlencode('agent');
    $from = urlencode('DOZE Support <support@monitor.dozeinternet.com>');
    $to = urlencode('Mustafa Zaman <mustafa@ssd-tech.com>');
    $cc = urlencode('Khondoker Nazibul Hossain <nazibul@dozeinternet.com>');
    $contenttype = urlencode('text/plain; charset=utf-8');
    $historytype = urlencode('EmailCustomer');
    $historycomment = urlencode('From DozeSupport Panel');
    $unlockonaway = 1;

    $otrsticketurl = "http://103.239.252.132/otrs/generateOTRSticket.pl?title=$title&queue=$queue&lock=$lock&priority=$priority&state=$state&type=$type&owner=$owner&user=$user&articletype=$articletype&articlesub=$sub&sendertype=$sendertype&from=$from&to=$to&cc=$cc&contenttype=$contenttype&historytype=$historytype&historycomment=$historycomment&unlockonaway=$unlockonaway&message=$message_body";

//        echo $otrsticketurl;
//        exit;

    $resultotrs = file_get_contents($otrsticketurl);
    //$resultotrs = '{"ticket_number":"98171534","article_id":"804213","ticket_id":"171595"}';
    //echo "<br/> otrs response :" . $resultotrs;
    $resultotrs = json_decode($resultotrs, true);
    //print_r($resultotrs);
    $ticketid = $resultotrs['ticket_id'];
    $ticketnumber = $resultotrs['ticket_number'];

    echo "<br/>";
    echo "Ticket ID : " . $ticketid;
    echo "<br/>";
    echo "Ticket Number : " . $ticketnumber;
    //$alertmsg = "Ticket ID:" . $resultotrs['ticket_id'] . " & Ticket number: " . $resultotrs['ticket_number'];
    //alert($alertmsg);
    //exit;
    $urltohit = '';
    if ($input_data['table_id'] == '' || empty($input_data['table_id'])) {

        $urltohit = "http://103.239.252.134/dozetest/ssd_crm/webservices/fintelligent/save_billing_info_support.php?email=" . $input_data['generate_ticket'] . "&feedback=" . urlencode("Support Subscriber [TicketNo:$ticketnumber] : " . $input_data['ticket_description']);

    } else {

        $urltohit = "http://103.239.252.134/dozetest/ssd_crm/webservices/fintelligent/create_collection_task_api.php?mbcl_id=" . $input_data['table_id'] . "&feedback=" . urlencode("Support Subscriber [TicketNo:$ticketnumber] : " . $input_data['ticket_description']);

    }

    //echo "<br/>" . $urltohit;

    $result = file_get_contents($urltohit);
    //$res = explode("|", $result);
    echo "<br/>";
    echo "\n API response : " . $result;

//    if (strtolower(trim($res[0])) == 'ok') {
//
//    } else {
//        echo "<br/>";
//        echo 'API response : ' . $result;
//        //alert($result);
//    }
    //ok|Operation Successful.

} else {
    echo "<br/>";
    echo "Something goes wrong !!!";
}

function getCallOutcome($id)
{
    if ($id == '1') {
        return 'Followup';
    } else if ($id == '2') {
        return 'Not Interested to pay';
    } else if ($id == '3') {
        return 'Pay Today';
    } else if ($id == '4') {
        return 'Pay Later';
    } else {
        return '';
    }
}