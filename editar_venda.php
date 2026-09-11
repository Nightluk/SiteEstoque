<?php
include('inc/conexao.php');
include('inc/trava.php');

$mensagem = "";

if (!isset($_GET['cd']) || empty($_GET['cd'])) {
    header('Location: vendas.php');
    exit();
}

$cd_venda = intval($_GET['cd']);

if (isset($_POST['btn_atualizar'])) {
    $produtos_selecionados = $_POST['produtos'] ?? [];
    $quantidades = $_POST['quantidades'] ?? [];

    if (!empty($produtos_selecionados)) {
        $conexao->begin_transaction();

        try {
            $sql_antigos = "SELECT cd_produto, qtd_item FROM tb_Vendas_Itens WHERE cd_venda = ?";
            $stmt_antigos = $conexao->prepare($sql_antigos);
            $stmt_antigos->bind_param("i", $cd_venda);
            $stmt_antigos->execute();
            $res_antigos = $stmt_antigos->get_result();

            while ($antigo = $res_antigos->fetch_assoc()) {
                $sql_restaura = "UPDATE tb_Produtos SET qtd_estoque = qtd_estoque + ? WHERE cd_produto = ?";
                $stmt_restaura = $conexao->prepare($sql_restaura);
                $stmt_restaura->bind_param("ii", $antigo['qtd_item'], $antigo['cd_produto']);
                $stmt_restaura->execute();
            }

            $valido = true;
            $total_venda = 0;
            $itens_processar = [];

            foreach ($produtos_selecionados as $index => $cd_produto) {
                $qtd = intval($quantidades[$index]);
                
                if ($cd_produto > 0 && $qtd > 0) {
                    $sql_p = "SELECT nm_produto, vl_unitario, qtd_estoque FROM tb_Produtos WHERE cd_produto = ?";
                    $stmt_p = $conexao->prepare($sql_p);
                    $stmt_p->bind_param("i", $cd_produto);
                    $stmt_p->execute();
                    $prod = $stmt_p->get_result()->fetch_assoc();

                    if (!$prod || $prod['qtd_estoque'] < $qtd) {
                        $valido = false;
                        $nome_p = $prod ? $prod['nm_produto'] : "Desconhecido";
                        $qtd_disp = $prod ? $prod['qtd_estoque'] : 0;
                        $mensagem = "<div class='alert alert-danger mt-3'>Estoque insuficiente para <strong>{$nome_p}</strong>. Disponível: {$qtd_disp}</div>";
                        break;
                    }

                    $subtotal = $prod['vl_unitario'] * $qtd;
                    $total_venda += $subtotal;

                    $itens_processar[] = [
                        'cd_produto' => $cd_produto,
                        'qtd' => $qtd,
                        'vl_unitario' => $prod['vl_unitario'],
                        'subtotal' => $subtotal
                    ];
                }
            }

            if ($valido && count($itens_processar) > 0) {
                $sql_del_itens = "DELETE FROM tb_Vendas_Itens WHERE cd_venda = ?";
                $stmt_del_itens = $conexao->prepare($sql_del_itens);
                $stmt_del_itens->bind_param("i", $cd_venda);
                $stmt_del_itens->execute();

                $sql_u_venda = "UPDATE tb_Vendas SET vl_total_venda = ? WHERE cd_venda = ?";
                $stmt_u_venda = $conexao->prepare($sql_u_venda);
                $stmt_u_venda->bind_param("di", $total_venda, $cd_venda);
                $stmt_u_venda->execute();

                foreach ($itens_processar as $item) {
                    $sql_ins = "INSERT INTO tb_Vendas_Itens (cd_venda, cd_produto, qtd_item, vl_unitario, vl_subtotal) VALUES (?, ?, ?, ?, ?)";
                    $stmt_ins = $conexao->prepare($sql_ins);
                    $stmt_ins->bind_param("iiidd", $cd_venda, $item['cd_produto'], $item['qtd'], $item['vl_unitario'], $item['subtotal']);
                    $stmt_ins->execute();

                    $sql_baixa = "UPDATE tb_Produtos SET qtd_estoque = qtd_estoque - ? WHERE cd_produto = ?";
                    $stmt_baixa = $conexao->prepare($sql_baixa);
                    $stmt_baixa->bind_param("ii", $item['qtd'], $item['cd_produto']);
                    $stmt_baixa->execute();
                }

                $conexao->commit();
                $mensagem = "<div class='alert alert-success mt-3'>Venda #{$cd_venda} atualizada com sucesso! <a href='vendas.php' class='alert-link'>Voltar para Vendas</a></div>";
            } else {
                $conexao->rollback();
            }

        } catch (Exception $e) {
            $conexao->rollback();
            $mensagem = "<div class='alert alert-danger mt-3'>Erro ao atualizar venda: " . $e->getMessage() . "</div>";
        }
    }
}
$sql_venda = "SELECT * FROM tb_Vendas WHERE cd_venda = ?";
$stmt_v = $conexao->prepare($sql_venda);
$stmt_v->bind_param("i", $cd_venda);
$stmt_v->execute();
$venda = $stmt_v->get_result()->fetch_assoc();

if (!$venda) {
    header('Location: vendas.php');
    exit();
}
$sql_itens_v = "SELECT * FROM tb_Vendas_Itens WHERE cd_venda = ?";
$stmt_i = $conexao->prepare($sql_itens_v);
$stmt_i->bind_param("i", $cd_venda);
$stmt_i->execute();
$itens_atuais = $stmt_i->get_result()->fetch_all(MYSQLI_ASSOC);
$sql_prod = "SELECT cd_produto, nm_produto, vl_unitario, qtd_estoque FROM tb_Produtos ORDER BY nm_produto ASC";
$res_prod = $conexao->query($sql_prod);
$lista_produtos = $res_prod->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Estoque - Editar Venda #<?php echo $cd_venda; ?></title>
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
                    <li class="nav-item"><a class="nav-link" href="compras.php">Compras</a></li>
                    <li class="nav-item"><a class="nav-link active" href="vendas.php">Vendas</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Sair</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title mb-3">Editar Venda #<?php echo $cd_venda; ?></h2>
                        <?php echo $mensagem; ?>

                        <form action="" method="post" id="formVenda">
                            <div id="container-itens">
                                <?php foreach ($itens_atuais as $item_v): ?>
                                    <div class="row g-3 align-items-center mb-3 linha-produto">
                                        <div class="col-md-5">
                                            <label class="form-label">Produto</label>
                                            <select class="form-select select-produto" name="produtos[]" required onchange="calcularTotais()">
                                                <option value="" data-preco="0">Selecione um produto...</option>
                                                <?php foreach ($lista_produtos as $p): ?>
                                                    <option value="<?php echo $p['cd_produto']; ?>" data-preco="<?php echo $p['vl_unitario']; ?>" <?php echo ($p['cd_produto'] == $item_v['cd_produto']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($p['nm_produto']) . " - R$ " . number_format($p['vl_unitario'], 2, ',', '.') . " (Estoque: " . $p['qtd_estoque'] . ")"; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Quantidade</label>
                                            <input type="number" min="1" class="form-control qtd-item" name="quantidades[]" value="<?php echo $item_v['qtd_item']; ?>" required onchange="calcularTotais()" onkeyup="calcularTotais()">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Subtotal</label>
                                            <input type="text" class="form-control subtotal-item" value="R$ <?php echo number_format($item_v['vl_subtotal'], 2, ',', '.'); ?>" readonly>
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-outline-danger mt-4" onclick="removerLinha(this)">X</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <button type="button" class="btn btn-secondary" onclick="adicionarLinha()">+ Adicionar outro produto</button>
                                <h4>Total da Venda: <span id="labelTotal" class="text-success">R$ <?php echo number_format($venda['vl_total_venda'], 2, ',', '.'); ?></span></h4>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" name="btn_atualizar" class="btn btn-primary btn-lg w-50">Salvar Alterações</button>
                                <a href="vendas.php" class="btn btn-secondary btn-lg w-50">Cancelar</a>
                            </div>
                        </form>
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