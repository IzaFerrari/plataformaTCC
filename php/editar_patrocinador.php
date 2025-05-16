<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['idUsuario'])) {
    echo "Você precisa estar logada.";
    exit();
}

$idUsuario = $_SESSION['idUsuario'];
$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empresa = $_POST['empresa'];
    $areaInteresse = $_POST['areaInteresse'];
    $descricaoPerfil = $_POST['descricaoPerfil'];

    $sql = "UPDATE Patrocinador SET empresa = ?, areaInteresse = ?, descricaoPerfil = ? WHERE idUsuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $empresa, $areaInteresse, $descricaoPerfil, $idUsuario);

    if ($stmt->execute()) {
        $sucesso = "Informações do patrocinador atualizadas com sucesso!";
    } else {
        $erro = "Erro ao atualizar: " . $conn->error;
    }
}

// Buscar dados atuais do patrocinador
$sql = "SELECT empresa, areaInteresse, descricaoPerfil FROM Patrocinador WHERE idUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$dados = $result->fetch_assoc();
?>

<h2>Editar Dados do Patrocinador</h2>

<?php if ($erro) echo "<p style='color:red;'>$erro</p>"; ?>
<?php if ($sucesso) echo "<p style='color:green;'>$sucesso</p>"; ?>

<form method="POST" action="">
    <label for="empresa">Empresa:</label>
    <input type="text" id="empresa" name="empresa" value="<?php echo htmlspecialchars($dados['empresa']); ?>" required><br><br>

    <label for="areaInteresse">Área de Interesse:</label>
    <input type="text" id="areaInteresse" name="areaInteresse" value="<?php echo htmlspecialchars($dados['areaInteresse']); ?>" required><br><br>

    <label for="descricaoPerfil">Descrição do Perfil:</label><br>
    <textarea id="descricaoPerfil" name="descricaoPerfil" rows="4" cols="50" required><?php echo htmlspecialchars($dados['descricaoPerfil']); ?></textarea><br><br>

    <input type="submit" value="Salvar Alterações">
</form>
<a href="perfil.php">Voltar para o Perfil</a>
