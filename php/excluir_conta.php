<?php
session_start();
include('conexao.php');

$idUsuario = $_SESSION['idUsuario'] ?? null;

if (!$idUsuario) {
    die("Usuário não autenticado.");
}

$mensagemErro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senhaDigitada = $_POST['senha'] ?? '';

    // Verifica a senha do usuário
    $query = "SELECT senha FROM Usuario WHERE idUsuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if ($usuario && password_verify($senhaDigitada, $usuario['senha'])) {
        // Excluir usuário
        $delete = $conn->prepare("DELETE FROM Usuario WHERE idUsuario = ?");
        $delete->bind_param("i", $idUsuario);
        $delete->execute();

        session_destroy();
        header("Location: conta_excluida.php"); // Redireciona para o página de confirmação após excluir
        exit;
    } else {
        $mensagemErro = "Senha incorreta. Por favor, tente novamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir conta - Sistema TCCs</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <link href="../html/css/estilo.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<form method="POST" action="" style="display: flex; width: 100%; height: 100vh;">

    <div class="left">
        <h1>Excluir Conta</h1>
        <p>Digite sua senha para confirmar a exclusão da conta</p>

        <?php if ($mensagemErro): ?>
            <p style="color: red; font-weight: bold;"><?= htmlspecialchars($mensagemErro) ?></p>
        <?php endif; ?>

        <div class="form-group">
            <label for="senha">Senha:</label>
            <div class="input-container">
                <i class="fas fa-lock fa-lg"></i>
                <input type="password" id="senha" name="senha" placeholder="Confirme sua senha" required>
            </div>
        </div>

        <div class="botoes-container">
            <div class="botao-com-texto">
                <button class="login-button" type="submit" style="color: red">
                    <i class="fas fa-circle-xmark"></i>
                </button>
                <p>Excluir conta</p>
            </div>

            <div class="botao-com-texto">
                <button class="login-button" type="button" onclick="window.location.href='perfil.php'">
                    <i class="fas fa-arrow-circle-left"></i>
                </button>
                <p>Cancelar</p>
            </div>
        </div>
    </div>

    <div class="right">
        <div class="icons">
            <i class="fas fa-question-circle fa-2x"></i>
            <i class="fas fa-user fa-2x"></i>
        </div>
        <div class="right-content">
            <p>O Sistema de Divulgação para Trabalhos de Conclusão de Curso (TCCs) visa conectar alunos e ex-alunos do Centro Paula Souza a mentores voluntários e patrocinadores...</p>
        </div>
        <div class="logo">
            TCCs<br><span style="font-size:clamp(0.8rem, 1vw, 1rem);">Centro Paula Souza</span>
        </div>
    </div>

</form>

</body>
</html>
