<?php

require_once 'Swift-5.0.3/lib/swift_required.php';

if(isset($_POST['mails']) && isset($_POST['AttachedFiles'])) {
	
	$f = $_POST['AttachedFiles'];
	$empresa = utf8_decode($_POST['empresa']);
	$recipientes = $_POST['mails'];
	
	$files = explode(";", $f);
	
	if(count($recipientes) >= 1 && count($files) >= 1) {
		
	    /*
		require("phpmailer/class.phpmailer.php");
		
		$mail = new PHPmailer();
		$mail->SetLanguage('en','phpmailer/language');
		$mail->IsSMTP();
		$mail->SMTPAuth=true;
		$mail->Host='smtp.gmail.com';
		$mail->SMTPSecure='ssl';
		$mail->Username='elteniente.computershop@gmail.com';
		$mail->Password='jotejote';
		$mail->Port='465';
		$mail->SetFrom('elteniente.computershop@gmail.com', 'COMPUTERSHOP');
		
		foreach ($recipientes as $recipiente)
			if($recipiente != '')
				$mail->AddAddress($recipiente);
	
		foreach ($files as $file) {
			$file = rawurldecode($file);
			if($file != '' && file_exists($file)) {
				$mail->AddAttachment($file);
			}
		}

		$mail->Subject = $empresa;
		$mail->Body='-- 
		Adjunto saldos y reporte.-

		NOTA: Los saldos pueden tener un desfase de 48 hrs. aprox. con respecto a la última transacción.
		Les recordamos hacer sus compras de  servicios para su empresa con anticipación.

		Atte. Computershop.

		Dudas, consultas y reclamos a los contactos:
		Lucero-Gloria@aramark.cl
		elteniente@computershop.cl'; 
		
		if(!$mail->Send()){ echo $mail->ErrorInfo; }
	
		unset($mail);
		*/
	    	    
	    // Create the SMTP configuration
	    $transport = Swift_SmtpTransport::newInstance("smtp.gmail.com", 465, 'ssl');
	    $transport->setUsername("elteniente.computershop@gmail.com");
	    $transport->setPassword("jotejote");
	    
	    // Create the message
	    $message = Swift_Message::newInstance();	    
	    $message->setTo(array_filter($recipientes,'strlen'));
	    $message->setSubject($empresa);
	    $message->setFrom('elteniente.computershop@gmail.com', 'COMPUTERSHOP');
	    $message->setBody('-- 
		Adjunto saldos y reporte.-

		NOTA: Los saldos pueden tener un desfase de 48 hrs. aprox. con respecto a la última transacción.
		Les recordamos hacer sus compras de  servicios para su empresa con anticipación.

		Atte. Computershop.

		Dudas, consultas y reclamos a los contactos:
		Lucero-Gloria@aramark.cl
		elteniente@computershop.cl');
	    
	    
	    foreach ($files as $file) {
	        $file = rawurldecode($file);
	        if($file != '' && file_exists($file)) {
	             $message->attach(Swift_Attachment::fromPath($file));
	        }
	    }
	    
	    // Send the email
	    $mailer = Swift_Mailer::newInstance($transport);
	    $mailer->send($message, $failedRecipients);
	    
	    // Show failed recipients
	    echo empty($failedRecipients) ? '' : print_r($failedRecipients);
	    
	}
}
?>
