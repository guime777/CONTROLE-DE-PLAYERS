<?php
session_start(); 

if (isset($_POST["submit"]) && !empty($_POST['email']) && !empty($_POST['senha'])) {
    include_once('config.php'); 

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Formato de email inválido.";
        header('Location: login.php'); 
        exit(); 
    }

    $stmt = $conexao->prepare("SELECT id, senha FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1) {
        $_SESSION['error'] = "Usuário não encontrado.";
        header('Location: login.php'); 
        exit(); 
    } else {
        $usuario = $result->fetch_assoc();
        
        if (password_verify($_POST['senha'], $usuario['senha'])) {
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['email'] = $email; 
            header('Location: sistema.php'); 
            exit(); 
        } else {
            $_SESSION['error'] = "Senha incorreta.";
            header('Location: login.php'); 
            exit(); 
        }
    }
} else {
    $_SESSION['error'] = "Por favor, preencha todos os campos.";
    header('Location: login.php'); 
    exit(); 
}
?>