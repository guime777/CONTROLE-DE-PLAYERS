<?php
session_start();
include_once('config.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$logado = isset($_SESSION['email']) ? $_SESSION['email'] : "E-mail não disponível";

$capitao_id = $_SESSION['user_id'];

$sql_usuarios = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conexao->prepare($sql_usuarios);
$stmt->bind_param("i", $capitao_id);
$stmt->execute();
$result_usuarios = $stmt->get_result();

if ($result_usuarios->num_rows === 0) {
    die("Erro: Capitão não encontrado.");
}

$sql_teams = "SELECT teams.id, teams.name, teams.description, GROUP_CONCAT(players.nome SEPARATOR ', ') as players
FROM teams
LEFT JOIN team_players ON teams.id = team_players.team_id
LEFT JOIN players ON team_players.player_id = players.id
WHERE teams.capitao_id = ?
GROUP BY teams.id, teams.name, teams.description
ORDER BY teams.id DESC";
$stmt_teams = $conexao->prepare($sql_teams);
$stmt_teams->bind_param("i", $capitao_id);
$stmt_teams->execute();
$result_teams = $stmt_teams->get_result();

$sql_players = "SELECT players.*
FROM players
WHERE players.capitao_id = ?
ORDER BY players.id DESC";
$stmt_players = $conexao->prepare($sql_players);
$stmt_players->bind_param("i", $capitao_id);
$stmt_players->execute();
$result_players = $stmt_players->get_result();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add_player') {
        // Obtenha os dados do formulário
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $telefone = trim($_POST['telefone']);
        $sexo = $_POST['sexo'];
        $data_nasc = $_POST['data_nasc'];
        $cpf = trim($_POST['cpf']);
        $nick = trim($_POST['nick']);
        $cidade = trim($_POST['cidade']);
        $csrf_token = $_POST['csrf_token'];
        
        if ($csrf_token !== $_SESSION['csrf_token']) {
            die("Token CSRF inválido.");
        }

        $erros = [];
        if (empty($nome)) {
            $erros[] = "O nome é obrigatório.";
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros[] = "Email inválido.";
        }
        if (empty($telefone)) {
            $erros[] = "O telefone é obrigatório.";
        }
        if (empty($sexo)) {
            $erros[] = "O sexo é obrigatório.";
        }
        if (empty($data_nasc)) {
            $erros[] = "A data de nascimento é obrigatória.";
        }
        if (empty($cpf) || !preg_match('/^\d{11}$/', $cpf)) { // Verifica se o CPF tem 11 dígitos
            $erros[] = "O CPF deve ter 11 dígitos.";
        }
        if (empty($nick)) {
            $erros[] = "O nick é obrigatório.";
        }
        if (empty($cidade)) {
            $erros[] = "A cidade é obrigatória.";
        }

        if (!empty($erros)) {
            foreach ($erros as $erro) {
                echo "<div class='alert alert-danger'>$erro</div>";
            }
        } else {
            $sql_insert = "INSERT INTO players (nome, email, telefone, sexo, data_nasc, cpf, nick, cidade, capitao_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conexao->prepare($sql_insert);
            if ($stmt_insert === false) {
                die("Erro na preparação da consulta: " . $conexao->error);
            }
            $stmt_insert->bind_param("ssssssssi", $nome, $email, $telefone, $sexo, $data_nasc, $cpf, $nick, $cidade, $capitao_id);
            $stmt_insert->execute();

            if ($stmt_insert->affected_rows > 0) {
                $_SESSION['mensagem'] = "Jogador adicionado com sucesso!";
                header('Location: sistema.php');
                exit();
            } else {
                echo "Erro ao cadastrar o jogador: " . $stmt_insert->error;
            }
        }
        
    } elseif ($_POST['action'] == 'add_agendamento') {
        $data_hora = $_POST['data_hora'];
        $descricao = $_POST['descricao'];
        $local = $_POST['local']; 
        $equipe1 = $_POST['equipe1']; 
        $equipe2 = $_POST['equipe2']; 
        $csrf_token = $_POST['csrf_token'];

        if ($csrf_token !== $_SESSION['csrf_token']) {
            die("Token CSRF inválido.");
        }

        if (empty($data_hora) || empty($descricao) || empty($local) || empty($equipe1) || empty($equipe2)) {
            echo "Por favor, preencha todos os campos obrigatórios para o agendamento.";
        } else {
            $sql_insert_agendamento = "INSERT INTO agendamentos (data_hora, descricao, capitao_id, local) VALUES (?, ?, ?, ?)";
            $stmt_insert_agendamento = $conexao->prepare($sql_insert_agendamento);
            $stmt_insert_agendamento->bind_param("ssis", $data_hora, $descricao, $capitao_id, $local);
            $stmt_insert_agendamento->execute();

            if ($stmt_insert_agendamento->affected_rows > 0) {
                $_SESSION['mensagem'] = "Agendamento criado com sucesso!";
                header('Location: sistema.php');
                exit();
            } else {
                echo "Erro ao cadastrar o agendamento: " . $stmt_insert_agendamento->error;
            }
        }
    } elseif ($_POST['action'] == 'atualizar_partida') {
        $partida_id = $_POST['partida_id'];
        $nova_data_hora = $_POST['nova_data_hora'];
        $csrf_token = $_POST['csrf_token'];

        if ($csrf_token !== $_SESSION['csrf_token']) {
            die("Token CSRF inválido.");
        }

        $sql_update_partida = "UPDATE agendamentos SET data_hora = ?, data_hora_atualizacao = NOW() WHERE id = ?";
        $stmt_update = $conexao->prepare($sql_update_partida);
        $stmt_update->bind_param("si", $nova_data_hora, $partida_id);
        $stmt_update->execute();

        if ($stmt_update->affected_rows > 0) {
            $mensagem = "O horário da partida foi alterado para " . date('d/m/Y H:i', strtotime($nova_data_hora));
            $tipo = 'alteracao_horario';

            $sql_notificacao = "INSERT INTO notificacoes (tipo, mensagem) VALUES (?, ?)";
            $stmt_notificacao = $conexao->prepare($sql_notificacao);
            $stmt_notificacao->bind_param("ss", $tipo, $mensagem);
            $stmt_notificacao->execute();
            $notificacao_id = $stmt_notificacao->insert_id;

            $sql_usuarios = "SELECT id, email FROM usuarios"; 
            $result_usuarios = $conexao->query($sql_usuarios);
            while ($row = $result_usuarios->fetch_assoc()) {
                $usuario_id = $row['id'];
                $usuario_email = $row['email'];

                $sql_associacao = "INSERT INTO notificacao_usuarios (notificacao_id, usuario_id) VALUES (?, ?)";
                $stmt_associacao = $conexao->prepare($sql_associacao);
                $stmt_associacao->bind_param("ii", $notificacao_id, $usuario_id);
                $stmt_associacao->execute();

                enviarNotificacaoPorEmail($usuario_email, $mensagem);
            }

            $_SESSION['mensagem'] = "Partida atualizada com sucesso e notificações enviadas!";
            header('Location: sistema.php');
            exit();
        } else {
            echo "Erro ao atualizar a partida: " . $stmt_update->error;
        }
    }
}

$sql_agendamentos = "SELECT * FROM agendamentos WHERE capitao_id = ? ORDER BY data_hora DESC";
$stmt_agendamentos = $conexao->prepare($sql_agendamentos);
$stmt_agendamentos->bind_param("i", $capitao_id);
$stmt_agendamentos->execute();
$result_agendamentos = $stmt_agendamentos->get_result();

if (isset($_GET['delete_agendamento'])) {
    $agendamento_id = $_GET['delete_agendamento'];
    $sql_delete_agendamento = "DELETE FROM agendamentos WHERE id = ? AND capitao_id = ?";
    $stmt_delete_agendamento = $conexao->prepare($sql_delete_agendamento);
    $stmt_delete_agendamento->bind_param("ii", $agendamento_id, $capitao_id);
    $stmt_delete_agendamento->execute();
    if ($stmt_delete_agendamento->affected_rows > 0) {
        $_SESSION['mensagem'] = "Agendamento excluído com sucesso!";
        header('Location: sistema.php');
        exit();
    } else {
        echo "Erro ao excluir o agendamento: " . $stmt_delete_agendamento->error;
    }
}

function enviarNotificacaoPorEmail($usuarioEmail, $mensagem) {
    $assunto = "Notificação de Alteração de Horário";
    $headers = "From: no-reply@seusite.com\r\n";
    mail($usuarioEmail, $assunto, $mensagem, $headers);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <title>SISTEMA | PLAYERS</title>
</head>
<body>
    <div class="pos-f-t">
        <div class="collapse" id="navbarToggleExternalContent">
            <div class="bg-dark p-4">
                <h5 class="text-white h4">Menu de Navegação</h5>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="insertPlayer.php">Adicionar Jogador</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="addTeam.php">Adicionar Equipe</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="addPlayerToTeam.php">Adicionar Jogador à Equipe</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="agendar_partida.php">Agendar Partida</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="processar_pagamento.php">Realizar Pagamento</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="historico_transacoes.php">Histórico de Transações</a>
                    </li>
                </ul>
            </div>
        </div>
        <nav class="navbar navbar-dark bg-primary">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Alterna navegação">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="sistema.php">SISTEMA DE PLAYERS</a>
            <div class="d-flex">
                <a href="sair.php" class="btn btn-danger me-5">Sair</a>
            </div>
        </nav>
    </div>

    <div class="container mt-4">
        <h1>Bem-vindo ao Elite dos Gamers</h1>
        <div class="m-4">
            <h2>Capitão</h2>
            <table class="table text-white table-bg">
                <thead>
                    <tr>
                        <th scope="col">Nome</th>
                        <th scope="col">Email</th>
                        <th scope="col">Telefone</th>
                        <th scope="col">Sexo</th>
                        <th scope="col">Data de Nascimento</th>
                        <th scope="col">CPF</th>
                        <th scope="col">Nick</th>
                        <th scope="col">Cidade</th>
                        <th scope="col">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user_data = mysqli_fetch_assoc($result_usuarios)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user_data['nome']); ?></td>
                            <td><?php echo htmlspecialchars($user_data['email']); ?></td>
                            <td><?php echo htmlspecialchars($user_data['telefone']); ?></td>
                            <td><?php echo htmlspecialchars($user_data['sexo']); ?></td>
                            <td><?php echo htmlspecialchars($user_data['data_nasc']); ?></td>
                            <td><?php echo htmlspecialchars($user_data['cpf']); ?></td>
                            <td><?php echo htmlspecialchars($user_data['nick']); ?></td>
                            <td><?php echo htmlspecialchars($user_data['cidade']); ?></td>
                            <td>
                                <a class='btn btn-sm btn-primary' href='edit.php?id=<?php echo $user_data['id']; ?>' title='Editar'>Editar</a>
                                <a class='btn btn-sm btn-danger' href='delete.php?id=<?php echo $user_data['id']; ?>' title='Deletar'>Deletar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="m-5">
            <h2>Players</h2>
            <table class="table text-white table-bg">
                <thead>
                    <tr>
                        <th scope="col">Nome</th>
                        <th scope="col">Email</th>
                        <th scope="col">Telefone</th>
                        <th scope="col">Sexo</th>
                        <th scope="col">Data de Nascimento</th>
                        <th scope="col">CPF</th>
                        <th scope="col">Nick</th>
                        <th scope="col">Cidade</th>
                        <th scope="col">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($player_data = mysqli_fetch_assoc($result_players)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($player_data['nome']); ?></td>
                            <td><?php echo htmlspecialchars($player_data['email']); ?></td>
                            <td><?php echo htmlspecialchars($player_data['telefone']); ?></td>
                            <td><?php echo htmlspecialchars($player_data['sexo']); ?></td>
                            <td><?php echo htmlspecialchars($player_data['data_nasc']); ?></td>
                            <td><?php echo htmlspecialchars($player_data['cpf']); ?></td>
                            <td><?php echo htmlspecialchars($player_data['nick']); ?></td>
                            <td><?php echo htmlspecialchars($player_data['cidade']); ?></td>
                            <td>
                                <a class='btn btn-sm btn-primary' href='editPlayer.php?id=<?php echo $player_data['id']; ?>' title='Editar'>Editar</a>
                                <a class='btn btn-sm btn-danger' href='deletePlayer.php?id=<?php echo $player_data['id']; ?>' title='Deletar'>Deletar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="m-5">
            <h2>Equipes</h2>
            <table class="table text-white table-bg">
                <thead>
                    <tr>
                        <th scope="col">Nome da Equipe</th>
                        <th scope="col">Descrição</th>
                        <th scope="col">Jogadores</th>
                        <th scope="col">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($team_data = mysqli_fetch_assoc($result_teams)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($team_data['name']); ?></td>
                            <td><?php echo htmlspecialchars($team_data['description']); ?></td>
                            <td>
                                <?php
                                if (isset($team_data['players']) && $team_data['players'] !== null) {
                                    echo htmlspecialchars($team_data['players']);
                                } else {
                                    echo "Nenhum jogador disponível"; 
                                }
                                ?>
                            </td>
                            <td>
                                <a class='btn btn-sm btn-primary' href='editTeam.php?id=<?php echo $team_data['id']; ?>' title='Editar'>Editar</a>
                                <a class='btn btn-sm btn-danger' href='deleteTeam.php?id=<?php echo $team_data['id']; ?>' title='Deletar'>Deletar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div class="m-5">
            <h2>Agendamentos</h2>
            <table class="table text-white table-bg">
                <thead>
                    <tr>
                        <th scope="col">Data e Hora</th>
                        <th scope="col">Descrição</th>
                        <th scope="col">Local</th>
                        <th scope="col">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($agendamento_data = mysqli_fetch_assoc($result_agendamentos)): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($agendamento_data['data_hora'])); ?></td>
                            <td><?php echo htmlspecialchars($agendamento_data['descricao']); ?></td>
                            <td><?php echo htmlspecialchars($agendamento_data['local']); ?></td>
                            <td>
                                <a class='btn btn-sm btn-primary' href='editAgendamento.php?id=<?php echo $agendamento_data['id']; ?>' title='Editar'>Editar</a>
                                <a class='btn btn-sm btn-danger' href='sistema.php?delete_agendamento=<?php echo $agendamento_data['id']; ?>' title='Deletar'>Deletar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="m-5">
            <h2>Notificações</h2>
            <table class="table text-white table-bg">
                <thead>
                    <tr>
                        <th scope="col">Mensagem</th>
                        <th scope="col">Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql_notificacoes = "SELECT n.mensagem, n.data_envio FROM notificacoes n
                                         JOIN notificacao_usuarios nu ON n.id = nu.notificacao_id
                                         WHERE nu.usuario_id = ? AND n.status = 'pendente'";
                    $stmt_notificacoes = $conexao->prepare($sql_notificacoes);
                    $stmt_notificacoes->bind_param("i", $capitao_id);
                    $stmt_notificacoes->execute();
                    $result_notificacoes = $stmt_notificacoes->get_result();

                    $sql_update_status = "UPDATE notificacoes n
                                          JOIN notificacao_usuarios nu ON n.id = nu.notificacao_id
                                          SET n.status = 'lida'
                                          WHERE nu.usuario_id = ?";
                    $stmt_update_status = $conexao->prepare($sql_update_status);
                    $stmt_update_status->bind_param("i", $capitao_id);
                    $stmt_update_status->execute();

                    if ($result_notificacoes->num_rows > 0): ?>
                        <?php while ($notificacao_data = mysqli_fetch_assoc($result_notificacoes)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($notificacao_data['mensagem']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($notificacao_data['data_envio'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">Nenhuma notificação encontrada.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>