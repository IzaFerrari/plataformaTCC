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
    $areaEspecialidade = trim($_POST['area']);
    $descricaoPerfil = trim($_POST['descricao']);
    $idUsuario = $_SESSION['idUsuario'];

    if (empty($areaEspecialidade) || empty($descricaoPerfil)) {
        $error_message = "Preencha todos os campos!";
    } else {
        $verifica = $conn->prepare("SELECT idUsuario FROM Mentor WHERE idUsuario = ?");
        $verifica->bind_param("i", $idUsuario);
        $verifica->execute();
        $resultado = $verifica->get_result();

        if ($resultado->num_rows > 0) {
            $error_message = "Você já está cadastrado como mentor.";
        } else {
            $stmt = $conn->prepare("INSERT INTO Mentor (idUsuario, areaEspecialidade, descricaoPerfil) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $idUsuario, $areaEspecialidade, $descricaoPerfil);

            if ($stmt->execute()) {
                $sucesso_message = "Cadastro como Mentor realizado com sucesso!";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Sistema TCCs</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <link href="../html/css/estilo.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <div class="left">
        <h1>Cadastro</h1>
        <p>Mentor</p>

        <?php if (!empty($error_message)): ?>
            <div class="error-message" style="color:red;"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if (!empty($sucesso_message)): ?>
            <div class="success-message" style="color:green;"><?php echo htmlspecialchars($sucesso_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="area">Área de Especialidade:</label>
                <div class="input-container">
                    <i class="fas fa-user-graduate"></i>
                    <input type="text" id="area" name="area" placeholder="Sua especialidade" required>
                </div>
            </div>

            <div class="form-group">
                <label for="descricao">Descrição do Perfil:</label>
                <div class="input-container">
                    <textarea id="descricao" name="descricao" placeholder="Digite aqui um resumo sobre o seu perfil" required></textarea>
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

    <script>
        const selectTrigger = document.getElementById('selectTrigger');
        const options = document.getElementById('options');
        const textoSelecionado = document.getElementById('textoSelecionado');
        const iconeSelecionado = document.getElementById('iconeSelecionado');

        selectTrigger?.addEventListener('click', () => {
            options.style.display = options.style.display === 'block' ? 'none' : 'block';
        });

        const optionItems = document.querySelectorAll('.option');
        optionItems.forEach(item => {
            item.addEventListener('click', () => {
                const texto = item.textContent.trim();
                const iconeClass = item.getAttribute('data-icon');

                textoSelecionado.textContent = texto;
                iconeSelecionado.className = iconeClass;
                options.style.display = 'none';
            });
        });

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.custom-select')) {
                options.style.display = 'none';
            }
        });
    </script>

</body>
</html>
