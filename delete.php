<?php

include("db.class.php");

$id = $_POST['id'];

try {
	
	$sql = 'DELETE FROM empresa WHERE id='.$id;
	$sth = $conn->exec($sql);
	echo 'La empresa fue descartada del sistema exitosamente';
	
} catch(Exception $e) {
	header('HTTP/1.1 500 Internal Server Error');
}  