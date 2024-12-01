<?php
include_once('config.php');

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit();
}

$message = ""; 
$successMessage = ""; 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data_hora = isset($_POST['data_hora']) ? $_POST['data_hora'] : null;
    $descricao = isset($_POST['descricao']) ? $_POST['descricao'] : null;
    $local = isset($_POST['local']) ? $_POST['local'] : null;
    $capitao_id = $_SESSION['user_id']; 

  
    if (empty($data_hora) || empty($descricao) || empty($local)) {
        $message = "Por favor, preencha todos os campos obrigatórios.";
    } else {
        $sql = "INSERT INTO agendamentos (data_hora, descricao, capitao_id, local) VALUES (?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssis", $data_hora, $descricao, $capitao_id, $local);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $successMessage = "Agendamento cadastrado com sucesso!";
            header('Location: sistema.php');
            exit();
        } else {
            $message = "Erro ao cadastrar o agendamento: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Agendar Partida</title>
    <link rel="stylesheet" href="css/agendarPartida.css">
</head>
<body>
    <div class="box">
        <h1>Agendar Partida</h1>
        <form method="POST" action="agendar_partida.php">
            <fieldset>
                <legend>Informações do Agendamento</legend>
                <div class="inputBox">
                    <input type="datetime-local" id="data_hora" name="data_hora" class="inputUser" required>
                    <label for="data_hora" class="labelInput">Data e Hora</label>
                </div>

                <div class="inputBox">
                    <input type="text" id="local" name="local" class="inputUser" required>
                    <label for="local" class="labelInput">Local</label>
                </div>

                <div class="inputBox">
                    <input type="text" id="descricao" name="descricao" class="inputUser" required>
                    <label for="descricao" class="labelInput">Descrição</label>
                </div>

                <div class="inputBox">
                    <select id="tipo" name="tipo" class="inputUser" required>
                        <option value="Partida">Partida</option>
                        <option value="Treino">Treino</option>
                    </select>
                    <label for="tipo" class="labelInput">Tipo</label>
                </div>

                <div class="inputBox">
                    <select id="team_id" name="team_id" class="inputUser" required>
                        <?php
                        $sql = "SELECT id, name FROM teams WHERE capitao_id = ?"; 
                        $stmt = $conexao->prepare($sql);
                        $stmt->bind_param("i", $_SESSION['user_id']); 
                        $stmt->execute();
                        $result = $stmt->get_result();

                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                        }
                        ?>
                    </select>
                    <label for="team_id" class="labelInput">Equipe</label>
                </div>

                <button type="submit" id="submit">Agendar</button>
            </fieldset>
        </form>

        <?php if ($successMessage): ?>
            <p class="success-message"><?php echo $successMessage; ?></p>
        <?php endif; ?>

        <?php if ($message): ?>
            <p class="error-message"><?php echo $message; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>