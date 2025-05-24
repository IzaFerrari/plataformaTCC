<?php
session_start();
include 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $stmt = $conn->prepare("SELECT idUsuario, senha, tipoUsuario FROM Usuario WHERE email = ? AND status = 'Ativo'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($senha, $user['senha'])) {
            $_SESSION['idUsuario'] = $user['idUsuario'];
            $_SESSION['tipoUsuario'] = $user['tipoUsuario'];
            
            header("Location: ../php/inicio.php");
            exit();
        } else {
            $erro = "Senha incorreta.";
        }
    } else {
        $erro = "Usuário não encontrado ou inativo.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login Sistema TCCs</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet" />
    <link href="../html/css/estilo.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
    <div class="left">
        <h1>Olá.</h1>
        <p>Seja bem-vindo ao Sistema TCCs.</p>
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Digite seu e-mail:</label>
                <div class="input-container">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="Seu e-mail" required />
                </div>
            </div>
            <div class="form-group">
                <label for="senha">E a sua senha:</label>
                <div class="input-container">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="senha" name="senha" placeholder="Sua senha" required />
                </div>
            </div>
            <button class="login-button" type="submit">
                <i class="fas fa-arrow-circle-right"></i>
            </button>
        </form>
        <div class="links">
        <p><i class="fas fa-user-plus"></i> <a href="registro.php">Não tem uma conta?</a></p>
            <p><i class="fas fa-unlock-alt"></i> Esqueceu a senha?</p>
        </div>
        <?php if (!empty($erro)): ?>
            <p style="color: red; margin-top: 10px;"><?php echo htmlspecialchars($erro); ?></p>
        <?php endif; ?>
    </div>
    <div class="right">
        <div class="icons">
            <i class="fas fa-question-circle fa-2x"></i>
            <i class="fas fa-user fa-2x"></i>
        </div>
        <div class="right-content">
            <p>O Sistema de Divulgação para Trabalhos de Conclusão de Curso (TCCs) visa conectar alunos e ex-alunos do Centro Paula Souza a mentores voluntários e patrocinadores, proporcionando um ambiente de apoio e incentivo ao aprimoramento acadêmico.</p>
        </div>
        <div class="logo">
            TCCs<br /><span style="font-size:clamp(0.8rem, 1vw, 1rem);">Centro Paula Souza</span>
        </div>
    </div>
</body>
</html>
