<?php
session_start();
include_once('conexao.php');

if (!isset($_SESSION['idUsuario'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idTCC = $_POST['idTCC'];
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $linkProjeto = isset($_POST['linkProjeto']) ? $_POST['linkProjeto'] : null;

    $sql = "UPDATE tcc SET titulo = ?, descricao = ?, link = ? WHERE idTCC = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $titulo, $descricao, $linkProjeto, $idTCC);

    if ($stmt->execute()) {
        header("Location: ../html/inicio.php?msg=Post atualizado com sucesso!");
    } else {
        echo "Erro ao atualizar post.";
    }
}
?>
