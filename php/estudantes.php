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
    // Query para buscar estudantes e ex-alunos ativos
    $sql = "SELECT 
              u.nome, 
              u.email, 
              u.telefone, 
              u.foto, 
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
</body>
</html>