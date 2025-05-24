<?php  
session_start();

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../php/index.php");
    exit();
}

include_once('../php/conexao.php');
include_once('../php/protecao.php');
include('../php/menu.php');

$status_message = '';
if (isset($_GET['status']) && $_GET['status'] == 'deleted') {
    $status_message = "Post apagado com sucesso!";
} elseif (isset($_GET['error']) && $_GET['error'] == 'unauthorized') {
    $status_message = "Você não tem permissão para realizar esta ação.";
}

$idUsuario = $_SESSION['idUsuario'];

// Buscar tipo de usuário
$tipoUsuario = null;
$sql = "SELECT nome, tipoUsuario FROM usuario WHERE idUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $tipoUsuario = $row['tipoUsuario'];
    $_SESSION['nome'] = $row['nome'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <link href="../html/css/estilo.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="pagina-perfil">

<header>
    
</header>

<main class="container" style="max-width: 1440px; width: 100%; margin: 2rem auto 0 auto; padding: 2rem;">

     <h1 style="text-align: center; margin-bottom: 2rem;">TCCs Postados</h1>

    <?php if (!empty($status_message)): ?>
        <div class="status-message" style="text-align: center; color: green; font-weight: bold;">
            <p><?php echo htmlspecialchars($status_message); ?></p>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['msg'])): ?>
        <div class="status-message" style="text-align: center; color: green; font-weight: bold; margin-bottom: 1rem;">
            <p><?php echo htmlspecialchars($_GET['msg']); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="grid-projetos" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">

<?php

$formularioNovoPost = '';
if ($tipoUsuario === 'Aluno' || $tipoUsuario === 'Ex-aluno') {
    $formularioNovoPost = '<section class="card-projeto">';
    $formularioNovoPost .= '<h3 style="text-align: left">Publicar Post</h3>';
    $formularioNovoPost .= '<form action="../php/postar_post.php" method="POST">
            <label for="titulo"><strong>Título:</strong></label><br>
            <input type="text" id="titulo" name="titulo" required><br><br>

            <label for="descricao"><strong>Descrição:</strong></label><br>
            <textarea id="descricao" name="descricao" rows="4" required></textarea><br><br>

            <label for="linkProjeto"><strong>Link do Projeto:</strong></label><br>
            <input type="url" id="linkProjeto" name="linkProjeto"><br><br>

            <button type="submit" class="login-button" style="font-size: 1rem"><strong>Publicar</strong></button>
          </form>';
    $formularioNovoPost .= '</section>';
    echo $formularioNovoPost;
}

$sql = "SELECT tcc.*, usuario.nome FROM tcc 
        JOIN estudante ON tcc.idAutor = estudante.idEstudante
        JOIN usuario ON estudante.idUsuario = usuario.idUsuario
        ORDER BY tcc.idTCC DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<section class="card-projeto">';
        echo '<h2>' . htmlspecialchars($row['titulo']) . '</h2>';
        echo '<p>' . nl2br(htmlspecialchars($row['descricao'])) . '</p>';
        echo '<p><i class="fas fa-paperclip"></i> <a href="' . htmlspecialchars($row['link']) . '" target="_blank">Link do Projeto</a></p>';
        echo '<p style="text-align: left">Postado por <strong>' . htmlspecialchars($row['nome']) . '</strong> <br>Status: <strong>' . htmlspecialchars($row['status']) . '</strong></p>';

        $sql_autor = "SELECT idEstudante FROM estudante WHERE idUsuario = ?";
        $stmt_autor = $conn->prepare($sql_autor);
        $stmt_autor->bind_param("i", $idUsuario);
        $stmt_autor->execute();
        $result_autor = $stmt_autor->get_result();

        if ($result_autor && $result_autor->num_rows > 0) {
            $autor = $result_autor->fetch_assoc();
            $idEstudanteLogado = $autor['idEstudante'];

            if ($row['idAutor'] == $idEstudanteLogado) {
                echo '<a href="../php/editar_post.php?id=' . $row['idTCC'] . '" style="color: blue;">Editar</a> | ';
                echo '<form action="../php/apagar_post.php" method="POST" style="display:inline;">
                        <input type="hidden" name="idTCC" value="' . $row['idTCC'] . '">
                        <button type="submit" style="color: red; background: none; border: none; cursor: pointer;">Excluir</button>
                      </form>';
            }
        }

        echo '<h3 style="text-align: left; margin-top: 1.5rem;">Feedbacks:</h3>';

        $sql_fb = "SELECT f.idFeedback, f.titulo, f.texto, f.data, u.nome
                   FROM feedback f
                   LEFT JOIN mentor m ON f.idMentor = m.idMentor
                   LEFT JOIN patrocinador p ON f.idPatrocinador = p.idPatrocinador
                   LEFT JOIN usuario u ON u.idUsuario = COALESCE(m.idUsuario, p.idUsuario)
                   WHERE f.idTCC = ?
                   ORDER BY f.data DESC";
        $stmt_fb = $conn->prepare($sql_fb);
        $stmt_fb->bind_param("i", $row['idTCC']);
        $stmt_fb->execute();
        $result_fb = $stmt_fb->get_result();

        if ($result_fb->num_rows > 0) {
            while ($fb = $result_fb->fetch_assoc()) {
                echo '<div class="feedback-box" style="border-top: 1px solid #ccc; padding-top: 0.5rem; margin-top: 0.5rem;">';
                echo '<strong>' . htmlspecialchars($fb['titulo']) . '</strong> por ' . htmlspecialchars($fb['nome']) . ' em ' . $fb['data'];
                echo '<p>' . nl2br(htmlspecialchars($fb['texto'])) . '</p>';
                if ($fb['nome'] === $_SESSION['nome']) {
                    echo '<a href="../php/editar_feedback.php?id=' . $fb['idFeedback'] . '" style="color: blue;">Editar</a> | ';
                    echo '<form action="../php/apagar_feedback.php" method="POST" style="display:inline;">
                            <input type="hidden" name="idFeedback" value="' . $fb['idFeedback'] . '">
                            <button type="submit" style="color:red; background:none; border:none; cursor:pointer;">Excluir</button>
                          </form>';
                }
                echo '</div>';
            }
        } else {
            echo '<p>Nenhum feedback ainda.</p>';
        }

        if ($tipoUsuario === 'Mentor' || $tipoUsuario === 'Patrocinador') {
    echo '<button class="toggle-feedback" style="margin-top: 1rem;">Deixar Feedback</button>';
    echo '<div class="form-feedback" style="display: none; margin-top: 1rem;">
            <form action="../php/enviar_feedback.php" method="POST">
                <input type="hidden" name="idTCC" value="' . $row['idTCC'] . '">
                <label for="titulo"><strong>Título:</strong></label><br>
                <input type="text" name="titulo" required><br><br>
                <label for="texto"><strong>Texto:</strong></label><br>
                <textarea name="texto" required rows="4"></textarea><br><br>
                <button type="submit" class="login-button" style="font-size: 1rem"><strong>Enviar Feedback</strong></button>
            </form>
          </div>';
}

        echo '</section>';
    }
} else {
    echo '<p>Nenhum post encontrado.</p>';
}
?>

    </div>
</main>

<script>
  document.querySelectorAll('.toggle-feedback').forEach(button => {
    button.addEventListener('click', () => {
      const form = button.nextElementSibling;
      const isVisible = form.style.display === 'block';
      form.style.display = isVisible ? 'none' : 'block';
      button.textContent = isVisible ? 'Deixar Feedback' : 'Ocultar Feedback';
    });
  });
</script>


</body>
</html>
