<!-- menu.php -->
<style>
    body {
        padding-top: 60px;
    }

    nav.menu {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        background-color: #333;
        padding: 15px 30px;
        z-index: 1000;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }

    nav.menu a {
        color: white;
        text-decoration: none;
        margin-right: 20px;
        font-weight: bold;
        transition: color 0.3s ease;
    }

    nav.menu a:hover {
        color: #ffcc00;
    }
</style>

<nav class="menu">
    <a href="../html/inicio.php">Página Inicial</a>
    <a href="../php/perfil.php">Meu Perfil</a>
    <a href="../php/mentores.php">Mentores</a>
    <a href="../php/patrocinadores.php">Patrocinadores</a>
    <a href="../php/sobre.php">Sobre o Projeto</a>
    <a href="../php/logout.php">Sair</a>
</nav>
