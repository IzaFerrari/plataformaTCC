<?php 
include("conexao.php");
include('../php/menu.php'); 
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Mentores Voluntários - Sistema TCCs</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&amp;display=swap" rel="stylesheet"/>
<link href="css/estilo2.css" rel="stylesheet" type="text/css"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>
</head>
<body>
<main class="main-busca">

  <div class="painel-lateral">
    <form class="form-busca" method="GET" action="">
      <label for="search"><strong>Buscar Mentores por Área de Especialidade:</strong></label>
      <input id="search" name="busca" placeholder="Digite a área de especialidade" type="text"
        value="<?php echo isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : ''; ?>" />
      <button class="btn-buscar" style="font-size: 2rem" type="submit"><i class="fas fa-search"></i></button>
    </form>
  </div>

  <div class="painel-resultado">
    <p class="resultado-titulo">Resultados encontrados:</p>
  </div>

  <div class="mentors-container">

    <?php
    $sql = "SELECT   
                u.nome,  
                u.email,  
                u.telefone,  
                u.foto,  
                m.areaEspecialidade,  
                m.descricaoPerfil  
            FROM Usuario u  
            JOIN Mentor m ON u.idUsuario = m.idUsuario  
            WHERE u.status = 'Ativo' AND u.tipoUsuario = 'Mentor'";

    if (isset($_GET['busca']) && !empty($_GET['busca'])) {
        $busca = mysqli_real_escape_string($conn, $_GET['busca']);
        $sql .= " AND m.areaEspecialidade LIKE '%$busca%'";
    }

    $resultado = mysqli_query($conn, $sql);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        while ($mentor = mysqli_fetch_assoc($resultado)) {
            $foto = !empty($mentor['foto']) ? "uploads/" . htmlspecialchars($mentor['foto']) : 'img/sabrina.png';

            echo '<div class="mentor-card destaque">';
            echo "<img class='mentor-image' src='{$foto}' alt='Foto de " . htmlspecialchars($mentor['nome']) . "' />";
            echo "<div class='mentor-name'>" . htmlspecialchars($mentor['nome']) . "</div>";
            echo "<div class='mentor-description'><strong>Descrição:</strong> " . nl2br(htmlspecialchars($mentor['descricaoPerfil'])) . "</div>";
            echo "<div class='mentor-info'>";
            echo "<strong>Área de Especialidade:</strong> " . htmlspecialchars($mentor['areaEspecialidade']) . "<br/><br/>";
            echo "<strong>Email:</strong> " . htmlspecialchars($mentor['email']) . "<br/>";
            echo "<strong>Telefone:</strong> " . (!empty($mentor['telefone']) ? htmlspecialchars($mentor['telefone']) : '-') ;
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p>Nenhum mentor encontrado.</p>";
    }

    mysqli_close($conn);
    ?>

  </div>
</main>
</body>
</html>
