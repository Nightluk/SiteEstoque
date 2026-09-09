<?php include('inc/conexao.php');
$sql = "SELECT * FROM tb_produtos";
$resultado = $conexao->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Estoque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"> </script>
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
                    <li class="nav-item"><a class="nav-link active" href="estoque.php">Estoque</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h1 class="card-title">Todos os produtos em estoque:</h1>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Preço</th>
                                    <th>Quantidade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($resultado->num_rows > 0) {
                                    while ($produto = $resultado->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $produto['cd_produto'] . "</td>";
                                        echo "<td>" . $produto['nm_produto'] . "</td>";
                                        echo "<td>" . $produto['ds_produto'] . "</td>";
                                        echo "<td>" . $produto['qtd_estoque'] . "</td>";
                                        echo "<td>" . $produto['vl_unitario'] . "</td>";
                                        echo "<td><a class='btn btn-outline-danger' href='excluir.php?cd=" . $produto['cd_produto'] . "'>Excluir</a></td>";
                                        echo "<td><a class='btn btn-outline-warning' href='editar.php?cd=" . $produto['cd_produto'] . "'>Editar</a></td>";
                                        echo "</tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>
