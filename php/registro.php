<?php
session_start();
include_once("conexao.php");

// Inicializar variáveis para evitar warnings
$nome_input = "";
$email_input = "";
$telefone_input = "";
$tipoUsuario_input = "";
$senha_input = "";
$confirmar_senha_input = "";
$mensagem = "";

// Processar o formulário se enviado via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_input = $_POST['nome'] ?? '';
    $email_input = $_POST['email'] ?? '';
    $telefone_input = $_POST['telefone'] ?? '';
    $tipoUsuario_input = $_POST['tipoUsuario'] ?? '';
    $senha_input = $_POST['senha'] ?? '';
    $confirmar_senha_input = $_POST['confirmar_senha'] ?? '';

    // Verifica se senhas coincidem
    if ($senha_input !== $confirmar_senha_input) {
        $mensagem = "As senhas não coincidem.";
    } else {
        // Verifica se o e-mail já está cadastrado
        $verifica_sql = "SELECT idUsuario FROM Usuario WHERE email = ?";
        $stmt = $conn->prepare($verifica_sql);
        $stmt->bind_param("s", $email_input);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mensagem = "Este e-mail já está cadastrado.";
        } else {
            // Insere o novo usuário
            $senha_hash = password_hash($senha_input, PASSWORD_DEFAULT);
            $sql = "INSERT INTO Usuario (nome, email, telefone, senha, tipoUsuario) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $nome_input, $email_input, $telefone_input, $senha_hash, $tipoUsuario_input);

            if ($stmt->execute()) {
                // Redireciona conforme o tipo
                $idUsuario = $stmt->insert_id;
                $_SESSION['idUsuario'] = $idUsuario;
                $_SESSION['tipoUsuario'] = $tipoUsuario_input;

                if ($tipoUsuario_input === "Aluno" || $tipoUsuario_input === "Ex-aluno") {
                    header("Location: ../html/estudante_cadastro.html");
                } elseif ($tipoUsuario_input === "Mentor") {
                    header("Location: ../html/mentor_cadastro.html");
                } elseif ($tipoUsuario_input === "Patrocinador") {
                    header("Location: ../html/patrocinador_cadastro.html");
                } else {
                    header("Location: ../html/login.html");
                }
                exit();
            } else {
                $mensagem = "Erro ao registrar. Tente novamente.";
            }
        }
    }
}
?>

