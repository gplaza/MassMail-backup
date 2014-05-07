<?php

include("db.class.php");

$empresa = utf8_decode($_POST['empresa']);
$id = utf8_decode($_POST['id']);
$mails = isset($_POST['mails'])? $_POST['mails'] : array();

try {

    $sql = 'UPDATE empresa SET nombre=\''.$empresa.'\' WHERE id='.$id;
    $sth = $conn->exec($sql);

    $sql = 'DELETE FROM empresa_mail WHERE id_empresa='.$id;
    $sth = $conn->exec($sql);


    foreach ($mails as $mail) {
         
        $sm = $conn->query("SELECT * from mail WHERE email='$mail'");
        $result = $sm->fetchAll(PDO::FETCH_ASSOC);
         
        if(count($result) == 1)
        {
            $sql = 'INSERT INTO empresa_mail (id_empresa,id_mail) VALUES ('.$id.','.$result[0]['id'].')';
            $sth = $conn->exec($sql);

        } else {
             
            $sql = "INSERT INTO mail (email) VALUES ('$mail')";
            $sth = $conn->exec($sql);
             
            $idMail = $conn->lastInsertId();
             
            $sql = "INSERT INTO empresa_mail (id_empresa,id_mail) VALUES ($id,$idMail)";
            $sth = $conn->exec($sql);
        }
    }

    echo "La empresa fue agregada al sistema exitosamente";

} catch(Exception $e) {

    echo 'Error : '.$e->getMessage().'';
    echo 'N� : '.$e->getCode();
}

?>