<?php
include("conexao.php");
include('../php/menu.php');
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Estudantes e Ex-Alunos - Sistema TCCs</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&amp;display=swap" rel="stylesheet"/>
<link href="css/estilo2.css" rel="stylesheet" type="text/css"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>
</head>
<body>
<main class="main-busca">
  <div class="painel-lateral">
    <form class="form-busca" method="GET" action="">
      <label for="search"><strong>Pesquisar estudantes por nome ou curso:</strong></label>
      <input id="search" name="busca" placeholder="Digite nome ou curso" type="text" value="<?php echo isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : ''; ?>"/>
      <button class="btn-buscar" style="font-size: 2rem" type="submit">
        <i class="fas fa-search"></i>
      </button>
    </form>
  </div>
  
  <div class="painel-resultado">
    <p class="resultado-titulo">Resultados encontrados:</p>
  </div>
  
  <div class="mentors-container">
    <?php
    $sql = "SELECT 
              u.idUsuario,
              u.nome, 
              u.email, 
              u.telefone, 
              u.foto, 
              e.idEstudante,
              e.curso, 
              e.anoConclusao, 
              e.status 
            FROM Usuario u
            JOIN Estudante e ON u.idUsuario = e.idUsuario
            WHERE u.status = 'Ativo' 
              AND (u.tipoUsuario = 'Aluno' OR u.tipoUsuario = 'Ex-aluno')";
    
    if (isset($_GET['busca']) && !empty(trim($_GET['busca']))) {
        $busca = mysqli_real_escape_string($conn, trim($_GET['busca']));
        $sql .= " AND (u.nome LIKE '%$busca%' OR e.curso LIKE '%$busca%')";
    }
    
    $resultado = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($resultado) > 0) {
        while ($estudante = mysqli_fetch_assoc($resultado)) {
            $foto = !empty($estudante['foto']) ? "uploads/" . htmlspecialchars($estudante['foto']) : 'imagens/default.png';
            ?>
            <div class="mentor-card destaque">
              <img alt="Foto de <?php echo htmlspecialchars($estudante['nome']); ?>" class="mentor-image" src="<?php echo $foto; ?>"/>
              <div class="mentor-name"><?php echo htmlspecialchars($estudante['nome']); ?></div>
              <div class="mentor-description">
                <strong>Curso:</strong> <?php echo htmlspecialchars($estudante['curso']); ?><br/><br/>
                <strong>Ano de Conclusão:</strong> <?php echo htmlspecialchars($estudante['anoConclusao']); ?><br/><br/>
                <strong>Status:</strong> <?php echo htmlspecialchars($estudante['status']); ?>
              </div>
              <div class="mentor-info">
                <strong>Email:</strong> <?php echo htmlspecialchars($estudante['email']); ?><br/>
              </div>

              <!-- Toggle TCCs/Projetos -->
              <div style="margin-top: 1em;">
                <button 
                  type="button" 
                  onclick="toggleToggle(this)" 
                  style="all: unset; cursor: pointer; color: inherit; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 0.5em;">
                  <span style="display:inline-block; transition: transform 0.3s ease;" class="toggle-arrow">&#9656;</span> 
                  Ver tccs/projetos
                </button>
                <div class="toggle-content" style="display: none; padding-left: 1em;">
                  <?php
                  $idEstudante = (int)$estudante['idEstudante'];
                  $sqlTCC = "SELECT titulo, link FROM TCC WHERE idAutor = $idEstudante";
                  $resultTCC = mysqli_query($conn, $sqlTCC);
                  if ($resultTCC && mysqli_num_rows($resultTCC) > 0) {
                      while ($tcc = mysqli_fetch_assoc($resultTCC)) {
                          $titulo = htmlspecialchars($tcc['titulo']);
                          $link = htmlspecialchars($tcc['link']);
                          echo '<div style="margin-bottom:0.4em;">';
                          echo '<a href="'. $link .'" target="_blank" style="color: inherit; text-decoration: underline;">'. $titulo .'</a>';
                          echo '</div>';
                      }
                  } else {
                      echo '<div>(Nenhum projeto encontrado)</div>';
                  }
                  ?>
                </div>
              </div>
            </div>
            <?php
        }
    } else {
        echo "<p>Nenhum aluno ou ex-aluno encontrado.</p>";
    }
    mysqli_close($conn);
    ?>
  </div>
</main>

<script>
function toggleToggle(button) {
  const content = button.nextElementSibling;
  const arrow = button.querySelector('.toggle-arrow');
  if (content.style.display === 'block') {
    content.style.display = 'none';
    arrow.style.transform = 'rotate(0deg)';
  } else {
    content.style.display = 'block';
    arrow.style.transform = 'rotate(90deg)';
  }
}
</script>

</body>
</html>