<?php

if(isset($_POST['mails']) && isset($_POST['AttachedFiles'])) {
	
	$f = $_POST['AttachedFiles'];
	$empresa = utf8_decode($_POST['empresa']);
	$recipientes = $_POST['mails'];
	
	$files = explode(";", $f);
	
	if(count($recipientes) >= 1 && count($files) >= 1) {
		
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
	}
}
?>
