<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once('conexao.php');
include_once('protecao.php');

if (!isset($_SESSION['idUsuario'])) {
    die("Erro: Usuário não autenticado.");
}

$idFeedback = $_POST['idFeedback'] ?? null;
$titulo = $_POST['titulo'] ?? null;
$texto = $_POST['texto'] ?? null;

if (!$idFeedback || !$titulo || !$texto) {
    die("Erro: Todos os campos são obrigatórios.");
}

// Verifica se o feedback pertence ao usuário
$sql_check = "SELECT f.idFeedback, u.idUsuario FROM feedback f
              LEFT JOIN mentor m ON f.idMentor = m.idMentor
              LEFT JOIN patrocinador p ON f.idPatrocinador = p.idPatrocinador
              LEFT JOIN usuario u ON u.idUsuario = COALESCE(m.idUsuario, p.idUsuario)
              WHERE f.idFeedback = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("i", $idFeedback);
$stmt->execute();
$res = $stmt->get_result();

if (!$res || $res->num_rows === 0) {
    die("Erro: Feedback não encontrado.");
}

$row = $res->fetch_assoc();
if ($row['idUsuario'] != $_SESSION['idUsuario']) {
    die("Erro: Você não tem permissão para editar este feedback.");
}

// Atualiza
$sql_up = "UPDATE feedback SET titulo = ?, texto = ?, data = CURRENT_TIMESTAMP WHERE idFeedback = ?";
$stmt_up = $conn->prepare($sql_up);
$stmt_up->bind_param("ssi", $titulo, $texto, $idFeedback);
$stmt_up->execute();

header("Location: inicio.php?msg=Feedback atualizado com sucesso");
exit();
