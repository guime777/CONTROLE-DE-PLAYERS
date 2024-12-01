<?php
session_start();
include_once('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$capitao_id = $_SESSION['user_id'];

function exibirNotificacoes($usuario_id) {
    global $conexao;

    $sql = "SELECT mensagem, data_envio FROM notificacoes WHERE usuario_id = ? ORDER BY data_envio DESC";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($notificacao = $result->fetch_assoc()) {
            echo "<div class='notificacao'>";
            echo "<p>" . htmlspecialchars($notificacao['mensagem']) . "</p>";
            echo "<small>Recebido em: " . date('d/m/Y H:i', strtotime($notificacao['data_envio'])) . "</small>";
            echo "</div>";
        }
    } else {
        echo "<div class='notificacao'>Nenhuma notificação encontrada.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Notificações</title>
    <link rel="stylesheet" href="css/notificacoes.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="sistema.php">SISTEMA DE PLAYERS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <div class="d-flex">
            <a href="sair.php" class="btn btn-danger me-5">Sair</a>
        </div>
    </nav>
    <br>
    <h1>Notificações</h1>
    <div class="container">
        <?php exibirNotificacoes($capitao_id); ?>
    </div>
    <a href="sistema.php" class="btn btn-primary">Voltar para o Sistema</a>
</body>
</html>