<?php
if(isset($_POST['submit']))
{
   // print_r('Nome: ' . $_POST['nome']);
    //  print_r('<br>');
    //  print_r('Email: ' . $_POST['email']);
     // print_r('<br>');
    //  print_r('Telefone: ' . $_POST['telefone']);
     // print_r('<br>');
     // print_r('Sexo: ' . $_POST['genero']);
     // print_r('<br>');
    //  print_r('Data de nascimento: ' . $_POST['data_nascimento']);
     // print_r('<br>');
     // print_r('Cidade: ' . $_POST['cidade']);
     // print_r('<br>');
     // print_r('Estado: ' . $_POST['estado']);
    //  print_r('<br>');
    //  print_r('Endereço: ' . $_POST['endereco']);

    include_once('config.php');

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $sexo = $_POST['genero'];
    $data_nasc = $_POST['data_nascimento'];
    $cpf = $_POST['cpf'] ;
    $nick = $_POST['nick'];
    $cidade = $_POST['cidade'];

    $result = mysqli_query($conexao, "INSERT INTO players (nome,email,telefone,sexo,data_nasc,cpf,nick,cidade) 
    VALUES('$nome', '$email','$telefone', '$sexo', '$data_nasc', '$cpf', '$nick', '$cidade')");
    
    header('Location: sistema.php');


}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario | Players </title>
    <link rel="stylesheet" href="css/formularioPlayers.css">
</head>
<body>
    <div class="box">
        <form action="formularioPlayers.php" method="POST">
            <fieldset>
                <legend><b>Formulario de Players</b></legend>
                <br>
                <div class="inputBox">
                    <input type="text" name="nome" id="nome" class="inputUser" required>
                    <label for="name" class="labelInput">Nome Completo</label>
                </div>
                <br>
                
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
                <a href="sistema.php"><input class="inputBack" type="button" value="Voltar"></input></a>
            </fieldset>
        </form>
    </div>
</body>
</html>