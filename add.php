<?php

include("db.class.php");

$empresa = $_POST['empresa'];
// $mails = $_POST['mails'];

try {
	
	$sql = "INSERT INTO empresa (nombre) VALUES ('$empresa')";
	$sth = $conn->exec($sql);
	$id = $conn->lastInsertId();
    $data = array("id"=>$id);
    
	echo json_encode($data);
	
} catch(Exception $e) {
	
	echo 'Erreur : '.$e->getMessage().'';
	echo 'N� : '.$e->getCode();
	
}