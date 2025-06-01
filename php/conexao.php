<?php
$host = "localhost:3306"; 
$usuario = "root"; 
$senha = ""; 
$banco = "tcc_connect"; 

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
