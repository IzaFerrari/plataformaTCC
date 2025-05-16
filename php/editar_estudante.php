<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['idUsuario'])) {
    echo "Você precisa estar logado.";
    exit();
}

$idUsuario = $_SESSION['idUsuario'];
$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $curso = $_POST['curso'];
    $anoConclusao = $_POST['anoConclusao'];

    $sql = "UPDATE Estudante SET curso = ?, anoConclusao = ? WHERE idUsuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $curso, $anoConclusao, $idUsuario);

    if ($stmt->execute()) {
        $sucesso = "Informações do estudante atualizadas com sucesso!";
    } else {
        $erro = "Erro ao atualizar: " . $conn->error;
    }
}

// Buscar dados atuais do estudante
$sql = "SELECT curso, anoConclusao FROM Estudante WHERE idUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$dados = $result->fetch_assoc();
?>

<h2>Editar Dados do Estudante</h2>

<?php if ($erro) echo "<p style='color:red;'>$erro</p>"; ?>
<?php if ($sucesso) echo "<p style='color:green;'>$sucesso</p>"; ?>

<form method="POST" action="">
    <label for="curso">Curso:</label>
    <input type="text" id="curso" name="curso" value="<?php echo htmlspecialchars($dados['curso']); ?>" required><br><br>

    <label for="anoConclusao">Ano de Conclusão:</label>
    <input type="text" id="anoConclusao" name="anoConclusao" value="<?php echo htmlspecialchars($dados['anoConclusao']); ?>" required><br><br>

    <input type="submit" value="Salvar Alterações">
</form>
<a href="perfil.php">Voltar para o Perfil</a>