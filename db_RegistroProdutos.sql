CREATE DATABASE db_RegistroProdutos;
USE db_RegistroProdutos;

CREATE TABLE tb_Usuarios (
    cd_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nm_usuario VARCHAR(100) NOT NULL,
    email_usuario VARCHAR(100) NOT NULL UNIQUE,
    senha_usuario VARCHAR(255) NOT NULL
);

-- usuário padrão (Login: admin@estoque.com | Senha: 123)
INSERT INTO tb_Usuarios (nm_usuario, email_usuario, senha_usuario) 
VALUES ('Administrador', 'admin@estoque.com', MD5('123'));

CREATE TABLE tb_Produtos (
    cd_produto INT AUTO_INCREMENT PRIMARY KEY,
    nm_produto VARCHAR(100) NOT NULL,
    ds_produto TEXT,
    vl_unitario DOUBLE NOT NULL,
    qtd_estoque INT NOT NULL DEFAULT 0
);
CREATE TABLE tb_Compras (
    cd_compra INT AUTO_INCREMENT PRIMARY KEY,
    dt_compra DATETIME DEFAULT CURRENT_TIMESTAMP,
    vl_total_compra DOUBLE NOT NULL DEFAULT 0
);
CREATE TABLE tb_Compras_Itens (
    cd_item_compra INT AUTO_INCREMENT PRIMARY KEY,
    cd_compra INT NOT NULL,
    cd_produto INT NOT NULL,
    qtd_item INT NOT NULL,
    vl_unitario DOUBLE NOT NULL,
    vl_subtotal DOUBLE NOT NULL,
    FOREIGN KEY (cd_compra) REFERENCES tb_Compras(cd_compra) ON DELETE CASCADE,
    FOREIGN KEY (cd_produto) REFERENCES tb_Produtos(cd_produto) ON DELETE CASCADE
);
CREATE TABLE tb_Vendas (
    cd_venda INT AUTO_INCREMENT PRIMARY KEY,
    dt_venda DATETIME DEFAULT CURRENT_TIMESTAMP,
    vl_total_venda DOUBLE NOT NULL DEFAULT 0
);
CREATE TABLE tb_Vendas_Itens (
    cd_item_venda INT AUTO_INCREMENT PRIMARY KEY,
    cd_venda INT NOT NULL,
    cd_produto INT NOT NULL,
    qtd_item INT NOT NULL,
    vl_unitario DOUBLE NOT NULL,
    vl_subtotal DOUBLE NOT NULL,
    FOREIGN KEY (cd_venda) REFERENCES tb_Vendas(cd_venda) ON DELETE CASCADE,
    FOREIGN KEY (cd_produto) REFERENCES tb_Produtos(cd_produto) ON DELETE CASCADE
);


/* INSERT INTO tb_Produtos (nm_produto, ds_produto, vl_unitario, qtd_estoque) VALUES
('Arroz Tipo 1 5kg', 'Arroz branco tipo 1 pacote 5kg', 29.90, 50),
('Feijão Carioca 1kg', 'Feijão carioca tipo 1 pacote 1kg', 8.50, 80),
('Açúcar Refinado 1kg', 'Açúcar refinado da marca tradicional', 4.20, 60),
('Café Torrado e Moído 500g', 'Café tradicional moído embalagem vácuo', 18.90, 45),
('Óleo de Soja 900ml', 'Óleo vegetal de soja refinado', 6.80, 100),
('Leite Integral 1L', 'Leite UHT integral caixa de 1 litro', 5.49, 120),
('Farinha de Trigo 1kg', 'Farinha de trigo tradicional sem fermento', 5.10, 40),
('Macarrão Espaguete 500g', 'Macarrão de sêmola tipo espaguete', 4.30, 90),
('Molho de Tomate 340g', 'Molho de tomate tradicional sachê', 2.80, 75),
('Sal Refinado 1kg', 'Sal iodado refinado pacote 1kg', 2.50, 30),
('Manteiga com Sal 200g', 'Manteiga de leite de primeira qualidade', 11.90, 35),
('Margarina 500g', 'Margarina vegetal com sal potes de 500g', 7.50, 50),
('Refrigerante Cola 2L', 'Refrigerante sabor cola garrafa PET 2L', 8.99, 60),
('Suco de Laranja 1L', 'Suco de laranja pronto para beber caixa 1L', 9.90, 40),
('Água Mineral Sem Gás 500ml', 'Água mineral natural garrafa 500ml', 2.00, 150),
('Cerveja Pilsen 350ml', 'Cerveja pilsen lata 350ml', 3.99, 200),
('Biscoito Recheado Chocolate 130g', 'Biscoito recheado com creme de chocolate', 3.20, 85),
('Biscoito Cream Cracker 400g', 'Biscoito salgado tipo cream cracker', 5.80, 55),
('Chocolate ao Leite 80g', 'Barra de chocolate ao leite tradicional', 5.50, 70),
('Salgadinho de Milho 100g', 'Salgadinho assado sabor queijo', 6.00, 40),
('Detergente Líquido 500ml', 'Detergente para louças neutro 500ml', 2.49, 110),
('Sabão em Pó 800g', 'Sabão em pó para lavar roupas', 14.90, 65),
('Amaciante de Roupas 2L', 'Amaciante concentrado para roupas 2L', 16.50, 45),
('Desinfetante Pinho 1L', 'Desinfetante com fragrância de pinho', 7.90, 50),
('Água Sanitária 1L', 'Água sanitária para higienização geral', 4.50, 80),
('Esponja de Aço (Pct c/ 8)', 'Palha de aço para limpeza pesada', 5.20, 30),
('Papel Toalha (Pct c/ 2 rolos)', 'Papel toalha folha dupla com 2 rolos', 6.80, 40),
('Saco de Lixo 50L (Pct c/ 10)', 'Saco para lixo reforçado 50 litros', 9.90, 35),
('Lustra Móveis 200ml', 'Lustra móveis com proteção e brilho', 8.30, 25),
('Limpador Multiuso 500ml', 'Limpador de superfícies uso geral', 5.90, 60),
('Sabonete em Barra 90g', 'Sabonete em barra hidratação intensa', 2.30, 140),
('Shampoo 350ml', 'Shampoo hidratação para todos os tipos de cabelo', 15.90, 50),
('Condicionador 350ml', 'Condicionador suave uso diário', 17.50, 45),
('Creme Dental 90g', 'Creme dental proteção anticárie', 4.80, 100),
('Escova de Dentes Média', 'Escova dental cerdas médias com protetor', 7.90, 60),
('Papel Higiênico (Pct c/ 12 rolos)', 'Papel higiênico folha dupla 30 metros', 18.90, 55),
('Desodorante Aerossol 150ml', 'Desodorante antitranspirante 48h', 13.90, 40),
('Fio Dental 50m', 'Fio dental com sabor de menta 50 metros', 9.50, 30),
('Sabonete Líquido 250ml', 'Sabonete líquido para as mãos com frasco dosador', 10.90, 35),
('Algodão em Disco (Pct c/ 50)', 'Algodão macio em discos para uso cosmético', 6.50, 25),
('Presunto Fatiado 200g', 'Presunto cozido fatiado para lanches', 8.90, 30),
('Queijo Mussarela Fatiado 200g', 'Queijo mussarela fatiado fresco', 12.50, 35),
('Iogurte Natural 170g', 'Iogurte natural sem adição de açúcar', 3.80, 45),
('Requeijão Cratoso 200g', 'Requeijão cremoso copo 200g', 7.90, 40),
('Pão de Forma Tradicional 450g', 'Pão de forma fatiado tradicional', 7.49, 50),
('Maionese 500g', 'Maionese cremosa tradicional em pote', 8.20, 65),
('Ketchup 400g', 'Ketchup tradicional sachê ou bisnaga', 9.90, 55),
('Mostarda 200g', 'Mostarda amarela tradicional', 6.40, 40),
('Milho Verde em Conserva 170g', 'Milho verde em conserva lata', 3.90, 70),
('Ervilha em Conserva 170g', 'Ervilha em conserva lata', 3.70, 60);
