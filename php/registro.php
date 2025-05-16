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
                    header("Location: index.php");
                }
                exit();
            } else {
                $mensagem = "Erro ao registrar. Tente novamente.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
</head>
<body>
    <h1>Cadastro de Usuário</h1>

    <?php if (!empty($mensagem)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($mensagem); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="nome">Nome:</label><br>
        <input type="text" name="nome" id="nome" required value="<?php echo htmlspecialchars($nome_input); ?>"><br><br>

        <label for="email">Email:</label><br>
        <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($email_input); ?>"><br><br>

        <label for="telefone">Telefone:</label><br>
        <input type="text" name="telefone" id="telefone" required value="<?php echo htmlspecialchars($telefone_input); ?>"><br><br>

        <label for="tipoUsuario">Tipo de Usuário:</label><br>
        <select name="tipoUsuario" id="tipoUsuario" required>
            <option value="">Selecione</option>
            <option value="Aluno" <?php if ($tipoUsuario_input === "Aluno") echo "selected"; ?>>Aluno</option>
            <option value="Ex-aluno" <?php if ($tipoUsuario_input === "Ex-aluno") echo "selected"; ?>>Ex-aluno</option>
            <option value="Mentor" <?php if ($tipoUsuario_input === "Mentor") echo "selected"; ?>>Mentor</option>
            <option value="Patrocinador" <?php if ($tipoUsuario_input === "Patrocinador") echo "selected"; ?>>Patrocinador</option>
        </select><br><br>

        <label for="senha">Senha:</label><br>
        <input type="password" name="senha" id="senha" required><br><br>

        <label for="confirmar_senha">Confirmar Senha:</label><br>
        <input type="password" name="confirmar_senha" id="confirmar_senha" required><br><br>

        <input type="submit" value="Cadastrar">
    </form>
</body>
</html>