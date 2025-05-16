<?php
session_start();
if (isset($_SESSION['tipoUsuario'])) {
    $tipoUsuario = $_SESSION['tipoUsuario'];
    echo "<p>Você está logado como $tipoUsuario.</p>";
}
?>