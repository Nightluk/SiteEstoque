<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_RegistroProdutos";

$conexao = new mysqli($host, $user, $pass, $db);

if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}
?>
