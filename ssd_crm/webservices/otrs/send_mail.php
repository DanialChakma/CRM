<?php
/**
 * Created by PhpStorm.
 * User: Talemul
 * Date: 6/17/2015
 * Time: 1:15 PM
 */

require_once '../lib_mail/swift_required.php';


/**
 * @param array $to_send ex- array("to_test@yahoo.com" => "Test Name");
 * @param string $subject
 * @param string $message_body
 * @param array $cc_send ex-array("cc_test@yahoo.com" => "Test Name");
 * @param string $attachment . file name
 * @param array $bcc_send ex-array("bcc_test@yahoo.com" => "Test Name");
 * @return mixed it returns number of recipient . return zero or negative number means false.
 */
function send_mail($to_send = array(), $subject = '', $message_body = '',$cc_send = array(),  $attachment = '', $bcc_send = array())
{

    $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl');
    $transport->setUsername("dozeinternet24@gmail.com");
    $transport->setPassword("doze1234");

// Create the message
    $message = Swift_Message::newInstance();
    $message->setTo($to_send);
    $message->setCc($cc_send);
    $message->setBcc($bcc_send);
    $message->setSubject($subject, 'text/html');
    $message->setBody($message_body, 'text/html');
    $message->setFrom("dozeinternet24@gmail.com", "Doze Internet");
    if ($attachment != '') {
        $message->attach(Swift_Attachment::fromPath($attachment));
    }

// Send the email
    $mailer = Swift_Mailer::newInstance($transport);
    $test = $mailer->send($message, $failedRecipients);

    return $test;
}

//
//$to_send = array(
//    "talemul@ssd-tech.com" => "Talemul Islam"
//);
//$cc_send = array("talemul@yahoo.com" => "Talemul Yahoo");
//$bcc_send = array("mazhar@ssd-tech.com" => "Mazhaar");
//$subject = 'Final test';
//$message_body = 'This is test mail';
//
//$ttt = send_mail($to_send,  $subject, $message_body,$cc_send, $attachment, $bcc_send);
//
