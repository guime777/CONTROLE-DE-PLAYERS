<?php
if(!empty($_GET['id']))
{
  
    include_once('config.php');

    $id = $_GET['id'];  

    $sqlSelect = "SELECT * FROM players WHERE id=$id";

    $result = $conexao->query($sqlSelect);

    if($result->num_rows > 0)
    {
    while($user_data = mysqli_fetch_assoc($result))
    {
        $nome = $user_data['nome'];
        $email = $user_data['email'];
        $telefone = $user_data['telefone'];
        $sexo = $user_data['sexo'];
        $data_nasc = $user_data['data_nasc'];
        $cpf = $user_data['cpf'] ;
        $nick = $user_data['nick'];
        $cidade = $user_data['cidade'];

    }
 
}
    else
{
header('Location: sistema.php');
}
}
else
{
    header('Location: sistema.php');

}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario | GN </title>
    <link rel="stylesheet" href="css/editPlayer.css">
</head>
<body>
    <div class="box">
        <form action="saveEdit2.php" method="POST">
            <fieldset>
                <legend><b>Editar Players</b></legend>
                <br>
                <div class="inputBox">
                    <input type="text" name="nome" id="nome" class="inputUser"  value="<?php echo $nome ?>" required>
                    <label for="name" class="labelInput">Nome Completo</label>
                </div>
                <br>
                <br><br>
                <div class="inputBox">
                    <input type="text" name="email" id="email" class="inputUser" value="<?php echo $email ?>" required>
                    <label for="email" class="labelInput">Email</label>
                </div>
                <br><br>
                <div class="inputBox">
                    <input type="tel" name="telefone" id="telefone" class="inputUser" value="<?php echo $telefone ?>" required>
                    <label for="telefone" class="labelInput">Telefone</label>
                </div>
                <p>Sexo:</p>
                <input type="radio" id="feminino" name="genero" value="feminino" <?php echo ($sexo == 'feminino') ? 'checked' : '' ?> required>
                <label for="feminino">Feminino</label>
                <br>
                <input type="radio" id="masculino" name="genero" value="masculino" <?php echo ($sexo == 'masculino') ? 'checked' : '' ?> required>
                <label for="masculino">Masculino</label>
                <br>
                <input type="radio" id="outro" name="genero" value="outro" <?php echo ($sexo == 'outro') ? 'checked' : '' ?> required>
                <label for="outro">Outro</label>
                <br><br>
                <label for="data_nascimento"><b>Data de Nascimento:</b></label>
                <input type="date" name="data_nascimento" id="data_nascimento" value="<?php echo $data_nasc ?>" required>
                <br><br><br>
                <div class="inputBox">
                    <input type="text" name="cpf" id="cpf" class="inputUser" value="<?php echo $cpf ?>" required>
                    <label for="cpf" class="labelInput">CPF</label>
                </div>
                <br><br>
                <div class="inputBox">
                    <input type="text" name="nick" id="nick" class="inputUser" value="<?php echo $nick ?>" required>
                    <label for="nick" class="labelInput">Nick</label>
                </div>
                <br><br>
                <div class="inputBox">
                    <input type="text" name="cidade" id="cidade" class="inputUser" value="<?php echo $cidade ?>" required>
                    <label for="cidade" class="labelInput">Cidade</label>
                </div>
                <br><br>
                <input type="hidden" name="id" value="<?php echo $id  ?>">
                <input type="submit" name="update" id="update">
                <a href="sistema.php"><input class="inputBack" type="button" value="Voltar"></input></a>
            </fieldset>
        </form>
    </div>
</body>
</html>