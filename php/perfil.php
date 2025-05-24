<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    echo "<p>Você precisa estar logado para acessar esta página.</p>";
    exit();
}

include_once('conexao.php');
include('../php/menu.php');

$idUsuario = $_SESSION['idUsuario'];

$sql = "SELECT nome, email, tipoUsuario, foto FROM usuario WHERE idUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    
    $foto_path = !empty($usuario['foto']) && file_exists("uploads/" . $usuario['foto']) ? "uploads/" . htmlspecialchars($usuario['foto']) : "img/perfil.jpg";
    
    $curso = "";
    $anoConclusao = "";
    $status = "";

    if ($usuario['tipoUsuario'] == 'Aluno' || $usuario['tipoUsuario'] == 'Ex-aluno') {
        $sql_estudante = "SELECT * FROM estudante WHERE idUsuario = ?";
        $stmt_estudante = $conn->prepare($sql_estudante);
        $stmt_estudante->bind_param("i", $idUsuario);
        $stmt_estudante->execute();
        $result_estudante = $stmt_estudante->get_result();

        if ($result_estudante->num_rows > 0) {
            $estudante = $result_estudante->fetch_assoc();
            $curso = $estudante['curso'];
            $anoConclusao = $estudante['anoConclusao'];
            $status = $estudante['status'];
        }
    } elseif ($usuario['tipoUsuario'] == 'Mentor') {
        $sql_mentor = "SELECT * FROM mentor WHERE idUsuario = ?";
        $stmt_mentor = $conn->prepare($sql_mentor);
        $stmt_mentor->bind_param("i", $idUsuario);
        $stmt_mentor->execute();
        $result_mentor = $stmt_mentor->get_result();

        if ($result_mentor->num_rows > 0) {
            $mentor = $result_mentor->fetch_assoc();
            $curso = "Área de Especialidade: " . $mentor['areaEspecialidade'];
            $anoConclusao = "Descrição: " . $mentor['descricaoPerfil'];
        }
    } elseif ($usuario['tipoUsuario'] == 'Patrocinador') {
        $sql_patrocinador = "SELECT * FROM patrocinador WHERE idUsuario = ?";
        $stmt_patrocinador = $conn->prepare($sql_patrocinador);
        $stmt_patrocinador->bind_param("i", $idUsuario);
        $stmt_patrocinador->execute();
        $result_patrocinador = $stmt_patrocinador->get_result();

        if ($result_patrocinador->num_rows > 0) {
            $patrocinador = $result_patrocinador->fetch_assoc();
            $curso = "Empresa: " . $patrocinador['empresa'];
            $anoConclusao = "Área de Interesse: " . $patrocinador['areaInteresse'];
        }
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
    <h2>Meu perfil</h2>
    <?php if (!empty($usuario['foto']) && file_exists("uploads/" . $usuario['foto'])) : ?>
    <img src="<?= "uploads/" . htmlspecialchars($usuario['foto']) ?>" alt="<?= htmlspecialchars($usuario['nome']) ?>" class="foto-perfil">
  <?php endif; ?>
    <h3><?= htmlspecialchars($usuario['nome']) ?></h3>
    <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
    <p><strong>Tipo de Usuário:</strong> <?= htmlspecialchars($usuario['tipoUsuario']) ?></p>
    
    <?php if (!empty($curso)) : ?>
      <p><strong><?= $usuario['tipoUsuario'] == 'Aluno' || $usuario['tipoUsuario'] == 'Ex-aluno' ? 'Curso' : '' ?></strong> <?= htmlspecialchars($curso) ?></p>
    <?php endif; ?>

    <?php if (!empty($anoConclusao)) : ?>
      <p><strong><?= $usuario['tipoUsuario'] == 'Aluno' || $usuario['tipoUsuario'] == 'Ex-aluno' ? 'Ano de Conclusão' : '' ?></strong> <?= htmlspecialchars($anoConclusao) ?></p>
    <?php endif; ?>

    <?php if (!empty($status)) : ?>
      <p><strong>Status:</strong> <?= htmlspecialchars($status) ?></p>
    <?php endif; ?>
  </section>

  <div class="botoes-container">
    <div class="botao-com-texto">
      <a href="../php/editar_perfil.php" class="login-button">
        <span class="edit-icon">
          <i class="fas fa-file-pen" style="font-size: 2rem; padding-top: 13px; padding-left: 7px"></i>
        </span>
      </a>
      <p>Editar Perfil</p>
    </div>

    <div class="botao-com-texto">
      <a href="../php/excluir_conta.php" class="login-button" style="color: red">
        <i class="fas fa-circle-xmark"></i>
      </a>
      <p>Excluir conta</p>
    </div>
  </div>
</main>

</body>
</html>

<?php
} else {
    echo "<p>Usuário não encontrado.</p>";
}
?>
