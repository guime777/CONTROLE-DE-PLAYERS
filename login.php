<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela de Login</title>
    <style>
        body{
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(to right, rgb(20,147,220), rgb(17,54,71));
        }
        div{
            background-color: rgba(0, 0, 0, 0.6);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%,-50%);
            padding: 80px;
            border-radius: 15px;
            color: white;
        }
        input{
            padding: 15px;
            border: none;
            outline: none;
            font-size: 15px;

        }
        .inputSubmit{
            background-color: dodgerblue;
            border: none;
            padding: 15px;
            width: 100%;
            border-radius: 10px;
            color: white;
            font-size: 15px;
            cursor: pointer;

        }

        .inputBack{
            background-color: red;
            margin-top: 10px;
            border: none;
            border-radius: 10px;
            width: 100%;
            color: white;
            font-size: 15px;
            text-align: center;
            cursor: pointer;
        }

        .inputSubmit:hover{
            background-color: deepskyblue;
        }

        .inputBack:hover{
            background-color: #ff6961;
        }
    </style>
</head>
<body>
    <div>
        <h1>Login</h1>
        <form action="testeLogin.php" method="POST">
            <input type="text" name="email" placeholder="Email">
            <br><br>
            <input type="password" name="senha" placeholder="Senha">
            <br><br>
            <input class="inputSubmit" type="submit" name="submit" value="Enviar"><br>
            <a href="home.php"><input class="inputBack" type="button" value="Voltar"></input></a>
        </form>
    </div>
    
</body>
</html>