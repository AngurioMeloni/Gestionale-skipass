<?php
// filepath: /c:/xampp/htdocs/5IE-TEP/Gestionale-skipass/config.php

// Parametri di connessione al database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "skipassmanagement";

// Creazione della connessione
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica della connessione
if ($conn->connect_error) {
    die("Connessione al database fallita: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>