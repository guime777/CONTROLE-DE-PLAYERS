<?php
session_start();
include_once('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $agendamento_id = $_GET['id'];

    $sql_agendamento = "SELECT * FROM agendamentos WHERE id = ? AND capitao_id = ?";
    $stmt_agendamento = $conexao->prepare($sql_agendamento);
    $stmt_agendamento->bind_param("ii", $agendamento_id, $_SESSION['user_id']);
    $stmt_agendamento->execute();
    $result_agendamento = $stmt_agendamento->get_result();

    if ($result_agendamento->num_rows === 0) {
        die("Agendamento não encontrado.");
    }

    $agendamento_data = $result_agendamento->fetch_assoc();
} else {
    die("ID do agendamento não fornecido.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'atualizar_agendamento') {
    $nova_data_hora = $_POST['nova_data_hora'];
    $nova_descricao = $_POST['nova_descricao'];
    $novo_local = $_POST['novo_local'];
    $csrf_token = $_POST['csrf_token'];

    if ($csrf_token !== $_SESSION['csrf_token']) {
        die("Token CSRF inválido.");
    }

    $sql_update_agendamento = "UPDATE agendamentos SET data_hora = ?, descricao = ?, local = ? WHERE id = ?";
    $stmt_update = $conexao->prepare($sql_update_agendamento);
    $stmt_update->bind_param("sssi", $nova_data_hora, $nova_descricao, $novo_local, $agendamento_id);
    $stmt_update->execute();

    if ($stmt_update->affected_rows > 0) {
        $mensagem = "O agendamento foi atualizado para " . date('d/m/Y H:i', strtotime($nova_data_hora)) . ".";
        $tipo = 'atualizacao_agendamento';

        $sql_notificacao = "INSERT INTO notificacoes (tipo, mensagem) VALUES (?, ?)";
        $stmt_notificacao = $conexao->prepare($sql_notificacao);
        $stmt_notificacao->bind_param("ss", $tipo, $mensagem);
        $stmt_notificacao->execute();

        if ($stmt_notificacao->affected_rows > 0) {
            $notificacao_id = $stmt_notificacao->insert_id;

            $sql_usuarios = "SELECT id FROM usuarios"; 
            $result_usuarios = $conexao->query($sql_usuarios);
            while ($row = $result_usuarios->fetch_assoc()) {
                $usuario_id = $row['id'];

                $sql_associacao = "INSERT INTO notificacao_usuarios (notificacao_id, usuario_id) VALUES (?, ?)";
                $stmt_associacao = $conexao->prepare($sql_associacao);
                $stmt_associacao->bind_param("ii", $notificacao_id, $usuario_id);
                $stmt_associacao->execute();
            }
        }

        $_SESSION['mensagem'] = "Agendamento atualizado com sucesso e notificações enviadas!";
        header('Location: sistema.php');
        exit();
    } else {
        echo "Erro ao atualizar o agendamento: " . $stmt_update->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <title>Editar Agendamento</title>
</head>
<body>
    <div class="container mt-4">
        <h2>Editar Agendamento</h2>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="atualizar_agendamento">
            <div class="mb-3">
                <label for="nova_data_hora" class="form-label">Nova Data e Hora</label>
                <input type="datetime-local" class="form-control" id="nova_data_hora" name="nova_data_hora" value="<?php echo date('Y-m-d\TH:i', strtotime($agendamento_data['data_hora'])); ?>" required>
            </div>
            <div class="mb-3">
                <label for="nova_descricao" class="form-label">Nova Descrição</label>
                <input type="text" class="form-control" id="nova_descricao" name="nova_descricao" value="<?php echo htmlspecialchars($agendamento_data['descricao']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="novo_local" class="form-label">Novo Local</label>
                <input type="text" class="form-control" id="novo_local" name="novo_local" value="<?php echo htmlspecialchars($agendamento_data['local']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar Agendamento</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>