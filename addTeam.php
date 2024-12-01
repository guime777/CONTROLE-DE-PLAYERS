<?php
session_start(); // Inicia a sessão
include_once('config.php'); // Inclui o arquivo de configuração do banco de dados

$mensagem = '';
$erro = '';

// Verifique se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redireciona para a página de login
    exit(); // Adicionando exit após o redirecionamento
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $capitao_id = $_SESSION['user_id']; // Obtenha o ID do usuário logado

    // Validação dos campos
    if (empty($name) || empty($description)) {
        $erro = "Por favor, preencha todos os campos obrigatórios.";
    } else {
        // Prepare a consulta SQL para inserir o novo time
        $sql = "INSERT INTO teams (name, description, capitao_id) VALUES (?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssi", $name, $description, $capitao_id);

        if ($stmt->execute()) {
            $mensagem = "Equipe '$name' adicionada com sucesso! Redirecionando...";
            echo "<script>setTimeout(function() { window.location.href = 'sistema.php'; }, 2000);</script>";
        } else {
            $erro = "Erro ao adicionar equipe: " . $stmt->error; // Mensagem de erro se a inserção falhar
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Equipe</title>
    <link rel="stylesheet" href="css/addTeam.css">
    
</head>
<body>
    <h1>Adicionar Nova Equipe</h1>
    <form action="addTeam.php" method="POST">
        <label for="name">Nome da Equipe:</label>
        <input type="text" id="name" name="name" required>
        <br><br>
        <label for="description">Descrição:</label>
        <textarea id="description" name="description" required></textarea>
        <br><br>
        <input type="submit" name="submit" value="Adicionar Equipe">
    </form>

    <?php if ($mensagem): ?>
        <div class="mensagem"><?php echo $mensagem; ?></div>
    <?php endif; ?>
    <?php if ($erro): ?>
        <div class="erro"><?php echo $erro; ?></div>
    <?php endif; ?>
</body>
</html>