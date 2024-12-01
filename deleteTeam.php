<?php


if (!isset($_GET['id'])) {
    header('Location: sistema.php');
    exit;
}


include_once('config.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    
}


$idEquipe = $_GET['id'];


$sqlCheckTeam = "SELECT * FROM teams WHERE id = ?";
$stmtCheckTeam = $conexao->prepare($sqlCheckTeam);
$stmtCheckTeam->bind_param("i", $idEquipe);
$stmtCheckTeam->execute();
$resultCheckTeam = $stmtCheckTeam->get_result();


if ($resultCheckTeam->num_rows === 0) {

    header('Location: sistema.php');
    exit;
}


$sqlDeleteTeamPlayers = "DELETE FROM team_players WHERE team_id = ?";
$stmtDeleteTeamPlayers = $conexao->prepare($sqlDeleteTeamPlayers);
$stmtDeleteTeamPlayers->bind_param("i", $idEquipe);
$stmtDeleteTeamPlayers->execute();


$sqlDeleteTeam = "DELETE FROM teams WHERE id = ?";
$stmtDeleteTeam = $conexao->prepare($sqlDeleteTeam);
$stmtDeleteTeam->bind_param("i", $idEquipe);
$stmtDeleteTeam->execute();


header('Location: sistema.php');
exit;
?>
