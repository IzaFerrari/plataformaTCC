<?php
session_start();
include_once('conexao.php');

if (!isset($_SESSION['idUsuario'])) {
    echo "Você precisa estar logado.";
    exit();
}

$idUsuario = $_SESSION['idUsuario'];
$msg = "";

// Atualizar dados se o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $areaEspecialidade = $_POST['areaEspecialidade'];
    $descricaoPerfil = $_POST['descricaoPerfil'];

    $sql = "UPDATE mentor SET areaEspecialidade = ?, descricaoPerfil = ? WHERE idUsuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $areaEspecialidade, $descricaoPerfil, $idUsuario);

    if ($stmt->execute()) {
        $msg = "Dados de mentor atualizados com sucesso!";
    } else {
        $msg = "Erro ao atualizar dados do mentor.";
    }
}

// Buscar dados atuais
$sql = "SELECT areaEspecialidade, descricaoPerfil FROM mentor WHERE idUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$mentor = $result->fetch_assoc();
?>

<h2>Editar Dados de Mentor</h2>
<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
<form action="editar_mentor.php" method="POST">
    <label>Área de Especialidade:</label>
    <input type="text" name="areaEspecialidade" value="<?php echo htmlspecialchars($mentor['areaEspecialidade']); ?>" required><br><br>

    <label>Descrição do Perfil:</label><br>
    <textarea name="descricaoPerfil" rows="5" cols="40" required><?php echo htmlspecialchars($mentor['descricaoPerfil']); ?></textarea><br><br>

    <input type="submit" value="Salvar Alterações">
</form>
<a href="perfil.php">Voltar para o Perfil</a>