<?php 
include("conexao.php"); 
include('../php/menu.php');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Sobre o Projeto - TCCs</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
    }
    h2 {
      margin-bottom: 20px;
    }
    .topo-projeto {
      display: flex;
      margin-bottom: 30px;
    }
    .btn-mais, .voltar-btn {
     border-radius: 20px;
      background-color: #000000;
      color: white;
      padding: 8px 14px;
      text-decoration: none;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }
    .btn-mais:hover, .voltar-btn:hover {
      background-color: #0056b3;
    }
    .fotos-time {
      display: flex;
      justify-content: space-around;
      flex-wrap: wrap;
      gap: 25px;
      margin-top: 20px;
    }
    .perfil {
      text-align: center;
      width: 180px;
    }
    .perfil img {
      width: 150px;
      height: 150px;
      border-radius: 50%;
	border: 2px solid black;
      object-fit: cover;
      box-shadow: 0 2px 5px rgba(0,0,0,0.15);
      transition: transform 0.3s ease;
    }
    .perfil img:hover {
      transform: scale(1.05);
    }
    .perfil p {
      margin-top: 12px;
      font-weight: bold;
      font-size: 1.1em;
      color: #333;
    }
    p.descricao-projeto {
      margin-top: 30px;
      font-size: 1em;
      line-height: 1.5;
      color: #555;
    }
  </style>
</head>
<body>

<main class="about-container">
  <section>
    <div class="topo-projeto">
      <h2>Sobre o Projeto</h2>
      <a href="index.php" class="btn-mais voltar-btn">Voltar ao Início</a>
    </div>
    
    <div class="fotos-time">
      <div class="perfil">
        <img src="img/sara.png" alt="Sara">
        <p>Sara</p>
      </div>
      <div class="perfil">
        <img src="img/yuri.png" alt="Kleber">
        <p>Kleber</p>
      </div>
      <div class="perfil">
        <img src="img/luis.png" alt="Luis">
        <p>Luis</p>
      </div>
      <div class="perfil">
        <img src="img/izabely.png" alt="Izabely">
        <p>Izabely</p>
      </div>
    </div>

    <p class="descricao-projeto">
      Este projeto foi desenvolvido como TCC de Análise e Desenvolvimento de Sistemas do curso do EADTEC.
    </p>
  </section>
</main>

</body>
</html>
