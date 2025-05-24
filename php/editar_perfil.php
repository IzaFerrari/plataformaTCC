<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    echo "<p>Você precisa estar logado para acessar esta página.</p>";
    exit();
}

include_once('conexao.php');
include('../php/menu.php');

$idUsuario = $_SESSION['idUsuario'];
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $tipoUsuario = $_POST['tipoUsuario'];
    $senha = $_POST['senha'];

    // Atualizar dados principais
    $sql = "UPDATE usuario SET nome = ?, email = ?, telefone = ?, tipoUsuario = ? WHERE idUsuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nome, $email, $telefone, $tipoUsuario, $idUsuario);
    $success = $stmt->execute();

    // Atualizar senha se informada
    if (!empty($senha)) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $sqlSenha = "UPDATE usuario SET senha = ? WHERE idUsuario = ?";
        $stmtSenha = $conn->prepare($sqlSenha);
        $stmtSenha->bind_param("si", $senhaHash, $idUsuario);
        $stmtSenha->execute();
    }

    // Processar upload da foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fotoNome = basename($_FILES['foto']['name']);
        $fotoCaminho = "uploads/" . $fotoNome;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $fotoCaminho)) {
            $sqlFoto = "UPDATE usuario SET foto = ? WHERE idUsuario = ?";
            $stmtFoto = $conn->prepare($sqlFoto);
            $stmtFoto->bind_param("si", $fotoNome, $idUsuario);
            $stmtFoto->execute();
        }
    }

    if ($success) {
        $mensagem = "Alterações salvas com sucesso.";
    } else {
        $mensagem = "Erro ao atualizar perfil.";
    }
}

// Buscar dados atualizados
$sql = "SELECT nome, email, telefone, tipoUsuario FROM usuario WHERE idUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    // Define link correto para "Editar outras informações" conforme o tipo de usuário
$linkEdicaoExtra = '#'; // padrão

switch ($usuario['tipoUsuario']) {
    case 'Aluno':
    case 'Ex-aluno':
        $linkEdicaoExtra = 'editar_estudante.php';
        break;
    case 'Mentor':
        $linkEdicaoExtra = 'editar_mentor.php';
        break;
    case 'Patrocinador':
        $linkEdicaoExtra = 'editar_patrocinador.php';
        break;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Meu Perfil - Editar - TCCs</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
  <link href="../html/css/estilo.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="pagina-perfil">

<main class="container" style="min-width: 60%">
  <section class="card-perfil">
    <h2>Editar perfil</h2>

    <?php if (!empty($mensagem)) : ?>
      <p style="color: green; font-weight: bold;"><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>

    <form action="editar_perfil.php" method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label for="nome" style="text-align: left">Nome:</label>
        <div class="input-container">
          <i class="fas fa-user"></i>
          <input type="text" id="nome" name="nome" placeholder="Seu nome" value="<?= htmlspecialchars($usuario['nome']) ?>">
        </div>
      </div>

      <div class="form-group">
        <label for="email" style="text-align: left">E-mail:</label>
        <div class="input-container">
          <i class="fas fa-envelope fa-lg"></i>
          <input type="email" id="email" name="email" placeholder="Seu e-mail" value="<?= htmlspecialchars($usuario['email']) ?>">
        </div>
      </div>

      <div class="form-group">
        <label for="senha" style="text-align: left">Nova senha (deixe em branco caso não queira alterar):</label>
        <div class="input-container">
          <i class="fas fa-lock fa-lg"></i>
          <input type="password" id="senha" name="senha" placeholder="Sua senha">
        </div>
      </div>

      <div class="form-group">
        <label for="telefone" style="text-align: left">Telefone:</label>
        <div class="input-container">
          <i class="fas fa-phone fa-lg"></i>
          <input type="tel" id="telefone" name="telefone" placeholder="Seu telefone" value="<?= htmlspecialchars($usuario['telefone']) ?>">
        </div>
      </div>

      <div class="form-group">
        <label for="perfil" style="text-align: left">Tipo de usuário:</label>
        <div class="custom-select">
          <div class="select-trigger" id="selectTrigger">
            <i id="iconeSelecionado" class="fas fa-question-circle"></i>
            <span id="textoSelecionado"><?= htmlspecialchars($usuario['tipoUsuario']) ?></span>
            <span class="seta">&#9662;</span>
          </div>
          <div class="options" id="options">
            <div class="option" data-value="Aluno" data-icon="fas fa-graduation-cap">
              <i class="fas fa-graduation-cap"></i><span>Aluno</span>
            </div>
            <div class="option" data-value="Mentor" data-icon="fas fa-chalkboard-teacher">
              <i class="fas fa-chalkboard-teacher"></i><span>Mentor</span>
            </div>
            <div class="option" data-value="Patrocinador" data-icon="fas fa-hand-holding-usd">
              <i class="fas fa-hand-holding-usd"></i><span>Patrocinador</span>
            </div>
          </div>
        </div>
        <input type="hidden" name="tipoUsuario" id="tipoUsuario" value="<?= htmlspecialchars($usuario['tipoUsuario']) ?>">
      </div>

      <div class="form-group">
        <label for="foto" style="text-align: left">Foto do Perfil:</label>
        <div style="display: flex; align-items: center;">
          <input type="file" id="foto" name="foto" style="display: none;" onchange="updateFileName()" />
          <button type="button" onclick="document.getElementById('foto').click()">Escolher arquivo</button>
          <span id="file-name" style="margin-left: 10px;">Nenhum arquivo escolhido</span>
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
  <a href="<?= $linkEdicaoExtra ?>" class="login-button">
    <i class="fas fa-plus-circle"></i>
  </a>
  <p>Editar outras informações</p>
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

<script>
function updateFileName() {
  const input = document.getElementById('foto');
  const fileName = input.files.length > 0 ? input.files[0].name : "Nenhum arquivo escolhido";
  document.getElementById('file-name').textContent = fileName;
}

const selectTrigger = document.getElementById('selectTrigger');
const options = document.getElementById('options');
const textoSelecionado = document.getElementById('textoSelecionado');
const iconeSelecionado = document.getElementById('iconeSelecionado');
const inputTipoUsuario = document.getElementById('tipoUsuario');

selectTrigger.addEventListener('click', () => {
  options.style.display = options.style.display === 'block' ? 'none' : 'block';
});

const optionItems = document.querySelectorAll('.option');
optionItems.forEach(item => {
  item.addEventListener('click', () => {
    const texto = item.textContent.trim();
    const iconeClass = item.getAttribute('data-icon');
    const valor = item.getAttribute('data-value');

    textoSelecionado.textContent = texto;
    iconeSelecionado.className = iconeClass;
    inputTipoUsuario.value = valor;

    options.style.display = 'none';
  });
});

document.addEventListener('click', function(event) {
  if (!event.target.closest('.custom-select')) {
    options.style.display = 'none';
  }
});
</script>
</body>
</html>
<?php
} else {
    echo "<p>Usuário não encontrado.</p>";
}
?>
