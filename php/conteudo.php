<?php
include('conexao.php');
include('protecao.php');

$sql = "SELECT tcc.*, usuarios.nome FROM tcc 
        JOIN estudante ON tcc.idAutor = estudante.idEstudante
        JOIN usuarios ON estudante.idUsuario = usuarios.idUsuario
        ORDER BY tcc.idTCC DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Erro na consulta: " . $conn->error);
}

if ($result->num_rows > 0) {
    while ($post = $result->fetch_assoc()) {
        echo "<article>";
        echo "<h2>" . htmlspecialchars($post['titulo']) . "</h2>";
        echo "<p>" . nl2br(htmlspecialchars($post['descricao'])) . "</p>";

        if (!empty($post['link'])) {
            echo "<p><a href='" . htmlspecialchars($post['link']) . "' target='_blank'>🔗 Ver Projeto</a></p>";
        }

        echo "<p>Postado por: " . htmlspecialchars($post['nome']) . " | Status: " . htmlspecialchars($post['status']) . "</p>"; 
        echo "</article>";
    }
} else {
    echo "<p>Nenhum post encontrado.</p>";
}
?>
