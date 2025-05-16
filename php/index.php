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
            
            echo "Login bem-sucedido!";
        } else {
            echo "Erro: Senha incorreta.";
        }
    } else {
        echo "Erro: Usuário não encontrado ou inativo.";
    }
}
?>
