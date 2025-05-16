<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once('conexao.php');
include_once('protecao.php');
include('../php/menu.php');

if (!isset($_SESSION['idUsuario'])) {
    die("Erro: Usuário não autenticado.");
}

$idFeedback = $_GET['id'] ?? null;

if (!$idFeedback) {
    die("Erro: Feedback não especificado.");
}

// Busca o feedback
$sql = "SELECT f.*, u.idUsuario FROM feedback f
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

$feedback = $result->fetch_assoc();

if ($feedback['idUsuario'] != $_SESSION['idUsuario']) {
    die("Erro: Você não tem permissão para editar este feedback.");
}
?>

<h2>Editar Feedback</h2>
<form action="atualizar_feedback.php" method="POST">
    <input type="hidden" name="idFeedback" value="<?= htmlspecialchars($idFeedback) ?>">
    <label for="titulo">Título:</label>
    <input type="text" name="titulo" value="<?= htmlspecialchars($feedback['titulo']) ?>" required><br>
    <label for="texto">Texto:</label>
    <textarea name="texto" required><?= htmlspecialchars($feedback['texto']) ?></textarea><br>
    <input type="submit" value="Salvar Alterações">
</form>
