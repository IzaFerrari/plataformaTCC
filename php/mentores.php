<?php include("conexao.php");
      include('../php/menu.php');?>  
<!DOCTYPE html>  
<html lang="pt-br">  
<head>  
    <meta charset="UTF-8">  
    <title>Mentores Voluntários</title>  
    <style>  
        body {  
            font-family: Arial, sans-serif;  
            margin: 30px;  
            background-color: #f9f9f9;  
        }  
        h2 {  
            margin-bottom: 20px;  
        }  

        form {  
            margin-bottom: 30px;  
        }  

        .mentor {  
            display: flex;  
            align-items: flex-start;  
            background-color: #fff;  
            border: 1px solid #ddd;  
            padding: 20px;  
            margin-bottom: 20px;  
            border-radius: 8px;  
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);  
        }  

        .mentor img {  
            width: 150px;  
            height: 150px;  
            object-fit: cover;  
            border-radius: 8px;  
            margin-right: 20px;  
        }  

        .mentor-info {  
            flex: 1;  
        }  

        input[type="text"] {  
            padding: 8px;  
            width: 300px;  
        }  

        input[type="submit"] {  
            padding: 8px 12px;  
        }  
    </style>
</head>  
<body>  
    <h2>Buscar Mentores por Área de Especialidade</h2>  
    <form method="GET" action="">  
        <input type="text" name="busca" placeholder="Digite a área de especialidade" value="<?php echo isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : ''; ?>">  
        <input type="submit" value="Buscar">  
    </form>  

    <?php  
    $sql = "SELECT   
                u.nome,  
                u.email,  
                u.telefone,  
                u.foto,  
                m.areaEspecialidade,  
                m.descricaoPerfil  
            FROM Usuario u  
            JOIN Mentor m ON u.idUsuario = m.idUsuario  
            WHERE u.status = 'Ativo' AND u.tipoUsuario = 'Mentor'";  

    if (isset($_GET['busca']) && !empty($_GET['busca'])) {  
        $busca = mysqli_real_escape_string($conn, $_GET['busca']);  
        $sql .= " AND m.areaEspecialidade LIKE '%$busca%'";  
    }  

    $resultado = mysqli_query($conn, $sql);  

    if (mysqli_num_rows($resultado) > 0) {  
        while ($mentor = mysqli_fetch_assoc($resultado)) {  
            echo "<div class='mentor'>";  
            
            // Exibir imagem ou imagem padrão  
            $foto = !empty($mentor['foto']) ? "uploads/" . htmlspecialchars($mentor['foto']) : 'imagens/default.png';  
            echo "<img src='" . $foto . "' alt='Foto de " . htmlspecialchars($mentor['nome']) . "'>";  

            echo "<div class='mentor-info'>";  
            echo "<h3>" . htmlspecialchars($mentor['nome']) . "</h3>";  
            echo "<p><strong>Descrição:</strong> " . nl2br(htmlspecialchars($mentor['descricaoPerfil'])) . "</p>";  
            echo "<p><strong>Área de Especialidade:</strong> " . htmlspecialchars($mentor['areaEspecialidade']) . "</p>";  
            echo "<p><strong>Email:</strong> " . htmlspecialchars($mentor['email']) . "<br>";  
            echo "<strong>Telefone:</strong> " . htmlspecialchars($mentor['telefone']) . "</p>";  
            echo "</div>";  

            echo "</div>";  
        }  
    } else {  
        echo "<p>Nenhum mentor encontrado.</p>";  
    }  

    mysqli_close($conn);  
    ?>  
</body>  
</html>