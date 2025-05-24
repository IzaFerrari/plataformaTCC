<?php include("conexao.php");
      include('../php/menu.php'); ?>  
<!DOCTYPE html>  
<html lang="pt-br">  
<head>  
    <meta charset="UTF-8">  
    <title>Patrocinadores</title>  
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
        .patrocinador {  
            display: flex;  
            align-items: flex-start;  
            background-color: #fff;  
            border: 1px solid #ddd;  
            padding: 20px;  
            margin-bottom: 20px;  
            border-radius: 8px;  
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);  
        }  
        .patrocinador img {  
            width: 150px;  
            height: 150px;  
            object-fit: cover;  
            border-radius: 8px;  
            margin-right: 20px;  
        }  
        .patrocinador-info {  
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
    <h2>Buscar Patrocinadores por Área de Interesse</h2>  
    <form method="GET" action="">  
        <input type="text" name="busca" placeholder="Digite a área de interesse" value="<?php echo isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : ''; ?>">  
        <input type="submit" value="Buscar">  
    </form>  

    <?php  
    $sql = "SELECT   
                u.nome,  
                u.email,  
                u.telefone,  
                u.foto,  
                p.empresa,  
                p.areaInteresse,
                p.descricaoPerfil
            FROM Usuario u  
            JOIN Patrocinador p ON u.idUsuario = p.idUsuario  
            WHERE u.status = 'Ativo' AND u.tipoUsuario = 'Patrocinador'";  

    if (isset($_GET['busca']) && !empty($_GET['busca'])) {  
        $busca = mysqli_real_escape_string($conn, $_GET['busca']);  
        $sql .= " AND p.areaInteresse LIKE '%$busca%'";  
    }  

    $resultado = mysqli_query($conn, $sql);  

    if (mysqli_num_rows($resultado) > 0) {  
        while ($patro = mysqli_fetch_assoc($resultado)) {  
            echo "<div class='patrocinador'>";  
            
            $foto = !empty($patro['foto']) ? "uploads/" . htmlspecialchars($patro['foto']) : 'imagens/default.png';  
            echo "<img src='" . $foto . "' alt='Foto de " . htmlspecialchars($patro['nome']) . "'>";  

            echo "<div class='patrocinador-info'>";  
            echo "<h3>" . htmlspecialchars($patro['nome']) . "</h3>";  
            echo "<p><strong>Descrição:</strong> " . nl2br(htmlspecialchars($patro['descricaoPerfil'])) . "</p>";  
            echo "<p><strong>Empresa:</strong> " . htmlspecialchars($patro['empresa']) . "</p>";  
            echo "<p><strong>Área de Interesse:</strong> " . htmlspecialchars($patro['areaInteresse']) . "</p>";  
            echo "<p><strong>Email:</strong> " . htmlspecialchars($patro['email']) . "<br>";  
            echo "</div>";  
            echo "</div>";  
        }  
    } else {  
        echo "<p>Nenhum patrocinador encontrado.</p>";  
    }  

    mysqli_close($conn);  
    ?>  
</body>  
</html>
