CREATE DATABASE db_RegistroProdutos;
USE db_RegistroProdutos;

CREATE TABLE tb_Produtos(
cd_produto int NOT NULL auto_increment primary key,
nm_produto varchar(80),
ds_produto varchar(180),
qtd_estoque int,
vl_unitario double
);
