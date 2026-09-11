<?php 
include('inc/conexao.php'); 
include('inc/trava.php');
$sql = "SELECT p.nm_produto, COALESCE(SUM(vi.qtd_item), 0) AS total_vendido 
        FROM tb_Produtos p 
        LEFT JOIN tb_Vendas_Itens vi ON p.cd_produto = vi.cd_produto 
        GROUP BY p.cd_produto, p.nm_produto 
        ORDER BY total_vendido DESC 
        LIMIT 10";

$resultado = $conexao->query($sql);

$nomes_produtos = [];
$quantidades_vendidas = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $nomes_produtos[] = $row['nm_produto'];
        $quantidades_vendidas[] = (int)$row['total_vendido'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Estoque - Home</title>
    <!-- CDN Universal do Chart.js UMD -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <h2 class="card-title text-center mb-4">Relatório de Desempenho de Vendas</h2>
                        <div style="max-width: 800px; margin: 0 auto; position: relative; min-height: 300px;">
                            <canvas id="myChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<script>
window.addEventListener('DOMContentLoaded', () => {
    const xValues = <?php echo json_encode($nomes_produtos); ?>;
    const yValues = <?php echo json_encode($quantidades_vendidas); ?>;

    console.log("Produtos:", xValues);
    console.log("Vendas:", yValues);

    const ctx = document.getElementById('myChart');

    if (typeof Chart === 'undefined') {
        console.error("Biblioteca Chart.js não foi carregada corretamente.");
        return;
    }

    new Chart(ctx, {
        type: "bar",
        data: {
            labels: xValues,
            datasets: [{
                label: "Quantidade Vendida",
                backgroundColor: "rgba(13, 110, 253, 0.7)",
                borderColor: "rgba(13, 110, 253, 1)",
                borderWidth: 1,
                data: yValues
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: true },
                title: {
                    display: true,
                    text: "Top 10 Produtos Mais Vendidos",
                    font: { size: 18 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });
});
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
