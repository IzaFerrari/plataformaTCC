<?php
session_start();
include 'conexao.php';

$error_message = '';
$sucesso_message = '';

// Verifica se o usuário está logado
if (!isset($_SESSION['idUsuario'])) {
    header("Location: login.php"); // Redireciona para a página de login caso não esteja logado
    exit();
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['curso'], $_POST['anoConclusao'])) {
        $curso = trim($_POST['curso']);
        $anoConclusao = trim($_POST['anoConclusao']);
        $idUsuario = $_SESSION['idUsuario'];

        // Verifica se os campos estão vazios
        if (empty($curso) || empty($anoConclusao)) {
            $error_message = "Todos os campos são obrigatórios!";
        } elseif (!preg_match("/^\d{4}$/", $anoConclusao)) { // Valida o ano de conclusão
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
                // Inserção segura com prepared statement
                $sql = $conn->prepare("INSERT INTO Estudante (idUsuario, curso, anoConclusao) VALUES (?, ?, ?)");
                $sql->bind_param("iss", $idUsuario, $curso, $anoConclusao);

                if ($sql->execute()) {
                    $sucesso_message = "Cadastro realizado com sucesso!";
                    // Redireciona após sucesso, se preferir
                    // header("Location: index.php");
                    // exit();
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
    <link rel="stylesheet" href="../html/css/register.css">
    <title>Cadastro Estudante</title>
</head>
<body>
    <main>
        <section>
            <h2>Cadastro do Estudante</h2>

            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <?php if (!empty($sucesso_message)): ?>
                <div class="success-message"><?php echo $sucesso_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="input-container">
                    <input id="curso" name="curso" type="text" placeholder=" " required>
                    <label for="curso">Curso</label>
                </div>

                <div class="input-container">
                    <input id="anoConclusao" name="anoConclusao" type="text" placeholder=" " required>
                    <label for="anoConclusao">Ano de Conclusão</label>
                </div>

                <button type="submit">Cadastrar Estudante</button>
            </form>
        </section>
    </main>
</body>
</html>