<?php
session_start();
include_once('conexao.php');

if (!isset($_SESSION['idUsuario'])) {
    echo "<p>Você precisa estar logado para excluir sua conta.</p>";
    exit();
}

$idUsuario = $_SESSION['idUsuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senha = $_POST['senha'];

    // Buscar a senha atual do usuário
    $sql = "SELECT senha FROM usuario WHERE idUsuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    // Verifica se a senha está correta (com hash)
    if (password_verify($senha, $usuario['senha'])) {
        // Deleta o usuário
        $delete = "DELETE FROM usuario WHERE idUsuario = ?";
        $stmt_delete = $conn->prepare($delete);
        $stmt_delete->bind_param("i", $idUsuario);

        if ($stmt_delete->execute()) {
            session_destroy();
            header("Location: index.php?msg=Conta excluída com sucesso.");
            exit();
        } else {
            echo "<p>Erro ao excluir conta.</p>";
        }
    } else {
        echo "<p style='color:red;'>Senha incorreta. Tente novamente.</p>";
    }
}
?>

<h2>Excluir Conta</h2>
<p>Digite sua senha para confirmar a exclusão da conta:</p>
<form method="POST" action="excluir_conta.php">
    <label for="senha">Senha:</label>
    <input type="password" name="senha" id="senha" required>
    <br><br>
    <input type="submit" value="Excluir Conta" style="color: black; padding: 8px 16px; border: none; cursor: pointer;">
</form>
<a href="perfil.php" style="display: inline-block; margin-top: 10px;">Cancelar</a>
