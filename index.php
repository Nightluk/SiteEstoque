<?php include('inc/conexao.php'); ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Site Estoque</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">
        
    <style>
        body {
            background-color: #808080;
        }

        .login-box {
            width: 400px;
            background-color: white;
            padding: 35px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .login-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .btn-login {
            width: 100%;
        }

        .footer {
            text-align: center;
            color: #777;
            font-size: 14px;
            margin-top: 25px;
        }
    </style>
</head>

<body>

    <main class="container min-vh-100 d-flex align-items-center justify-content-center">

        <div class="login-box">

            <h2 class="login-title">Controle de Estoque</h2>

            <p class="text-center text-muted mb-4">
                Faça login para continuar
            </p>

            <form action="home.php" method="POST">

                <div class="mb-3">
                    <label for="email" class="form-label">
                        E-mail
                    </label>

                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        placeholder="Digite seu e-mail"
                        required>
                </div>

                <div class="mb-3">
                    <label for="senha" class="form-label">
                        Senha
                    </label>

                    <input
                        type="password"
                        class="form-control"
                        id="senha"
                        name="senha"
                        placeholder="Digite sua senha"
                        required>
                </div>


                <button
                    type="submit"
                    class="btn btn-primary btn-login">
                    Entrar
                </button>

            </form>

            

        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
