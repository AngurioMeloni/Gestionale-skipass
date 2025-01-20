<?php
// filepath: /c:/xampp/htdocs/5IE-TEP/Gestionale-skipass/logout.php

session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: login.php');
exit;
?>