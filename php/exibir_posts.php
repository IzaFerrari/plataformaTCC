<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once('conexao.php');
include_once('protecao.php');
include('../php/menu.php');

if (!isset($_SESSION['idUsuario'])) {
    echo '<p>Você precisa estar logado para ver os posts.</p>';
    exit();
}

$idUsuario = $_SESSION['idUsuario'];

// Buscar tipo de usuário
$tipoUsuario = null;
$sql = "SELECT nome, tipoUsuario FROM usuario WHERE idUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $tipoUsuario = $row['tipoUsuario'];
    $_SESSION['nome'] = $row['nome']; // Salva para comparar com autor do feedback
}

// Mensagem de sucesso
if (isset($_GET['msg'])) {
    echo '<p style="color: green; font-weight: bold;">' . htmlspecialchars($_GET['msg']) . '</p>';
}

// Buscar todos os TCCs
$sql = "SELECT tcc.*, usuario.nome FROM tcc 
        JOIN estudante ON tcc.idAutor = estudante.idEstudante
        JOIN usuario ON estudante.idUsuario = usuario.idUsuario
        ORDER BY tcc.idTCC DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<article>';
        echo '<h2>' . htmlspecialchars($row['titulo']) . '</h2>';
        echo '<p>' . nl2br(htmlspecialchars($row['descricao'])) . '</p>';

        if (!empty($row['link'])) {
            echo '<p><a href="' . htmlspecialchars($row['link']) . '" target="_blank">🔗 Link do Projeto</a></p>';
        }

        echo '<p>Postado por ' . htmlspecialchars($row['nome']) . ' | Status: ' . htmlspecialchars($row['status']) . '</p>';

        // Verifica se o autor é o usuário logado
        $sql_autor = "SELECT idEstudante FROM estudante WHERE idUsuario = ?";
        $stmt_autor = $conn->prepare($sql_autor);
        $stmt_autor->bind_param("i", $idUsuario);
        $stmt_autor->execute();
        $result_autor = $stmt_autor->get_result();

        if ($result_autor && $result_autor->num_rows > 0) {
            $autor = $result_autor->fetch_assoc();
            $idEstudanteLogado = $autor['idEstudante'];

            if ($row['idAutor'] == $idEstudanteLogado) {
                echo '<a href="../php/editar_post.php?id=' . $row['idTCC'] . '" style="margin-left: 10px; color: blue;">Editar</a>';
                echo '<form action="../php/apagar_post.php" method="POST" style="display:inline;">
                        <input type="hidden" name="idTCC" value="' . $row['idTCC'] . '">
                        <button type="submit" style="margin-left: 10px; color: red; background: none; border: none; cursor: pointer;">Excluir</button>
                      </form>';
            }
        }

        // Feedbacks
        echo '<h4>Feedbacks:</h4>';
        $sql_fb = "SELECT f.idFeedback, f.titulo, f.texto, f.data, u.nome
                   FROM feedback f
                   LEFT JOIN mentor m ON f.idMentor = m.idMentor
                   LEFT JOIN patrocinador p ON f.idPatrocinador = p.idPatrocinador
                   LEFT JOIN usuario u ON u.idUsuario = COALESCE(m.idUsuario, p.idUsuario)
                   WHERE f.idTCC = ?
                   ORDER BY f.data DESC";
        $stmt_fb = $conn->prepare($sql_fb);
        $stmt_fb->bind_param("i", $row['idTCC']);
        $stmt_fb->execute();
        $result_fb = $stmt_fb->get_result();

        if ($result_fb->num_rows > 0) {
            while ($fb = $result_fb->fetch_assoc()) {
                echo '<div style="border:1px solid #ccc; padding:5px; margin:5px 0;">';
                echo '<strong>' . htmlspecialchars($fb['titulo']) . '</strong> por ' . htmlspecialchars($fb['nome']) . ' em ' . $fb['data'] . '<br>';
                echo '<p>' . nl2br(htmlspecialchars($fb['texto'])) . '</p>';

                if ($fb['nome'] === $_SESSION['nome']) {
                    echo '<form action="../php/editar_feedback.php" method="GET" style="display:inline;">
                            <input type="hidden" name="id" value="' . $fb['idFeedback'] . '">
                            <button type="submit" style="color:blue;">Editar</button>
                          </form>';
                    echo '<form action="../php/apagar_feedback.php" method="POST" style="display:inline;">
                            <input type="hidden" name="idFeedback" value="' . $fb['idFeedback'] . '">
                            <button type="submit" style="color:red;" onclick="return confirm(\'Tem certeza que deseja excluir este feedback?\')">Excluir</button>
                          </form>';
                }

                echo '</div>';
            }
        } else {
            echo '<p>Nenhum feedback ainda.</p>';
        }

        // Formulário de feedback
        if ($tipoUsuario === 'Mentor' || $tipoUsuario === 'Patrocinador') {
            echo '<details>';
            echo '<summary>Deixar Feedback</summary>';
            echo '<form action="../php/enviar_feedback.php" method="POST">
                    <input type="hidden" name="idTCC" value="' . $row['idTCC'] . '">
                    <label for="titulo">Título:</label>
                    <input type="text" name="titulo" required><br>
                    <label for="texto">Texto:</label>
                    <textarea name="texto" required></textarea><br>
                    <input type="submit" value="Enviar Feedback">
                  </form>';
            echo '</details>';
        }

        echo '</article><hr>';
    }
} else {
    echo '<p>Nenhum post encontrado.</p>';
}

// Formulário de novo post
if ($tipoUsuario === 'Aluno' || $tipoUsuario === 'Ex-aluno') {
    echo '<h2>Publicar Post</h2>';
    echo '<form action="../php/postar_post.php" method="POST">
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" required><br><br>

            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao" required></textarea><br><br>

            <label for="linkProjeto">Link do Projeto (ex: Google Drive, GitHub, etc):</label>
            <input type="url" id="linkProjeto" name="linkProjeto"><br><br>

            <input type="submit" value="Publicar">
          </form>';
}
?>
