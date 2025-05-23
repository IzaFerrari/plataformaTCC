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
    $areaEspecialidade = trim($_POST['areaEspecialidade']);
    $descricaoPerfil = trim($_POST['descricaoPerfil']);
    $idUsuario = $_SESSION['idUsuario'];

    // Validação básica
    if (empty($areaEspecialidade) || empty($descricaoPerfil)) {
        $error_message = "Preencha todos os campos!";
    } else {
        // Verifica se já existe cadastro como mentor
        $verifica = $conn->prepare("SELECT idUsuario FROM Mentor WHERE idUsuario = ?");
        $verifica->bind_param("i", $idUsuario);
        $verifica->execute();
        $resultado = $verifica->get_result();

        if ($resultado->num_rows > 0) {
            $error_message = "Você já está cadastrado como mentor.";
        } else {
            // Cadastro com prepared statement
            $stmt = $conn->prepare("INSERT INTO Mentor (idUsuario, areaEspecialidade, descricaoPerfil) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $idUsuario, $areaEspecialidade, $descricaoPerfil);

            if ($stmt->execute()) {
                $sucesso_message = "Cadastro como Mentor realizado com sucesso!";
                // Redireciona após sucesso
                header("Location: index.php");
                exit();
            } else {
                $error_message = "Erro ao cadastrar: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Mentor</title>
    <link rel="stylesheet" href="../html/css/register.css"> <!-- Se tiver um CSS -->
</head>
<body>
    <main>
        <section>
            <h2>Cadastro de Mentor</h2>

            <?php if (!empty($error_message)): ?>
                <div class="error-message" style="color:red;"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <?php if (!empty($sucesso_message)): ?>
                <div class="success-message" style="color:green;"><?php echo htmlspecialchars($sucesso_message); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="input-container">
                    <label for="areaEspecialidade">Área de Especialidade:</label>
                    <input type="text" name="areaEspecialidade" id="areaEspecialidade" required>
                </div>

                <div class="input-container">
                    <label for="descricaoPerfil">Descrição do Perfil:</label>
                    <textarea name="descricaoPerfil" id="descricaoPerfil" required></textarea>
                </div>

                <button type="submit">Cadastrar Mentor</button>
            </form>
        </section>
    </main>
</body>
</html>