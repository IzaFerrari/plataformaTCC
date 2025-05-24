<?php
session_start();
include_once('conexao.php');
include_once('protecao.php');
include('../php/menu.php');

if (!isset($_SESSION['idUsuario'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "ID do post não fornecido.";
    exit();
}

$idTCC = intval($_GET['id']);

$sql = "SELECT * FROM tcc WHERE idTCC = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idTCC);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Post não encontrado.";
    exit();
}

$post = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Meu Perfil - Editar Projeto - TCCs</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
  <link href="../html/css/estilo.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="pagina-perfil">

<main class="container" style="min-width: 60%">
  <section class="card-perfil">
    <h2>Editar Projeto</h2>
    <form action="atualizar_post.php" method="POST">
      <input type="hidden" name="idTCC" value="<?php echo $post['idTCC']; ?>">

      <div class="form-group">
        <label for="titulo">Título do Projeto:</label>
        <div class="input-container">
          <i class="fas fa-pencil"></i>
          <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($post['titulo']); ?>" placeholder="Digite o título do projeto" required>
        </div>
      </div>

      <div class="form-group">
        <label for="descricao">Descrição:</label>
        <div class="input-container">
          <textarea id="descricao" name="descricao" placeholder="Escreva uma descrição detalhada" required><?php echo htmlspecialchars($post['descricao']); ?></textarea>
        </div>
      </div>

      <div class="form-group">
        <label for="linkProjeto">Link do Projeto (opcional):</label>
        <div class="input-container">
          <i class="fas fa-link"></i>
          <input type="url" id="linkProjeto" name="linkProjeto" value="<?php echo htmlspecialchars($post['link']); ?>" placeholder="https://exemplo.com/seu-projeto">
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
