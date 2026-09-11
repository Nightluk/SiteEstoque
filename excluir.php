<?php
include('inc/conexao.php');
include('inc/trava.php');

if (isset($_GET['cd']) && !empty($_GET['cd'])) {
    $cd_produto = intval($_GET['cd']);

    $sql = "DELETE FROM tb_Produtos WHERE cd_produto = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $cd_produto);

    $stmt->execute();
}

header('Location: estoque.php');
exit();
?>