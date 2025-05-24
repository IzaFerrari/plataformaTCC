<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once('conexao.php');
include_once('protecao.php');

if (!isset($_SESSION['idUsuario'])) {
    die("Erro: Usuário não autenticado.");
}

$idUsuario = $_SESSION['idUsuario'];
$idTCC = $_POST['idTCC'] ?? null;
$titulo = $_POST['titulo'] ?? null;
$texto = $_POST['texto'] ?? null;

if (!$idTCC || !$titulo || !$texto) {
    die("Erro: Todos os campos são obrigatórios.");
}

$sql = "SELECT tipoUsuario FROM usuario WHERE idUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$res = $stmt->get_result();
$usuario = $res->fetch_assoc();

if (!$usuario || ($usuario['tipoUsuario'] !== 'Mentor' && $usuario['tipoUsuario'] !== 'Patrocinador')) {
    die("Erro: Você não tem permissão para enviar feedback.");
}

if ($usuario['tipoUsuario'] === 'Mentor') {
    $sql_id = "SELECT idMentor AS id FROM mentor WHERE idUsuario = ?";
} else {
    $sql_id = "SELECT idPatrocinador AS id FROM patrocinador WHERE idUsuario = ?";
}

$stmt_id = $conn->prepare($sql_id);
$stmt_id->bind_param("i", $idUsuario);
$stmt_id->execute();
$res_id = $stmt_id->get_result();
$row_id = $res_id->fetch_assoc();

if (!$row_id) {
    die("Erro: Não foi possível localizar seu perfil de " . $usuario['tipoUsuario']);
}

$idMentor = $usuario['tipoUsuario'] === 'Mentor' ? $row_id['id'] : null;
$idPatrocinador = $usuario['tipoUsuario'] === 'Patrocinador' ? $row_id['id'] : null;

$sql_check = "SELECT idFeedback FROM feedback WHERE idTCC = ? AND ((idMentor = ?) OR (idPatrocinador = ?))";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("iii", $idTCC, $idMentor, $idPatrocinador);
$stmt_check->execute();
$res_check = $stmt_check->get_result();

if ($res_check->num_rows > 0) {
    $sql_up = "UPDATE feedback SET titulo = ?, texto = ?, data = CURRENT_TIMESTAMP WHERE idTCC = ? AND ((idMentor = ?) OR (idPatrocinador = ?))";
    $stmt_up = $conn->prepare($sql_up);
    $stmt_up->bind_param("ssiii", $titulo, $texto, $idTCC, $idMentor, $idPatrocinador);
    $stmt_up->execute();
} else {
    $sql_in = "INSERT INTO feedback (titulo, texto, idTCC, idMentor, idPatrocinador) VALUES (?, ?, ?, ?, ?)";
    $stmt_in = $conn->prepare($sql_in);
    $stmt_in->bind_param("ssiii", $titulo, $texto, $idTCC, $idMentor, $idPatrocinador);
    $stmt_in->execute();
}

header("Location: inicio.php?msg=Feedback enviado com sucesso");
exit();

$conn->close();
?>
