<?php
session_start();
require_once 'conexao.php';
include('../php/menu.php');


if (!isset($_SESSION['idUsuario'])) {
    echo "<p>Você precisa estar logado para acessar esta página.</p>";
    exit();
}

$idUsuario = $_SESSION['idUsuario'];
$mensagem = "";

// Processar envio
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $area = $_POST['area'] ?? '';
    $descricao = $_POST['descricao'] ?? '';

    $sql = "UPDATE Mentor SET areaEspecialidade = ?, descricaoPerfil = ? WHERE idUsuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $area, $descricao, $idUsuario);
    $success = $stmt->execute();

    $mensagem = $success ? "Dados atualizados com sucesso." : "Erro ao atualizar os dados.";
}

// Buscar dados atuais
$sql = "SELECT areaEspecialidade, descricaoPerfil FROM Mentor WHERE idUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$mentor = $result->fetch_assoc();

$area = $mentor['areaEspecialidade'] ?? '';
$descricao = $mentor['descricaoPerfil'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Perfil - Mentor</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
  <link href="../html/css/estilo.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="pagina-perfil">

<main class="container" style="min-width: 60%">
  <section class="card-perfil">
    <h2>Editar Perfil - Mentor</h2>

    <?php if (!empty($mensagem)) : ?>
      <p style="color: green; font-weight: bold;"><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>

    <form method="POST" action="editar_mentor.php">
      <div class="form-group">
        <label for="area" style="text-align: left">Área de Especialidade:</label>
        <div class="input-container">
          <i class="fas fa-user-graduate"></i>
          <input type="text" id="area" name="area" placeholder="Sua especialidade" value="<?= htmlspecialchars($area) ?>">
        </div>
      </div>

      <div class="form-group">
        <label for="descricao" style="text-align: left">Descrição do Perfil:</label>
        <div class="input-container">
          <textarea id="descricao" name="descricao" placeholder="Digite aqui um resumo sobre o seu perfil"><?= htmlspecialchars($descricao) ?></textarea>
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
