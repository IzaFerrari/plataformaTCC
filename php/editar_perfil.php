<?php
session_start();
if (!isset($_SESSION['idUsuario'])) {
    echo "Você precisa estar logado.";
    exit();
}

include_once('conexao.php');
include('../php/menu.php');

$idUsuario = $_SESSION['idUsuario'];

// Buscar dados atuais do usuário
$sql = "SELECT nome, email, senha, telefone, tipoUsuario, foto FROM usuario WHERE idUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// Atualizar dados se o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $tipoUsuario = $_POST['tipoUsuario'];
    $senha_nova = $_POST['senha'];

    // Verifica se uma nova senha foi enviada
    if (!empty($senha_nova)) {
        $senha_final = password_hash($senha_nova, PASSWORD_DEFAULT);
    } else {
        $senha_final = $usuario['senha']; // mantém a senha atual
    }

    // Upload da nova foto (se enviada)
    $foto = $usuario['foto']; // valor anterior
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $novoNome = uniqid() . "." . $ext;

        $pastaFotos = "uploads/fotos/";
        if (!file_exists($pastaFotos)) {
            mkdir($pastaFotos, 0777, true);
        }

        move_uploaded_file($_FILES['foto']['tmp_name'], $pastaFotos . $novoNome);
        $foto = "fotos/" . $novoNome;
    }

    $sql = "UPDATE usuario SET nome = ?, email = ?, senha = ?, telefone = ?, tipoUsuario = ?, foto = ? WHERE idUsuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $nome, $email, $senha_final, $telefone, $tipoUsuario, $foto, $idUsuario);

    if ($stmt->execute()) {
        // Redirecionamento com base no tipo atualizado
        if ($tipoUsuario === "Mentor") {
            header("Location: editar_mentor.php");
        } elseif ($tipoUsuario === "Aluno" || $tipoUsuario === "Ex-aluno") {
            header("Location: editar_estudante.php");
        } elseif ($tipoUsuario === "Patrocinador") {
            header("Location: editar_patrocinador.php");
        } else {
            header("Location: perfil.php?msg=Perfil atualizado");
        }
        exit();
    } else {
        echo "Erro ao atualizar perfil.";
    }
}
?>

<h2>Editar Perfil</h2>
<form action="editar_perfil.php" method="POST" enctype="multipart/form-data">
    <label>Nome:</label>
    <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required><br><br>

    <label>Email:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required><br><br>

    <label>Nova Senha (deixe em branco para manter):</label>
    <input type="password" name="senha"><br><br>

    <label>Telefone:</label>
    <input type="text" name="telefone" value="<?= htmlspecialchars($usuario['telefone']) ?>"><br><br>

    <label>Tipo de Usuário:</label>
    <select name="tipoUsuario" required>
        <option value="Aluno" <?= $usuario['tipoUsuario'] == 'Aluno' ? 'selected' : '' ?>>Aluno</option>
        <option value="Ex-aluno" <?= $usuario['tipoUsuario'] == 'Ex-aluno' ? 'selected' : '' ?>>Ex-aluno</option>
        <option value="Mentor" <?= $usuario['tipoUsuario'] == 'Mentor' ? 'selected' : '' ?>>Mentor</option>
        <option value="Patrocinador" <?= $usuario['tipoUsuario'] == 'Patrocinador' ? 'selected' : '' ?>>Patrocinador</option>
    </select><br><br>

    <label>Foto de Perfil:</label><br>
    <?php if (!empty($usuario['foto'])): ?>
        <img src="uploads/<?= $usuario['foto'] ?>" alt="Foto de perfil" style="max-width: 120px;"><br>
    <?php endif; ?>
    <input type="file" name="foto"><br><br>

    <input type="submit" value="Salvar Alterações">
</form>
