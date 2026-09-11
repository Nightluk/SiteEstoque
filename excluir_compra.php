<?php
include('inc/conexao.php');
include('inc/trava.php');

if (isset($_GET['cd']) && !empty($_GET['cd'])) {
    $cd_compra = intval($_GET['cd']);

    $conexao->begin_transaction();

    try {
        $sql_itens = "SELECT cd_produto, qtd_item FROM tb_Compras_Itens WHERE cd_compra = ?";
        $stmt_itens = $conexao->prepare($sql_itens);
        $stmt_itens->bind_param("i", $cd_compra);
        $stmt_itens->execute();
        $res_itens = $stmt_itens->get_result();

        while ($item = $res_itens->fetch_assoc()) {
            $sql_estorna = "UPDATE tb_Produtos SET qtd_estoque = qtd_estoque - ? WHERE cd_produto = ?";
            $stmt_estorna = $conexao->prepare($sql_estorna);
            $stmt_estorna->bind_param("ii", $item['qtd_item'], $item['cd_produto']);
            $stmt_estorna->execute();
        }
        $sql_del = "DELETE FROM tb_Compras WHERE cd_compra = ?";
        $stmt_del = $conexao->prepare($sql_del);
        $stmt_del->bind_param("i", $cd_compra);
        $stmt_del->execute();

        $conexao->commit();
    } catch (Exception $e) {
        $conexao->rollback();
    }
}

header('Location: compras.php');
exit();
?>