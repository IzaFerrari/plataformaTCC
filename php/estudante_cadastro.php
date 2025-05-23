<?php
session_start();
include 'conexao.php';

$error_message = '';
$sucesso_message = '';

// Verifica se o usuário está logado
if (!isset($_SESSION['idUsuario'])) {
    header("Location: index.php"); // Página de login
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['curso'], $_POST['anoConclusao'])) {
        $curso = trim($_POST['curso']);
        $anoConclusao = trim($_POST['anoConclusao']);
        $idUsuario = $_SESSION['idUsuario'];

        // Verifica se os campos estão vazios
        if (empty($curso) || empty($anoConclusao)) {
            $error_message = "Todos os campos são obrigatórios!";
        } elseif (!preg_match("/^\d{4}$/", $anoConclusao)) {
            $error_message = "O ano de conclusão deve ser um número de 4 dígitos!";
        } else {
            // Verifica se já existe um cadastro para esse usuário
            $verifica = $conn->prepare("SELECT idUsuario FROM Estudante WHERE idUsuario = ?");
            $verifica->bind_param("i", $idUsuario);
            $verifica->execute();
            $resultado = $verifica->get_result();

            if ($resultado->num_rows > 0) {
                $error_message = "Cadastro de estudante já existe para este usuário.";
            } else {
                // Inserção segura
                $sql = $conn->prepare("INSERT INTO Estudante (idUsuario, curso, anoConclusao) VALUES (?, ?, ?)");
                $sql->bind_param("iss", $idUsuario, $curso, $anoConclusao);

                if ($sql->execute()) {
                    // Redireciona para o index.php (login) após cadastro
                    header("Location: index.php");
                    exit();
                } else {
                    $error_message = "Erro ao cadastrar estudante: " . $conn->error;
                }
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
    <link href="../html/css/estilo.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <div class="left">
        <h1>Cadastro</h1>
        <p>Estudante</p>

        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="curso">Curso:</label>
                <div class="input-container">
                    <i class="fas fa-graduation-cap"></i>
                    <input type="text" id="curso" name="curso" placeholder="Seu curso" required>
                </div>
            </div>

            <div class="form-group">
                <label for="anoConclusao">Ano de conclusão:</label>
                <div class="input-container">
                    <i class="fas fa-calendar-days"></i>
                    <input type="text" id="anoConclusao" name="anoConclusao" placeholder="Digite o ano de conclusão" required>
                </div>
            </div>

            <button class="login-button" type="submit">
                <i class="fas fa-circle-check"></i>
            </button>
        </form>

        <div class="links">
            <p>Cadastrar</p>
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
