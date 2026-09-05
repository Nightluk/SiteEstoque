<?php 

    $servidor = "localhost";
    $banco = "db_RegistroProdutos";
    $usuario = "root";
    $senha = "";

    $conexao = new mysqli($servidor, $usuario, $senha, $banco);

    if($conexao->connect_error){
        echo "Erro de conexão!" . $conexao->connect_error;
    }else{
        // echo "conectado";
    }

    

?>