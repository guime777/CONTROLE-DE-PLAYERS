<?php
session_start();
include_once('config.php');
require 'vendor/autoload.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$mensagem = '';
$redirecionar = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $valor = $_POST['valor'] * 100; 
    $metodo_pagamento = $_POST['metodo_pagamento'];
    $usuario_id = $_SESSION['user_id'];
    $csrf_token = $_POST['csrf_token'];

    if ($csrf_token !== $_SESSION['csrf_token']) {
        die("Token CSRF inválido.");
    }

    $status_pagamento = 'concluido';

    $sql_insert_pagamento = "INSERT INTO pagamentos (usuario_id, valor, status, metodo_pagamento) VALUES (?, ?, ?, ?)";
    $stmt_insert_pagamento = $conexao->prepare($sql_insert_pagamento);

    $valor_formatado = $valor / 100;

    $stmt_insert_pagamento->bind_param("idss", $usuario_id, $valor_formatado, $status_pagamento, $metodo_pagamento);
    $stmt_insert_pagamento->execute();

    if ($stmt_insert_pagamento->affected_rows > 0) {
        $mensagem = "Pagamento via PIX realizado com sucesso!";
        $payment_id = $stmt_insert_pagamento->insert_id; 

        $sql_insert_transacao = "INSERT INTO transacoes (usuario_id, payment_id, tipo, valor, data, status, descricao) VALUES (?, ?, 'pagamento', ?, NOW(), 'concluido', ?)";
        $stmt_insert_transacao = $conexao->prepare($sql_insert_transacao);
        $descricao = "Pagamento via PIX"; 
        $stmt_insert_transacao->bind_param("iids", $usuario_id, $payment_id, $valor_formatado, $descricao);
        $stmt_insert_transacao->execute();
    } else {
        $mensagem = "Erro ao realizar o pagamento: " . $stmt_insert_pagamento->error;
    }

    $redirecionar = true;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Processar Pagamento via PIX</title>
    <script>
        function redirecionar() {
            setTimeout(function() {
                window.location.href = 'sistema.php'; 
            }, 3000);
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h2>Processar Pagamento via PIX</h2>
        <?php
        if (!empty($mensagem)) {
            echo '<div class="alert alert-info">' . $mensagem . '</div>';
            if ($redirecionar) {
                echo '<script>redirecionar();</script>'; 
            }
        }
        ?>
        <form action="processar_pagamento.php" method="POST">
            <div class="mb-3">
                <label for="valor" class="form-label">Valor:</label>
                <input type="number" step="0.01" name="valor" id="valor" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="metodo_pagamento" class="form-label">Método de Pagamento:</label>
                <select name="metodo_pagamento" id="metodo_pagamento" class="form-select" required>
                    <option value="">Selecione um método</option>
                    <option value="pix">PIX</option>
                </select>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit" class="btn btn-primary">Pagar via PIX</button>
        </form>
    </div>
</body>
</html>