<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    echo "<p>Você precisa estar logado para acessar esta página.</p>";
    exit();
}

include_once('conexao.php');
include('../php/menu.php');

$idUsuario = $_SESSION['idUsuario'];

$sql = "SELECT nome, email, tipoUsuario, foto FROM usuario WHERE idUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    echo "<h1>Meu Perfil</h1>";

    // Exibir a foto, caso exista
    if (!empty($usuario['foto'])) {
        $foto_path = "uploads/" . htmlspecialchars($usuario['foto']);
        // Verificar se o arquivo da foto realmente existe
        if (file_exists($foto_path)) {
            echo "<p> <img src='" . $foto_path . "' alt='Foto de Perfil' style='max-width: 150px; max-height: 150px; width: auto; height: auto;'></p>";
        } else {
            echo "<p><strong>Foto:</strong> <em>Foto não encontrada.</em></p>";
        }
    }

    echo "<p><strong>Nome:</strong> " . htmlspecialchars($usuario['nome']) . "</p>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($usuario['email']) . "</p>";
    echo "<p><strong>Tipo de Usuário:</strong> " . htmlspecialchars($usuario['tipoUsuario']) . "</p>";

    // Exibir informações específicas de estudante, mentor ou patrocinador
    if ($usuario['tipoUsuario'] == 'Aluno' || $usuario['tipoUsuario'] == 'Ex-aluno') {
        $sql_estudante = "SELECT * FROM estudante WHERE idUsuario = ?";
        $stmt_estudante = $conn->prepare($sql_estudante);
        $stmt_estudante->bind_param("i", $idUsuario);
        $stmt_estudante->execute();
        $result_estudante = $stmt_estudante->get_result();

        if ($result_estudante->num_rows > 0) {
            $estudante = $result_estudante->fetch_assoc();
            echo "<p><strong>Curso:</strong> " . htmlspecialchars($estudante['curso']) . "</p>";
            echo "<p><strong>Ano de Conclusão:</strong> " . htmlspecialchars($estudante['anoConclusao']) . "</p>";
            echo "<p><strong>Status:</strong> " . htmlspecialchars($estudante['status']) . "</p>";
        }
    } elseif ($usuario['tipoUsuario'] == 'Mentor') {
        $sql_mentor = "SELECT * FROM mentor WHERE idUsuario = ?";
        $stmt_mentor = $conn->prepare($sql_mentor);
        $stmt_mentor->bind_param("i", $idUsuario);
        $stmt_mentor->execute();
        $result_mentor = $stmt_mentor->get_result();

        if ($result_mentor->num_rows > 0) {
            $mentor = $result_mentor->fetch_assoc();
            echo "<p><strong>Área de Especialidade:</strong> " . htmlspecialchars($mentor['areaEspecialidade']) . "</p>";
            echo "<p><strong>Descrição do Perfil:</strong> " . nl2br(htmlspecialchars($mentor['descricaoPerfil'])) . "</p>";
        }
    } elseif ($usuario['tipoUsuario'] == 'Patrocinador') {
        $sql_patrocinador = "SELECT * FROM patrocinador WHERE idUsuario = ?";
        $stmt_patrocinador = $conn->prepare($sql_patrocinador);
        $stmt_patrocinador->bind_param("i", $idUsuario);
        $stmt_patrocinador->execute();
        $result_patrocinador = $stmt_patrocinador->get_result();

        if ($result_patrocinador->num_rows > 0) {
            $patrocinador = $result_patrocinador->fetch_assoc();
            echo "<p><strong>Empresa:</strong> " . htmlspecialchars($patrocinador['empresa']) . "</p>";
            echo "<p><strong>Área de Interesse:</strong> " . htmlspecialchars($patrocinador['areaInteresse']) . "</p>";
        }
    }

    echo '<a href="editar_perfil.php" style="margin-right: 10px; color: blue;">✏ Editar Perfil</a>';
    echo '<a href="excluir_conta.php" style="color: red;" onclick="return confirm(\'Tem certeza que deseja excluir sua conta?\');">🗑 Excluir Conta</a>';

} else {
    echo "<p>Usuário não encontrado.</p>";
}
?>