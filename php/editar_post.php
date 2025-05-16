<?php
session_start();
include_once('conexao.php');
include_once('protecao.php');
include('../php/menu.php');

if (!isset($_SESSION['idUsuario'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "ID do post não fornecido.";
    exit();
}

$idTCC = intval($_GET['id']);

$sql = "SELECT * FROM tcc WHERE idTCC = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idTCC);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Post não encontrado.";
    exit();
}

$post = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Post</title>
</head>
<body>
    <h1>Editar Post</h1>
    <form action="atualizar_post.php" method="POST">
        <input type="hidden" name="idTCC" value="<?php echo $post['idTCC']; ?>">
        
        <label for="titulo">Título:</label><br>
        <input type="text" name="titulo" value="<?php echo htmlspecialchars($post['titulo']); ?>" required><br><br>
        
        <label for="descricao">Descrição:</label><br>
        <textarea name="descricao" required><?php echo htmlspecialchars($post['descricao']); ?></textarea><br><br>

        <label for="linkProjeto">Link do Projeto (opcional):</label><br>
        <input type="url" name="linkProjeto" value="<?php echo htmlspecialchars($post['link']); ?>"><br><br>

        <input type="submit" value="Atualizar">
    </form>
</body>
</html>
