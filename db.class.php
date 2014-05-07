<?php

try {

        /*************************************************************
        /******************** SQL SERVER *****************************
        /************************************************************/
        //$conn = new PDO("sqlsrv:server=127.0.0.1;Database=MassMail", "sa", "01Cshop47");
        //$conn->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_SYSTEM);

        /*************************************************************
        /******************** MYSQL **********************************
        /************************************************************/
        $conn = new PDO("mysql:host=127.0.0.1;dbname=massmail","massmail","euWQrBdqvXLyNFD2");

        $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        
}
catch(Exception $e) {
        die( print_r( $e->getMessage() ) );
}

?>

