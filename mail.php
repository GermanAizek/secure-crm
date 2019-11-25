<?php
session_start();
include("client_config.php");
require("include/PHPMailer/src/PHPMailer.php");
require("include/PHPMailer/src/SMTP.php");
require("include/PHPMailer/src/Exception.php");

$REPLY_MESSAGE = 'reply@server.ru';
$SUBJECT_MESSAGE = 'Электронный документооборот "Колхоз"';

function sendMail($from, $to, $textHTML)
{
	try {
		$mail = new PHPMailer\PHPMailer\PHPMailer();
		$mail->isSMTP();
		$mail->Host = 'mailserver';
		$mail->Port = 465;
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPAuth = true;
		$mail->Username = 'mail@server.ru';
		$mail->Password = 'password';
		$mail->setFrom($from);
		$mail->addReplyTo($REPLY_MESSAGE);
		$mail->addAddress($to);
		$mail->Subject = $SUBJECT_MESSAGE;
		//$mail->msgHTML(file_get_contents('contents.html'), __DIR__);
		$mail->msgHTML($textHTML);
		//$mail->addAttachment('images/phpmailer_mini.png');

		if ($mail->send()) {
			print json_encode('Message sent!');
		} else {
		    print json_encode('Mailer Error: ' . $mail->ErrorInfo);
		}
	}
	catch(PDOException $e) {
	    print json_encode($e);
	}
}
