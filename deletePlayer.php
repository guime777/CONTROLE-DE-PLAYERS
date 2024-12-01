<?php

if (!empty($_GET['id'])) {
    include_once('config.php');

    $id = $_GET['id'];

   
    $sqlSelect = "SELECT * FROM players WHERE id = $id";
    $result = $conexao->query($sqlSelect);

    if ($result->num_rows > 0) {
        
        $sqlDeleteTeamPlayers = "DELETE FROM team_players WHERE player_id = $id";
        $conexao->query($sqlDeleteTeamPlayers);  

        
        $sqlDeletePlayer = "DELETE FROM players WHERE id = $id";
        $conexao->query($sqlDeletePlayer);  
    }
}

header('Location: sistema.php');
?>
