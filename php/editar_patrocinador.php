<?php
session_start();
include('conexao.php');
include('../php/menu.php');


$idUsuario = $_SESSION['idUsuario'] ?? null;

if (!$idUsuario) {
    die("Usuário não autenticado.");
}

// Inicializa variável de mensagem
$mensagemSucesso = false;

// Buscar dados do patrocinador
$query = "SELECT p.empresa, p.areaInteresse, p.descricaoPerfil FROM Patrocinador p WHERE p.idUsuario = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$dados = $result->fetch_assoc();

$empresa = $dados['empresa'] ?? '';
$interesse = $dados['areaInteresse'] ?? '';
$descricao = $dados['descricaoPerfil'] ?? '';

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novaEmpresa = $_POST['empresa'] ?? '';
    $novoInteresse = $_POST['interesse'] ?? '';
    $novaDescricao = $_POST['descricao'] ?? '';

    $update = $conn->prepare("UPDATE Patrocinador SET empresa = ?, areaInteresse = ?, descricaoPerfil = ? WHERE idUsuario = ?");
    $update->bind_param("sssi", $novaEmpresa, $novoInteresse, $novaDescricao, $idUsuario);
    $update->execute();

    $mensagemSucesso = true;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Perfil - Patrocinador</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
  <link href="../html/css/estilo.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="pagina-perfil">

<main class="container" style="min-width: 60%">
  <section class="card-perfil">
    <h2>Editar Perfil - Patrocinador</h2>

    <?php if ($mensagemSucesso): ?>
      <p style="color: green; font-weight: bold;">Alterações salvas com sucesso!</p>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label for="empresa" style="text-align: left">Empresa:</label>
        <div class="input-container">
          <i class="fas fa-building"></i>
          <input type="text" id="empresa" name="empresa" placeholder="Sua empresa" value="<?= htmlspecialchars($empresa) ?>">
        </div>
      </div>

      <div class="form-group">
        <label for="interesse" style="text-align: left">Área de Interesse:</label>
        <div class="input-container">
          <i class="fas fa-comment-dots"></i>
          <input type="text" id="interesse" name="interesse" placeholder="Seu interesse" value="<?= htmlspecialchars($interesse) ?>">
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
          <button class="login-button" type="submit" name="salvar">
            <i class="fas fa-circle-check"></i>
          </button>
          <p>Salvar alterações</p>
        </div>

        <div class="botao-com-texto">
          <a href="perfil.php">
            <button class="login-button" type="button">
              <i class="fas fa-arrow-circle-left"></i>
            </button>
            <p>Voltar ao Perfil</p>
          </a>
        </div>
      </div>
    </form>
  </section>
</main>

</body>
</html>
