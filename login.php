<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela de Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div>
        <h1>Login</h1>
        <?php
        session_start();
        if (isset($_SESSION['error'])) {
            echo "<div class='error-message'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']); 
        }
        ?>
        <form action="testeLogin.php" method="POST">
            <input type="text" name="email" placeholder="Email" required>
            <br><br>
            <input type="password" name="senha" placeholder="Senha" required>
            <br><br>
            <input class="inputSubmit" type="submit" name="submit" value="Enviar"><br>
            <a href="home.php"><input class="inputBack" type="button" value="Voltar"></input></a>
        </form>
    </div>
</body>
</html>