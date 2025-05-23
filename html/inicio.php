<?php
session_start(); // Inicia a sessão apenas uma vez

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../php/index.php");
    exit();
}

include_once('../php/conexao.php'); // Usa include_once para evitar múltiplas inclusões
include_once('../php/protecao.php'); // Evita redefinição da função protect()
include('../php/menu.php');

$status_message = '';
if (isset($_GET['status']) && $_GET['status'] == 'deleted') {
    $status_message = "Post apagado com sucesso!";
} elseif (isset($_GET['error']) && $_GET['error'] == 'unauthorized') {
    $status_message = "Você não tem permissão para realizar esta ação.";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início</title>
</head>
<body>

    <header>
        <h1>TCCs Postados</h1>

        <?php if (!empty($status_message)): ?>
            <div class="status-message">
                <p><?php echo htmlspecialchars($status_message); ?></p>
            </div>
        <?php endif; ?>
    </header>
    
    <section id="posts">
        <?php include_once('../php/exibir_posts.php'); ?> <!-- Usa include_once para evitar problemas -->
    </section>

</body>
</html>