<?php
include_once('config.php');


$sql_teams = "SELECT * FROM teams";
$result_teams = $conexao->query($sql_teams);


$sql_players = "SELECT * FROM players";
$result_players = $conexao->query($sql_players);

$message = ''; 


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $team_id = $_POST['team_id'];
    $player_id = $_POST['player_id'];


    $sql_check = "SELECT * FROM team_players WHERE team_id = '$team_id' AND player_id = '$player_id'";
    $result_check = $conexao->query($sql_check);

    if ($result_check->num_rows > 0) {
        $message = "Este jogador já está na equipe.";
    } else {

        $sql_insert = "INSERT INTO team_players (team_id, player_id) VALUES ('$team_id', '$player_id')";

        if ($conexao->query($sql_insert) === TRUE) {
            $message = "Jogador adicionado à equipe com sucesso!";

            header("Location: sistema.php"); 
            exit();
        } else {
            $message = "Erro ao adicionar jogador à equipe: " . $conexao->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Jogador a Equipe</title>
    <link rel="stylesheet" href="css/addPlayerTeam.css">
</head>
<body>
    <h1>Adicionar Jogador a uma Equipe</h1>
    <form action="addPlayerToTeam.php" method="POST">
        <label for="team">Equipe:</label>
        <select name="team_id" id="team" required>
            <option value="">Selecione a Equipe</option>
            <?php while ($team = $result_teams->fetch_assoc()): ?>
                <option value="<?php echo $team['id']; ?>"><?php echo $team['name']; ?></option>
            <?php endwhile; ?>
        </select>
        <br><br>
        <label for="player">Jogador:</label>
        <select name="player_id" id="player" required>
            <option value="">Selecione o Jogador</option>
            <?php while ($player = $result_players->fetch_assoc()): ?>
                <option value="<?php echo $player['id']; ?>"><?php echo $player['nome']; ?></option>
            <?php endwhile; ?>
        </select>
        <br><br>
        <input type="submit" name="submit" value="Adicionar à Equipe">
    </form>
    <?php if (!empty($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
</body>
</html>
