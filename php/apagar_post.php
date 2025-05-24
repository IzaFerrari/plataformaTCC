<?php
session_start();
include_once('conexao.php');
include_once('protecao.php');

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../php/index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idTCC'])) {
    $idTCC = $_POST['idTCC'];
    $idUsuario = $_SESSION['idUsuario'];

    $sql = "SELECT tcc.idAutor FROM tcc WHERE tcc.idTCC = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idTCC);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $idAutor = $row['idAutor'];

        $sql_autor = "SELECT idEstudante FROM estudante WHERE idUsuario = ?";
        $stmt_autor = $conn->prepare($sql_autor);
        $stmt_autor->bind_param("i", $idUsuario);
        $stmt_autor->execute();
        $result_autor = $stmt_autor->get_result();
        
        if ($result_autor && $result_autor->num_rows > 0) {
            $autor = $result_autor->fetch_assoc();
            $idEstudanteLogado = $autor['idEstudante'];

            if ($idAutor == $idEstudanteLogado) {
                $sql_delete = "DELETE FROM tcc WHERE idTCC = ?";
                $stmt_delete = $conn->prepare($sql_delete);
                $stmt_delete->bind_param("i", $idTCC);
                $stmt_delete->execute();
                
                header("Location: ../php/inicio.php?status=deleted");
                exit();
            }
        }
    }
}

header("Location: ../php/inicio.php?error=unauthorized");
exit();
?>
