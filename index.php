<?php
session_start();
include('inc/conexao.php');

$erro = "";

if (isset($_SESSION['cd_usuario'])) {
    header('Location: home.php');
    exit();
}

if (isset($_POST['btn_login'])) {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT cd_usuario, nm_usuario, senha_usuario FROM tb_Usuarios WHERE email_usuario = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        
        if (md5($senha) === $usuario['senha_usuario']) {
            $_SESSION['cd_usuario'] = $usuario['cd_usuario'];
            $_SESSION['nm_usuario'] = $usuario['nm_usuario'];
            
            header('Location: home.php');
            exit();
        } else {
            $erro = "Senha incorreta!";
        }
    } else {
        $erro = "Usuário não encontrado!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Controle de Estoque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="card shadow-sm" style="width: 100%; max-width: 400px;">
            <div class="card-body p-4">
                <h3 class="card-title text-center mb-4">Acesso ao Sistema</h3>

                <?php if ($erro): ?>
                    <div class="alert alert-danger"><?php echo $erro; ?></div>
                <?php endif; ?>

                <form method="post" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" name="email" id="email" required placeholder="admin@estoque.com">
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" name="senha" id="senha" required placeholder="123">
                    </div>
                    <button type="submit" name="btn_login" class="btn btn-primary w-100">Entrar</button>
                </form>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>