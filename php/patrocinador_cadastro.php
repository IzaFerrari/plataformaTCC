<?php
session_start();
include 'conexao.php';

$error_message = '';
$sucesso_message = '';

if (!isset($_SESSION['idUsuario'])) {
    header("Location: login.php");
    exit();
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['empresa'], $_POST['interesse'], $_POST['descricao'])) {
        $empresa = $conn->real_escape_string($_POST['empresa']);
        $areaInteresse = $conn->real_escape_string($_POST['interesse']);
        $descricaoPerfil = $conn->real_escape_string($_POST['descricao']);
        $idUsuario = $_SESSION['idUsuario'];

        if (empty($empresa) || empty($areaInteresse) || empty($descricaoPerfil)) {
            $error_message = "Todos os campos são obrigatórios!";
        } else {
            $sql_code = $conn->prepare("INSERT INTO Patrocinador (idUsuario, empresa, areaInteresse, descricaoPerfil) VALUES (?, ?, ?, ?)");
            $sql_code->bind_param("isss", $idUsuario, $empresa, $areaInteresse, $descricaoPerfil);

            if ($sql_code->execute()) {
                $sucesso_message = "Cadastro de Patrocinador realizado com sucesso!";
                header("Location: index.php");
                exit();
            } else {
                $error_message = "Erro ao cadastrar Patrocinador: " . $conn->error;
            }
        }
    } else {
        $error_message = "Todos os campos são obrigatórios!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Sistema TCCs</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <link href="../html/css/estilo.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="left">
    <h1>Cadastro</h1>
    <p>Patrocinador</p>

    <?php if (!empty($error_message)): ?>
        <div class="error-message" style="color:red;">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($sucesso_message)): ?>
        <div class="success-message" style="color:green;">
            <?php echo htmlspecialchars($sucesso_message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="empresa">Empresa:</label>
            <div class="input-container">
                <i class="fas fa-building"></i>
                <input type="text" id="empresa" name="empresa" placeholder="Sua empresa" required>
            </div>
        </div>

        <div class="form-group">
            <label for="interesse">Área de Interesse:</label>
            <div class="input-container">
                <i class="fas fa-comment-dots"></i>
                <input type="text" id="interesse" name="interesse" placeholder="Seu interesse" required>
            </div>
        </div>

        <div class="form-group">
            <label for="descricao">Descrição do Perfil:</label>
            <div class="input-container">
                <textarea id="descricao" name="descricao" placeholder="Digite aqui um resumo sobre o seu perfil" required></textarea>
            </div>
        </div>

        <button type="submit" class="login-button">
            <i class="fas fa-circle-check"></i>
        </button>
    </form>

    <div class="links">
        <p><a href="index.php">Voltar para a página inicial</a></p>
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

</body>
</html>
