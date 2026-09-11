<?php 
include('inc/conexao.php');
include('inc/trava.php');

$mensagem = "";

if (isset($_POST['btn_comprar'])) {
    $produtos_selecionados = $_POST['produtos'] ?? [];
    $quantidades = $_POST['quantidades'] ?? [];

    if (!empty($produtos_selecionados)) {
        $valido = true;
        $total_compra = 0;
        $itens_processar = [];

        foreach ($produtos_selecionados as $index => $cd_produto) {
            $qtd = intval($quantidades[$index]);
            
            if ($cd_produto > 0 && $qtd > 0) {
                $sql_p = "SELECT nm_produto, vl_unitario FROM tb_Produtos WHERE cd_produto = ?";
                $stmt_p = $conexao->prepare($sql_p);
                $stmt_p->bind_param("i", $cd_produto);
                $stmt_p->execute();
                $prod = $stmt_p->get_result()->fetch_assoc();

                if ($prod) {
                    $subtotal = $prod['vl_unitario'] * $qtd;
                    $total_compra += $subtotal;

                    $itens_processar[] = [
                        'cd_produto' => $cd_produto,
                        'qtd' => $qtd,
                        'vl_unitario' => $prod['vl_unitario'],
                        'subtotal' => $subtotal
                    ];
                }
            }
        }

        if ($valido && count($itens_processar) > 0) {
            $conexao->begin_transaction();

            try {
                $sql_c = "INSERT INTO tb_Compras (vl_total_compra) VALUES (?)";
                $stmt_c = $conexao->prepare($sql_c);
                $stmt_c->bind_param("d", $total_compra);
                $stmt_c->execute();
                $cd_compra = $conexao->insert_id;

                foreach ($itens_processar as $item) {
                    $sql_item = "INSERT INTO tb_Compras_Itens (cd_compra, cd_produto, qtd_item, vl_unitario, vl_subtotal) VALUES (?, ?, ?, ?, ?)";
                    $stmt_item = $conexao->prepare($sql_item);
                    $stmt_item->bind_param("iiidd", $cd_compra, $item['cd_produto'], $item['qtd'], $item['vl_unitario'], $item['subtotal']);
                    $stmt_item->execute();

                    $sql_entrada = "UPDATE tb_Produtos SET qtd_estoque = qtd_estoque + ? WHERE cd_produto = ?";
                    $stmt_entrada = $conexao->prepare($sql_entrada);
                    $stmt_entrada->bind_param("ii", $item['qtd'], $item['cd_produto']);
                    $stmt_entrada->execute();
                }

                $conexao->commit();
                $mensagem = "<div class='alert alert-success mt-3'>Compra #{$cd_compra} registrada e estoque incrementado! Total: R$ " . number_format($total_compra, 2, ',', '.') . "</div>";
            } catch (Exception $e) {
                $conexao->rollback();
                $mensagem = "<div class='alert alert-danger mt-3'>Erro ao processar compra: " . $e->getMessage() . "</div>";
            }
        }
    } else {
        $mensagem = "<div class='alert alert-warning mt-3'>Adicione pelo menos um produto na compra.</div>";
    }
}

$sql_prod = "SELECT cd_produto, nm_produto, vl_unitario, qtd_estoque FROM tb_Produtos ORDER BY nm_produto ASC";
$res_prod = $conexao->query($sql_prod);
$lista_produtos = [];
while ($p = $res_prod->fetch_assoc()) {
    $lista_produtos[] = $p;
}
$sql_compras = "SELECT c.cd_compra, c.dt_compra, c.vl_total_compra, 
                GROUP_CONCAT(CONCAT(p.nm_produto, ' (x', i.qtd_item, ')') SEPARATOR ', ') AS itens
                FROM tb_Compras c
                JOIN tb_Compras_Itens i ON c.cd_compra = i.cd_compra
                JOIN tb_Produtos p ON i.cd_produto = p.cd_produto
                GROUP BY c.cd_compra
                ORDER BY c.dt_compra DESC";
$res_compras = $conexao->query($sql_compras);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Estoque - Registrar Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <li class="nav-item"><a class="nav-link active" href="compras.php">Compras</a></li>
                    <li class="nav-item"><a class="nav-link" href="vendas.php">Vendas</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Sair</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="row mb-5">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title mb-3">Registrar Compra (Entrada de Estoque)</h2>
                        <?php echo $mensagem; ?>

                        <form action="" method="post" id="formCompra">
                            <div id="container-itens">
                                <div class="row g-3 align-items-center mb-3 linha-produto">
                                    <div class="col-md-5">
                                        <label class="form-label">Produto</label>
                                        <select class="form-select select-produto" name="produtos[]" required onchange="calcularTotais()">
                                            <option value="" data-preco="0">Selecione um produto...</option>
                                            <?php foreach ($lista_produtos as $p): ?>
                                                <option value="<?php echo $p['cd_produto']; ?>" data-preco="<?php echo $p['vl_unitario']; ?>">
                                                    <?php echo htmlspecialchars($p['nm_produto']) . " - R$ " . number_format($p['vl_unitario'], 2, ',', '.') . " (Estoque: " . $p['qtd_estoque'] . ")"; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Quantidade</label>
                                        <input type="number" min="1" class="form-control qtd-item" name="quantidades[]" value="1" required onchange="calcularTotais()" onkeyup="calcularTotais()">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Subtotal</label>
                                        <input type="text" class="form-control subtotal-item" value="R$ 0,00" readonly>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-danger mt-4" onclick="removerLinha(this)">X</button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <button type="button" class="btn btn-secondary" onclick="adicionarLinha()">+ Adicionar outro produto</button>
                                <h4>Total da Compra: <span id="labelTotal" class="text-primary">R$ 0,00</span></h4>
                            </div>

                            <button type="submit" name="btn_comprar" class="btn btn-primary btn-lg mt-3 w-100">Finalizar Compra</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title mb-3">Histórico de Compras</h3>
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th># Compra</th>
                                        <th>Itens</th>
                                        <th>Data</th>
                                        <th>Valor Total</th>
                                        <th colspan="2" class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($res_compras && $res_compras->num_rows > 0): ?>
                                        <?php while ($c = $res_compras->fetch_assoc()): ?>
                                            <tr>
                                                <td>#<?php echo $c['cd_compra']; ?></td>
                                                <td><?php echo htmlspecialchars($c['itens']); ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($c['dt_compra'])); ?></td>
                                                <td>R$ <?php echo number_format($c['vl_total_compra'], 2, ',', '.'); ?></td>
                                                <td class="text-center">
                                                    <a href="editar_compra.php?cd=<?php echo $c['cd_compra']; ?>" class="btn btn-outline-warning btn-sm">Editar</a>
                                                </td>
                                                <td class="text-center">
                                                    <a href="excluir_compra.php?cd=<?php echo $c['cd_compra']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Tem certeza? Isso estornará as quantidades do estoque.');">Excluir</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6" class="text-center">Nenhuma compra registrada até o momento.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<script>
function calcularTotais() {
    let totalGeral = 0;
    const linhas = document.querySelectorAll('.linha-produto');

    linhas.forEach(linha => {
        const select = linha.querySelector('.select-produto');
        const inputQtd = linha.querySelector('.qtd-item');
        const inputSubtotal = linha.querySelector('.subtotal-item');

        const precoUnitario = parseFloat(select.options[select.selectedIndex].getAttribute('data-preco')) || 0;
        const quantidade = parseInt(inputQtd.value) || 0;
        const subtotal = precoUnitario * quantidade;

        inputSubtotal.value = 'R$ ' + subtotal.toFixed(2).replace('.', ',');
        totalGeral += subtotal;
    });

    document.getElementById('labelTotal').innerText = 'R$ ' + totalGeral.toFixed(2).replace('.', ',');
}

function adicionarLinha() {
    const container = document.getElementById('container-itens');
    const primeiraLinha = document.querySelector('.linha-produto');
    const novaLinha = primeiraLinha.cloneNode(true);

    novaLinha.querySelector('.select-produto').selectedIndex = 0;
    novaLinha.querySelector('.qtd-item').value = 1;
    novaLinha.querySelector('.subtotal-item').value = 'R$ 0,00';

    container.appendChild(novaLinha);
    calcularTotais();
}

function removerLinha(botao) {
    const linhas = document.querySelectorAll('.linha-produto');
    if (linhas.length > 1) {
        botao.closest('.linha-produto').remove();
        calcularTotais();
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>