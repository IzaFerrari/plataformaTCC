<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT idUsuario, senha, tipoUsuario FROM Usuario WHERE email = ? AND status = 'Ativo'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($senha, $user['senha'])) {
            $_SESSION['idUsuario'] = $user['idUsuario'];
            $_SESSION['tipoUsuario'] = $user['tipoUsuario'];
            
            header("Location: ../html/inicio.php");
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
<html>
<head>
    <title>Login</title>
</head>
<body>
    <form method="post" action="">
        <label>Email:</label>
        <input type="email" name="email" required>
        <br>
        <label>Senha:</label>
        <input type="password" name="senha" required>
        <br>
        <button type="submit">Entrar</button>
    </form>
    
    <form action="registro.php" method="get">
        <button type="submit">Registrar</button>
    </form>
    
    <?php if (isset($erro)) { echo "<p style='color: red;'>$erro</p>"; } ?>
</body>
</html>
