<?php include('inc/conexao.php');
$sql = "SELECT * FROM tb_Produtos";
$resultado = $conexao->query($sql);
include('inc/conexao.php');
include('inc/trava.php');
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Estoque - Consultar Estoque</title>
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
                        <h1 class="card-title mb-4">Todos os produtos em estoque:</h1>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Nome</th>
                                        <th>Descrição</th>
                                        <th>Quantidade</th>
                                        <th>Preço Unitário</th>
                                        <th colspan="2" class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($resultado && $resultado->num_rows > 0) {
                                        while ($produto = $resultado->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $produto['cd_produto'] . "</td>";
                                            echo "<td>" . htmlspecialchars($produto['nm_produto']) . "</td>";
                                            echo "<td>" . htmlspecialchars($produto['ds_produto']) . "</td>";
                                            echo "<td>" . $produto['qtd_estoque'] . "</td>";
                                            echo "<td>R$ " . number_format($produto['vl_unitario'], 2, ',', '.') . "</td>";
                                            echo "<td class='text-center'><a class='btn btn-outline-warning btn-sm' href='editar.php?cd=" . $produto['cd_produto'] . "'>Editar</a></td>";
                                            echo "<td class='text-center'><a class='btn btn-outline-danger btn-sm' href='excluir.php?cd=" . $produto['cd_produto'] . "' onclick=\"return confirm('Tem certeza que deseja excluir este produto?');\">Excluir</a></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7' class='text-center'>Nenhum produto cadastrado no momento.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>
