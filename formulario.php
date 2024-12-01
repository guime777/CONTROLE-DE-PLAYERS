<?php
if (isset($_POST['submit'])) {
    include_once('config.php');

    $nome = trim($_POST['nome']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $senha = trim($_POST['senha']);
    $telefone = trim($_POST['telefone']);
    $sexo = $_POST['genero'];
    $data_nasc = $_POST['data_nascimento'];
    $cpf = trim($_POST['cpf']);
    $nick = trim($_POST['nick']);
    $cidade = trim($_POST['cidade']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Email inválido.");
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $conexao->prepare("INSERT INTO usuarios (nome, email, senha, telefone, sexo, data_nasc, cpf, nick, cidade) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $nome, $email, $senha_hash, $telefone, $sexo, $data_nasc, $cpf, $nick, $cidade);

    if ($stmt->execute()) {
        $novo_usuario_id = $stmt->insert_id; 

        session_start();
        $_SESSION['user_id'] = $novo_usuario_id; 
        $_SESSION['user_email'] = $email; 
        $_SESSION['user_nome'] = $nome; 

        header('Location: sistema.php?success=1');
        exit(); 
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }

    $stmt->close();
    $conexao->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario | Usuarios</title>
    <link rel="stylesheet" href="css/formulario.css">
</head>
<body>
    <div class="box">
        <form action="formulario.php" method="POST">
            <fieldset>
                <legend><b>Formulario de Usuarios</b></legend>
                <br>
                <div class="inputBox">
                    <input type="text" name="nome" id="nome" class="inputUser" required>
                    <label for="nome" class="labelInput">Nome Completo</label>
                </div>
                <br>
                <div class="inputBox">
                    <input type="password" name="senha" id="senha" class="inputUser" required>
                    <label for="senha" class="labelInput">Senha</label>
                </div>
                <br><br>
                <div class="inputBox">
                    <input type="text" name="email" id="email" class="inputUser" required>
                    <label for="email" class="labelInput">Email</label>
                </div>
                <br><br>
                <div class="inputBox">
                    <input type="tel" name="telefone" id="telefone" class="inputUser" required>
                    <label for="telefone" class="labelInput">Telefone</label>
                </div>
                <p>Sexo:</p>
                <input type="radio" id="feminino" name="genero" value="feminino" required>
                <label for="feminino">Feminino</label>
                <br>
                <input type="radio" id="masculino" name="genero" value="masculino" required>
                <label for="masculino">Masculino</label>
                <br>
                <input type="radio" id="outro" name="genero" value="outro" required>
                <label for="outro">Outro</label>
                <br><br>
                <label for="data_nascimento"><b>Data de Nascimento:</b></label>
                <input type="date" name="data_nascimento" id="data_nascimento" required>
                <br><br><br>
                <div class="inputBox">
                    <input type="text" name="cpf" id="cpf" class="inputUser" required>
                    <label for="cpf" class="labelInput">CPF</label>
                </div>
                <br><br>
                <div class="inputBox">
                    <input type="text" name="nick" id="nick" class="inputUser" required>
                    <label for="nick" class="labelInput">Nick</label>
                </div>
                <br><br>
                <div class="inputBox">
                    <input type="text" name="cidade" id="cidade" class="inputUser" required>
                    <label for="cidade" class="labelInput">Cidade</label>
                </div>
                <br><br>
                <input type="submit" name="submit" id="submit">
                <a href="home.php"><input class="inputBack" type="button" value="Voltar"></input></a>
            </fieldset>
        </form>
    </div>
</body>
</html>