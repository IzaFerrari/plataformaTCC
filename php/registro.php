<?php
session_start();
include_once("conexao.php");

// Inicializar variáveis
$nome_input = "";
$email_input = "";
$telefone_input = "";
$tipoUsuario_input = "";
$senha_input = "";
$confirmar_senha_input = "";
$mensagem = "";

// Processamento do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_input = $_POST['nome'] ?? '';
    $email_input = $_POST['email'] ?? '';
    $telefone_input = $_POST['telefone'] ?? '';
    $tipoUsuario_input = $_POST['tipoUsuario'] ?? '';
    $senha_input = $_POST['senha'] ?? '';
    $confirmar_senha_input = $_POST['confirmar_senha'] ?? '';

    if ($senha_input !== $confirmar_senha_input) {
        $mensagem = "As senhas não coincidem.";
    } else {
        $verifica_sql = "SELECT idUsuario FROM Usuario WHERE email = ?";
        $stmt = $conn->prepare($verifica_sql);
        $stmt->bind_param("s", $email_input);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mensagem = "Este e-mail já está cadastrado.";
        } else {
            $senha_hash = password_hash($senha_input, PASSWORD_DEFAULT);
            $sql = "INSERT INTO Usuario (nome, email, telefone, senha, tipoUsuario) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $nome_input, $email_input, $telefone_input, $senha_hash, $tipoUsuario_input);

            if ($stmt->execute()) {
                $idUsuario = $stmt->insert_id;
                $_SESSION['idUsuario'] = $idUsuario;
                $_SESSION['tipoUsuario'] = $tipoUsuario_input;

                if ($tipoUsuario_input === "Aluno" || $tipoUsuario_input === "Ex-aluno") {
                    header("Location: estudante_cadastro.php");
                } elseif ($tipoUsuario_input === "Mentor") {
                    header("Location: mentor_cadastro.php");
                } elseif ($tipoUsuario_input === "Patrocinador") {
                    header("Location: patrocinador_cadastro.php");
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
    <title>Cadastro - Sistema TCCs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../html/css/estilo.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <div class="left">
        <h1>Cadastro</h1>
        <p>Crie suas credenciais</p>

        <?php if (!empty($mensagem)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($mensagem); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="nome">Digite seu nome:</label>
                <div class="input-container">
                    <i class="fas fa-user"></i>
                    <input type="text" id="nome" name="nome" placeholder="Seu nome" required value="<?php echo htmlspecialchars($nome_input); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="email">Digite seu e-mail:</label>
                <div class="input-container">
                    <i class="fas fa-envelope fa-lg"></i>
                    <input type="email" id="email" name="email" placeholder="Seu e-mail" required value="<?php echo htmlspecialchars($email_input); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="telefone">Digite seu telefone:</label>
                <div class="input-container">
                    <i class="fas fa-phone fa-lg"></i>
                    <input type="text" id="telefone" name="telefone" placeholder="Seu telefone" required value="<?php echo htmlspecialchars($telefone_input); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="perfil">O que você é?</label>
                <div class="custom-select">
                    <div class="select-trigger" id="selectTrigger">
                        <i id="iconeSelecionado" class="fas fa-question-circle"></i>
                        <span id="textoSelecionado">
                            <?php echo $tipoUsuario_input ? htmlspecialchars($tipoUsuario_input) : 'Selecione...'; ?>
                        </span>
                        <span class="seta">&#9662;</span>
                    </div>
                    <div class="options" id="options">
                        <div class="option" data-value="Aluno" data-icon="fas fa-graduation-cap">
                            <i class="fas fa-graduation-cap"></i><span>Aluno</span>
                        </div>
                        <div class="option" data-value="Mentor" data-icon="fas fa-chalkboard-teacher">
                            <i class="fas fa-chalkboard-teacher"></i><span>Mentor</span>
                        </div>
                        <div class="option" data-value="Patrocinador" data-icon="fas fa-hand-holding-usd">
                            <i class="fas fa-hand-holding-usd"></i><span>Patrocinador</span>
                        </div>
                        <div class="option" data-value="Ex-aluno" data-icon="fas fa-user-graduate">
                            <i class="fas fa-user-graduate"></i><span>Ex-aluno</span>
                        </div>
                    </div>
                    <input type="hidden" name="tipoUsuario" id="tipoUsuario" required value="<?php echo htmlspecialchars($tipoUsuario_input); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="senha">E a sua senha:</label>
                <div class="input-container">
                    <i class="fas fa-lock fa-lg"></i>
                    <input type="password" id="senha" name="senha" placeholder="Sua senha" required>
                </div>
            </div>

            <div class="form-group">
                <label for="confirmar_senha">Confirme a sua senha:</label>
                <div class="input-container">
                    <i class="fas fa-lock fa-lg"></i>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirme sua senha" required>
                </div>
            </div>

            <button type="submit" class="login-button">
                <i class="fas fa-circle-check"></i>
            </button>
            <div class="links">
                <p>Cadastrar</p>
            </div>
        </form>
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
        const inputTipoUsuario = document.getElementById('tipoUsuario');

        selectTrigger.addEventListener('click', () => {
            options.style.display = options.style.display === 'block' ? 'none' : 'block';
        });

        const optionItems = document.querySelectorAll('.option');
        optionItems.forEach(item => {
            item.addEventListener('click', () => {
                const texto = item.textContent.trim();
                const iconeClass = item.getAttribute('data-icon');
                const valor = item.getAttribute('data-value');

                textoSelecionado.textContent = texto;
                iconeSelecionado.className = iconeClass;
                inputTipoUsuario.value = valor;

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
