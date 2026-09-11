<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se não existir a variável de sessão, redireciona para a tela de login
if (!isset($_SESSION['cd_usuario'])) {
    header('Location: index.php');
    exit();
}
?>