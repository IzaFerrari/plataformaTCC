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

if (!$idFeedback) {
    die("Erro: Feedback não especificado.");
}

// Confirma que o feedback pertence ao usuário logado
$sql = "SELECT f.idFeedback, u.idUsuario FROM feedback f
        LEFT JOIN mentor m ON f.idMentor = m.idMentor
        LEFT JOIN patrocinador p ON f.idPatrocinador = p.idPatrocinador
        LEFT JOIN usuario u ON u.idUsuario = COALESCE(m.idUsuario, p.idUsuario)
        WHERE f.idFeedback = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idFeedback);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    die("Erro: Feedback não encontrado.");
}

$row = $result->fetch_assoc();

if ($row['idUsuario'] != $_SESSION['idUsuario']) {
    die("Erro: Você não tem permissão para excluir este feedback.");
}

// Exclui
$sql_del = "DELETE FROM feedback WHERE idFeedback = ?";
$stmt_del = $conn->prepare($sql_del);
$stmt_del->bind_param("i", $idFeedback);
$stmt_del->execute();

header("Location: exibir_posts.php?msg=Feedback excluído com sucesso");
exit();
