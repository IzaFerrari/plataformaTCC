<?php
session_start();
include 'conexao.php';

$error_message = '';
$sucesso_message = '';

// Verifica se o usuário está logado
if (!isset($_SESSION['idUsuario'])) {
    header("Location: login.php");
    exit();
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['empresa'], $_POST['areaInteresse'], $_POST['descricaoPerfil'])) {
        $empresa = $conn->real_escape_string($_POST['empresa']);
        $areaInteresse = $conn->real_escape_string($_POST['areaInteresse']);
        $descricaoPerfil = $conn->real_escape_string($_POST['descricaoPerfil']);
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
    <link rel="stylesheet" href="../html/css/register.css">
    <title>Cadastro Patrocinador</title>
</head>
<body>
    <main>
        <section>
            <h2>Cadastro do Patrocinador</h2>

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
                <div class="input-container">
                    <input id="empresa" name="empresa" type="text" placeholder=" " required>
                    <label for="empresa">Empresa</label>
                </div>

                <div class="input-container">
                    <input id="areaInteresse" name="areaInteresse" type="text" placeholder=" " required>
                    <label for="areaInteresse">Área de Interesse</label>
                </div>

                <div class="input-container">
                    <textarea id="descricaoPerfil" name="descricaoPerfil" placeholder=" " required></textarea>
                    <label for="descricaoPerfil">Descrição do Perfil</label>
                </div>

                <button type="submit">Cadastrar Patrocinador</button>
            </form>
        </section>
    </main>
</body>
</html>
