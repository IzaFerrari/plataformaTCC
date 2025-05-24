<?php
session_start();
include("../php/conexao.php");
include('../php/menu.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../html/login.html");
    exit();
}

$idUsuario = $_SESSION['idUsuario'];

// Verifica se é estudante
$sqlEstudante = "SELECT idEstudante FROM Estudante WHERE idUsuario = ?";
$stmtEstudante = $conn->prepare($sqlEstudante);
$stmtEstudante->bind_param("i", $idUsuario);
$stmtEstudante->execute();
$resultEstudante = $stmtEstudante->get_result();

if ($resultEstudante->num_rows > 0) {
    $rowEstudante = $resultEstudante->fetch_assoc();
    $idEstudante = $rowEstudante['idEstudante'];

    // Busca os TCCs do estudante
    $sqlTCC = "SELECT * FROM TCC WHERE idAutor = ?";
    $stmtTCC = $conn->prepare($sqlTCC);
    $stmtTCC->bind_param("i", $idEstudante);
    $stmtTCC->execute();
    $resultTCC = $stmtTCC->get_result();
} else {
    echo "<h2>Você não possui postagens, pois não é um estudante.</h2>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Meus Posts - TCC Connect</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="../html/css/estilo.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .post-box {
      margin-bottom: 20px;
      background-color: #fff;
      padding: 10px 15px;
      border-radius: 6px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .feedback-box {
      margin-top: 10px;
      background-color: #f5f5f5;
      padding: 8px 12px;
      border-radius: 5px;
      border-left: 4px solid #007bff;
    }
  </style>
</head>
<body class="pagina-perfil">

<main class="container" style="min-width: 90%">
  <div class="grid-tres-colunas">

    <!-- Seção dos posts com feedbacks -->
    <section class="card-perfil">
      <h2>Meus Projetos</h2>

      <?php if ($resultTCC->num_rows > 0): ?>
        <?php while ($row = $resultTCC->fetch_assoc()): ?>
          <div class="post-box">
            <h3><?= htmlspecialchars($row['titulo']) ?></h3>
            <p><i class="fas fa-paperclip"></i> 
              <?php if (!empty($row['link'])): ?>
                <a href="../uploads/<?= htmlspecialchars($row['link']) ?>" target="_blank">Link do Projeto</a>
              <?php else: ?>
                <span>Sem link disponível</span>
              <?php endif; ?>
            </p>
            <p style="text-align: left">Status: <strong><?= htmlspecialchars($row['status']) ?></strong></p>

            <a href="editar_post.php?id=<?= $row['idTCC'] ?>" style="color: blue;">Editar</a> | 
            <a href="excluir_post.php?id=<?= $row['idTCC'] ?>" style="color: red;" onclick="return confirm('Deseja realmente excluir este post?');">Excluir</a>

            <!-- Feedbacks desse TCC -->
            <div style="margin-top: 10px;">
              <h4>Feedbacks:</h4>
              <?php
              $sqlFeedback = "
                SELECT Feedback.*, 
                       COALESCE(Mentor.idMentor, Patrocinador.idPatrocinador) AS autorID,
                       COALESCE(U1.nome, U2.nome) AS autorNome
                FROM Feedback
                LEFT JOIN Mentor ON Feedback.idMentor = Mentor.idMentor
                LEFT JOIN Patrocinador ON Feedback.idPatrocinador = Patrocinador.idPatrocinador
                LEFT JOIN Usuario U1 ON Mentor.idUsuario = U1.idUsuario
                LEFT JOIN Usuario U2 ON Patrocinador.idUsuario = U2.idUsuario
                WHERE Feedback.idTCC = ?";
              $stmtFeedback = $conn->prepare($sqlFeedback);
              $stmtFeedback->bind_param("i", $row['idTCC']);
              $stmtFeedback->execute();
              $resultFeedback = $stmtFeedback->get_result();

              if ($resultFeedback->num_rows > 0):
                  while ($fb = $resultFeedback->fetch_assoc()):
              ?>
                <div class="feedback-box">
                  <strong><?= htmlspecialchars($fb['titulo']) ?></strong> por <?= htmlspecialchars($fb['autorNome']) ?> em <?= htmlspecialchars($fb['data']) ?>
                  <p><?= nl2br(htmlspecialchars($fb['texto'])) ?></p>
                </div>
              <?php endwhile; else: ?>
                <p style="margin-top:5px;">Sem feedbacks até o momento.</p>
              <?php endif; ?>
            </div>

          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>Você ainda não publicou nenhum projeto.</p>
      <?php endif; ?>

    </section>

  </div>
</main>

</body>
</html>
