<?php

class enviaCorreo{
	
	static public function enviale($contenido,$objetivo,$asunto){
		require 'libs/phpMailer/PHPMailerAutoload.php';
		
		require 'DataCorreo.php';
	
		$mail = new PHPMailer;
		
		//$mail->SMTPDebug = 3;                               // Enable verbose debug output
		
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'mail.blackoutsystems.com';  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = $sender;                 // SMTP username
		$mail->Password = $sender_pass;                           // SMTP password
		$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 587;                                    // TCP port to connect to
		
		$mail->From = $sender;
		$mail->FromName = $sender_name;
		//$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
		$mail->addAddress($objetivo);               // Name is optional
		//$mail->addReplyTo('israelg@blackoutsystems.com', 'Israel Pekas Garcia');
		//$mail->addCC('israelg@blackoutsystems.com');
		//$mail->addBCC('israel_gahe@hotmail.com');
		
		//$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
		//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
		//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
		$mail->isHTML(true);                                  // Set email format to HTML
		
		$mail->Subject = $asunto;
		$mail->Body    = $contenido;
		$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
		
		if(!$mail->send()) {
		    echo 'Error: Message could not be sent.';
		    echo 'Mailer Error: ' . $mail->ErrorInfo;
			die();
		}
	}
}
?>