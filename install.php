<?php

mysqli_report(MYSQLI_REPORT_ALL);

require_once("connection.php");
ini_set('display_errors', 1);


$CreaTabellaNewsLetter = "CREATE TABLE IF NOT EXISTS abbonato(
    nome varchar(100) not null,
    email varchar(100) primary key
);";


if($resultQ = mysqli_query($mysqliConnection, $CreaTabellaNewsLetter)){
//ok
}
else{
printf("Impossibile creare la tabella abbonati\n");
exit();
}
?>
























