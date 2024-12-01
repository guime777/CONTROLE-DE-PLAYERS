<?php
session_start(); 
include_once('config.php'); 

$mensagem = '';
$erro = '';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
    exit(); 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $sexo = $_POST['genero']; 
    $data_nasc = $_POST['data_nascimento'];
    $cpf = trim($_POST['cpf']);
    $nick = trim($_POST['nick']);
    $cidade = trim($_POST['cidade']);
    $capitao_id = $_SESSION['user_id']; 

    if (empty($nome) || empty($email) || empty($telefone) || empty($cpf)) {
        $erro = "Por favor, preencha todos os campos obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Email inválido.";
    } else {
        $sql = "INSERT INTO players (nome, email, telefone, sexo, data_nasc, cpf, nick, cidade, capitao_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        if ($stmt = $conexao->prepare($sql)) {
            $stmt->bind_param("ssssssssi", $nome, $email, $telefone, $sexo, $data_nasc, $cpf, $nick, $cidade, $capitao_id);

            if ($stmt->execute()) {
                $mensagem = "Jogador '$nome' adicionado com sucesso! Redirecionando...";
                echo "<script>setTimeout(function() { window.location.href = 'sistema.php'; }, 2000);</script>";
            } else {
                $erro = "Erro ao adicionar jogador: " . $stmt->error; 
            }

            $stmt->close();
        } else {
            $erro = "Erro ao preparar a consulta: " . $conexao->error; 
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Jogador</title>
    <link rel="stylesheet" href="css/insertPlayer.css">
</head>
<body>
    <div class="box">
        <form action="insertPlayer.php" method="POST">
            <fieldset>
                <legend><b>Adicionar Jogador</b></legend>
                <br>
                <div class="inputBox">
                    <input type="text" name="nome" id="nome" class="inputUser" required>
                    <label for="nome" class="labelInput">Nome Completo</label>
                </div>
                <br>
                <div class="inputBox">
                    <input type="text" name="email" id="email" class="inputUser" required>
                    <label for="email" class="labelInput">Email</label>
                </div>
                <br>
                <div class="inputBox">
                    <input type="tel" name="telefone" id="telefone" class="inputUser" required>
                    <label for="telefone" class="labelInput">Telefone</label>
                </div>
                <p>Sexo:</p>
                <input type="radio" id="feminino" name="genero" value="feminino" required>
                <label for="feminino" style="color: white;">Feminino</label>
                <input type="radio" id="masculino" name="genero" value="masculino" required>
                <label for="masculino" style="color: white;">Masculino</label>
                <input type="radio" id="outro" name="genero" value="outro" required>
                <label for="outro" style="color: white;">Outro</label>
                <br><br>
                <label for="data_nascimento" style="color: white;"><b>Data de Nascimento:</b></label>
                <input type="date" name="data_nascimento" id="data_nascimento" required>
                <br><br>
                <div class="inputBox">
                    <input type="text" name="cpf" id="cpf" class="inputUser" required>
                    <label for="cpf" class="labelInput">CPF</label>
                </div>
                <br>
                <div class="inputBox">
                    <input type="text" name="nick" id="nick" class="inputUser" required>
                    <label for="nick" class="labelInput">Nick</label>
                </div>
                <br>
                <div class="inputBox">
                    <input type="text" name="cidade" id="cidade" class="inputUser" required>
                    <label for="cidade" class="labelInput">Cidade</label>
                </div>
                <br>
                <input type="submit" name="submit" id="submit" value="Adicionar Jogador">
                <a href="sistema.php"><input class="inputBack" type="button" value="Voltar"></input></a>
            </fieldset>
        </form>
    </div>

    <?php if ($mensagem): ?>
        <div class="mensagem"><?php echo $mensagem; ?></div>
    <?php endif; ?>
    <?php if ($erro): ?>
        <div class="erro"><?php echo $erro; ?></div>
    <?php endif; ?>
</body>
</html>