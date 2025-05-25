<?php
// Pega o nome do arquivo atual, ex: inicio.php, perfil.php, etc.
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Sobre o Projeto - TCCs</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
  <link href="../html/css/estilo2.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
  <nav>
    <div class="logo2">
      TCCs
      <span>Centro Paula Souza</span>
    </div>
     <div class="menu2">
    <a href="../php/inicio.php" class="<?php if($current_page == 'inicio.php') echo 'active'; ?>">Página Inicial</a>
    <a href="../php/perfil.php" class="<?php if($current_page == 'perfil.php') echo 'active'; ?>">Meu Perfil</a>
    <a href="../php/estudantes.php" class="<?php if($current_page == 'estudantes.php') echo 'active'; ?>">Estudantes</a>
    <a href="../php/mentores.php" class="<?php if($current_page == 'mentores.php') echo 'active'; ?>">Mentores</a>
    <a href="../php/patrocinadores.php" class="<?php if($current_page == 'patrocinadores.php') echo 'active'; ?>">Patrocinadores</a>
    <a href="../php/sobre.php" class="<?php if($current_page == 'sobre.php') echo 'active'; ?>">Sobre o Projeto</a>
    <a href="../php/logout.php">Sair</a>
  </div>
  </nav>
