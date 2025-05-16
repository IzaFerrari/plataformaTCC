<?php
if (!isset($_SESSION)) {
    session_start();
}

function protect() {
    if (!isset($_SESSION['idUsuario'])) {
        header("Location: index.php");
        exit(); 
    }
}
