<?php
include('inc/conexao.php');
include('inc/trava.php');

$mensagem = "";

if (isset($_GET['cd']) && !empty($_GET['cd'])) {
    $cd_produto = intval($_GET['cd']);

    $sql = "SELECT * FROM tb_Produtos WHERE cd_produto = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $cd_produto);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $produto = $resultado->fetch_assoc();
    } else {
        header('Location: estoque.php');
        exit();
    }
} else {
    header('Location: estoque.php');
    exit();
}

if (isset($_POST['btn_atualizar'])) {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $qtdestoque = $_POST['quantidade'];

    $sql_update = "UPDATE tb_Produtos SET nm_produto = ?, ds_produto = ?, vl_unitario = ?, qtd_estoque = ? WHERE cd_produto = ?";
    $stmt_update = $conexao->prepare($sql_update);
    $stmt_update->bind_param("ssdii", $nome, $descricao, $preco, $qtdestoque, $cd_produto);

    if ($stmt_update->execute()) {
        $mensagem = "<div class='alert alert-success mt-3'>Produto atualizado com sucesso! <a href='estoque.php' class='alert-link'>Voltar ao estoque</a></div>";
        $produto['nm_produto'] = $nome;
        $produto['ds_produto'] = $descricao;
        $produto['vl_unitario'] = $preco;
        $produto['qtd_estoque'] = $qtdestoque;
    } else {
        $mensagem = "<div class='alert alert-danger mt-3'>Erro ao atualizar: " . $stmt_update->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Estoque - Editar Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="home.php">Site Estoque</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="registrarprodutos.php">Registrar Produtos</a></li>
                <li class="nav-item"><a class="nav-link" href="estoque.php">Estoque</a></li>
                <li class="nav-item"><a class="nav-link" href="compras.php">Compras</a></li>
                <li class="nav-item"><a class="nav-link" href="vendas.php">Vendas</a></li>
                <li class="nav-item"><a class="nav-link text-danger ms-2" href="logout.php">Sair</a></li>
            </ul>
        </div>
    </div>
</nav>

    <main class="container py-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h1 class="card-title">Editar produto #<?php echo $produto['cd_produto']; ?></h1>
                        <p class="card-text">Altere as informações necessárias e clique em salvar.</p>

                        <?php echo $mensagem; ?>

                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome do Produto</label>
                                <input type="text" class="form-control" name="nome" value="<?php echo htmlspecialchars($produto['nm_produto']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control" name="descricao" rows="3"><?php echo htmlspecialchars($produto['ds_produto']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="preco" class="form-label">Preço</label>
                                <input type="number" step="0.01" class="form-control" name="preco" value="<?php echo $produto['vl_unitario']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="quantidade" class="form-label">Quantidade</label>
                                <input type="number" class="form-control" name="quantidade" value="<?php echo $produto['qtd_estoque']; ?>" required>
                            </div>
                            <div class="d-flex gap-2">
                                <input type="submit" name="btn_atualizar" class="btn btn-primary" value="Salvar Alterações">
                                <a href="estoque.php" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>