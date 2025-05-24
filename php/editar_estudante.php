<?php
session_start();
include_once('conexao.php');
include('../php/menu.php');

if (!isset($_SESSION['idUsuario'])) {
    echo "<p>Você precisa estar logado para acessar esta página.</p>";
    exit();
}

$idUsuario = $_SESSION['idUsuario'];
$mensagem = '';

// Processar envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $curso = $_POST['curso'] ?? '';
    $anoConclusao = $_POST['data'] ?? '';

    $sql = "UPDATE estudante SET curso = ?, anoConclusao = ? WHERE idUsuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $curso, $anoConclusao, $idUsuario);
    $success = $stmt->execute();

    $mensagem = $success ? "Dados atualizados com sucesso." : "Erro ao atualizar os dados.";
}

// Buscar dados existentes do estudante
$sql = "SELECT curso, anoConclusao FROM estudante WHERE idUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$estudante = $result->fetch_assoc();

$curso = $estudante['curso'] ?? '';
$anoConclusao = $estudante['anoConclusao'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Estudante - TCCs</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
  <link href="../html/css/estilo.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="pagina-perfil">

<main class="container" style="min-width: 60%">
  <section class="card-perfil">
    <h2>Editar Perfil - Aluno</h2>

    <?php if (!empty($mensagem)) : ?>
      <p style="color: green; font-weight: bold;"><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>

    <form method="POST" action="editar_estudante.php">
      <div class="form-group">
        <label for="curso" style="text-align: left">Curso:</label>
        <div class="input-container">
          <i class="fas fa-graduation-cap"></i>
          <input type="text" id="curso" name="curso" placeholder="Seu curso" value="<?= htmlspecialchars($curso) ?>">
        </div>
      </div>

      <div class="form-group">
        <label for="data" style="text-align: left">Ano de conclusão:</label>
        <div class="input-container">
          <i class="fas fa-calendar-days fa-lg"></i>
          <input type="text" id="data" name="data" placeholder="Digite o ano de conclusão" value="<?= htmlspecialchars($anoConclusao) ?>">
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
          <a href="perfil.php" class="login-button">
            <i class="fas fa-arrow-circle-left"></i>
          </a>
          <p>Voltar ao Perfil</p>
        </div>
      </div>
    </form>
  </section>
</main>

</body>
</html>
