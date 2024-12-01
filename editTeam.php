<?php


if (!isset($_GET['id'])) {
    header('Location: sistema.php');
    exit;
}


include_once('config.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idEquipe = $_GET['id'];
    $novoNome = $_POST['novoNome'];
    $novaDescricao = $_POST['novaDescricao'];


    $sqlUpdateTeam = "UPDATE teams SET name = ?, description = ? WHERE id = ?";
    $stmtUpdateTeam = $conexao->prepare($sqlUpdateTeam);
    $stmtUpdateTeam->bind_param("ssi", $novoNome, $novaDescricao, $idEquipe);
    $stmtUpdateTeam->execute();


    $sqlDeletePlayers = "DELETE FROM team_players WHERE team_id = ?";
    $stmtDeletePlayers = $conexao->prepare($sqlDeletePlayers);
    $stmtDeletePlayers->bind_param("i", $idEquipe);
    $stmtDeletePlayers->execute();


    if (!empty($_POST['jogadores'])) {
        $jogadores = $_POST['jogadores'];
        foreach ($jogadores as $jogador) {
            $sqlInsertPlayer = "INSERT INTO team_players (team_id, player_id) VALUES (?, ?)";
            $stmtInsertPlayer = $conexao->prepare($sqlInsertPlayer);
            $stmtInsertPlayer->bind_param("ii", $idEquipe, $jogador);
            $stmtInsertPlayer->execute();
        }
    }

    header('Location: sistema.php');
    exit;
}


$idEquipe = $_GET['id'];


$sqlGetTeam = "SELECT * FROM teams WHERE id = ?";
$stmtGetTeam = $conexao->prepare($sqlGetTeam);
$stmtGetTeam->bind_param("i", $idEquipe);
$stmtGetTeam->execute();
$equipe = $stmtGetTeam->get_result()->fetch_assoc();

if (!$equipe) {
    header('Location: sistema.php');
    exit;
}


$sqlGetPlayers = "SELECT tp.player_id FROM team_players tp WHERE tp.team_id = ?";
$stmtGetPlayers = $conexao->prepare($sqlGetPlayers);
$stmtGetPlayers->bind_param("i", $idEquipe);
$stmtGetPlayers->execute();
$resultPlayers = $stmtGetPlayers->get_result();
$jogadoresEquipe = [];
while ($row = $resultPlayers->fetch_assoc()) {
    $jogadoresEquipe[] = $row['player_id'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Equipe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/edit.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Editar Equipe</h2>
        <form method="POST" action="editTeam.php?id=<?php echo htmlspecialchars($idEquipe); ?>">
            <div class="mb-3">
                <label for="novoNome" class="form-label">Nome da Equipe</label>
                <input type="text" class="form-control" id="novoNome" name="novoNome" value="<?php echo htmlspecialchars($equipe['name']); ?>">
            </div>
            <div class="mb-3">
                <label for="novaDescricao" class="form-label">Descrição da Equipe</label>
                <input type="text" class="form-control" id="novaDescricao" name="novaDescricao" value="<?php echo htmlspecialchars($equipe['description']); ?>">
            </div>
            <div class="mb-3">
                <label for="jogadores" class="form-label">Jogadores da Equipe</label>
                <select multiple class="form-select" id="jogadores" name="jogadores[]">
                    <?php $sqlJogadores = "SELECT * FROM players";
                    $resultJogadores = $conexao->query($sqlJogadores);
                    while ($jogador = $resultJogadores->fetch_assoc()) {
                        $selected = in_array($jogador['id'], $jogadoresEquipe) ? 'selected' : '';
                        echo "<option value='{$jogador['id']}' {$selected}>{$jogador['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            <a href="sistema.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
