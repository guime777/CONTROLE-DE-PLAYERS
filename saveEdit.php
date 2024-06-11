<?php

include_once('config.php');

if(isset($_POST['update']))
{
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $sexo = $_POST['genero'];
    $data_nasc = $_POST['data_nascimento'];
    $cpf = $_POST['cpf'] ;
    $nick = $_POST['nick'];
    $cidade = $_POST['cidade'];

    $sqlUpdate = "UPDATE usuarios SET nome= '$nome', email= '$email', telefone= '$telefone',sexo= '$sexo', data_nasc= '$data_nasc',cpf= '$cpf', nick= '$nick', cidade= '$cidade'
    WHERE id='$id'";

    $result = $conexao->query($sqlUpdate);
}

header('Location: sistema.php');
?>