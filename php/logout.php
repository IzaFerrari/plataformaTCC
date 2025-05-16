<?php
session_start();
session_destroy(); // Encerra a sessão
header("Location: ../php/index.php"); // Redireciona para a página inicial
exit();
?>