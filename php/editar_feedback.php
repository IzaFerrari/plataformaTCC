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

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Meu Perfil - TCCs</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
  <link href="../html/css/estilo.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="pagina-perfil">

<main class="container" style="min-width: 60%">
  <section class="card-perfil">
    <h2>Editar Feedback</h2>
    <form action="atualizar_feedback.php" method="POST">
      <input type="hidden" name="idFeedback" value="<?= htmlspecialchars($idFeedback) ?>">

      <div class="form-group">
        <label for="titulo" style="text-align: left">Título do Projeto:</label>
        <div class="input-container">
          <i class="fas fa-pencil"></i>
          <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($feedback['titulo']) ?>" placeholder="Digite o título do projeto" required>
        </div>
      </div>

      <div class="form-group">
        <label for="texto" style="text-align: left">Feedback:</label>
        <div class="input-container">
          <textarea id="texto" name="texto" placeholder="Escreva o feedback" required><?= htmlspecialchars($feedback['texto']) ?></textarea>
        </div>
      </div>

      <div class="botoes-container">
        <div class="botao-com-texto">
          <button type="submit" class="login-button">
            <i class="fas fa-circle-check"></i>
          </button>
          <p>Salvar alterações</p>
        </div>

        <div class="botao-com-texto">
          <a href="inicio.php" class="login-button" style="text-decoration: none;">
            <i class="fas fa-arrow-circle-left"></i>
          </a>
          <p>Voltar</p>
        </div>
      </div>
    </form>
  </section>
</main>

</body>
</html>
