<?php
session_start();
include_once('conexao.php');

if (!isset($_SESSION['idUsuario'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idUsuario = $_SESSION['idUsuario'];
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $linkProjeto = isset($_POST['linkProjeto']) ? $_POST['linkProjeto'] : null;

    // Buscar o tipo do usuário (Aluno ou Ex-aluno)
    $tipoUsuario = null;
    $sqlTipo = "SELECT tipoUsuario FROM usuario WHERE idUsuario = ?";
    $stmtTipo = $conn->prepare($sqlTipo);
    $stmtTipo->bind_param("i", $idUsuario);
    $stmtTipo->execute();
    $resultTipo = $stmtTipo->get_result();
    if ($rowTipo = $resultTipo->fetch_assoc()) {
        $tipoUsuario = $rowTipo['tipoUsuario'];
    }

    // Buscar o idEstudante correspondente ao idUsuario
    $sqlAutor = "SELECT idEstudante FROM estudante WHERE idUsuario = ?";
    $stmtAutor = $conn->prepare($sqlAutor);
    $stmtAutor->bind_param("i", $idUsuario);
    $stmtAutor->execute();
    $resultAutor = $stmtAutor->get_result();

    if ($rowAutor = $resultAutor->fetch_assoc()) {
        $idAutor = $rowAutor['idEstudante'];

        // Definir status com base no tipo de usuário
        $status = ($tipoUsuario === 'Ex-aluno') ? 'Concluído' : 'Em desenvolvimento';

        // Inserir o post
        $sql = "INSERT INTO tcc (idAutor, titulo, descricao, link, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $idAutor, $titulo, $descricao, $linkProjeto, $status);

        if ($stmt->execute()) {
            header("Location: ../html/inicio.php?msg=Post publicado com sucesso!");
        } else {
            echo "Erro ao publicar post.";
        }
    } else {
        echo "Autor não encontrado.";
    }
}
?>
