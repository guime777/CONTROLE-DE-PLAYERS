<?php
session_start();
include_once('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['user_id'];

$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';

$sql_transacoes = "SELECT t.*, p.valor AS valor_pagamento, p.status AS status_pagamento 
                   FROM transacoes t 
                   LEFT JOIN pagamentos p ON t.payment_id = p.id 
                   WHERE t.usuario_id = ?";

$params = [$usuario_id];
$conditions = [];

if ($tipo) {
    $conditions[] = "t.tipo = ?";
    $params[] = $tipo;
}
if ($status) {
    $conditions[] = "t.status = ?";
    $params[] = $status;
}
if ($data_inicio && $data_fim) {
    $conditions[] = "t.data BETWEEN ? AND ?";
    $params[] = $data_inicio;
    $params[] = $data_fim;
}

if (count($conditions) > 0) {
    $sql_transacoes .= " AND " . implode(' AND ', $conditions);
}

$sql_transacoes .= " ORDER BY t.data DESC";

$stmt_transacoes = $conexao->prepare($sql_transacoes);
$stmt_transacoes->bind_param(str_repeat('s', count($params)), ...$params);
$stmt_transacoes->execute();
$result_transacoes = $stmt_transacoes->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Histórico de Transações</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="sistema.php">SISTEMA DE PLAYERS</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="historico_transacoes.php">Histórico de Transações</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sair.php">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Histórico de Transações</h1>

        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <select name="tipo" class="form-select">
                        <option value="">Tipo de Transação</option>
                        <option value="pagamento" <?php if ($tipo == 'pagamento') echo 'selected'; ?>>Pagamento</option>
                        <option value="recebimento" <?php if ($tipo == 'recebimento') echo 'selected'; ?>>Recebimento</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Status</option>
                        <option value="pendente" <?php if ($status == 'pendente') echo 'selected'; ?>>Pendente</option>
                        <option value="concluido" <?php if ($status == 'concluido') echo 'selected'; ?>>Concluído</option>
                        <option value="cancelado" <?php if ($status == 'cancelado') echo 'selected'; ?>>Cancelado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="data_inicio" class="form-control" value="<?php echo $data_inicio; ?>">
                </div>
                <div class="col-md-3">
                    <input type="date" name="data_fim" class="form-control" value="<?php echo $data_fim; ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Filtrar</button>
        </form>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Data</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Valor</th>
                    <th scope="col">Status</th>
                    <th scope="col">Descrição</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_transacoes->num_rows > 0): ?>
                    <?php while ($transacao = $result_transacoes->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($transacao['data'])); ?></td>
                            <td><?php echo htmlspecialchars($transacao['tipo']); ?></td>
                            <td><?php echo htmlspecialchars($transacao['valor_pagamento'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($transacao['status_pagamento'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($transacao['descricao'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Nenhuma transação encontrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>