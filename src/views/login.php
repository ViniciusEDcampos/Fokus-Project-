<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/../config/db.php';

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = (string) ($_POST['senha'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail inválido.";
    } elseif ($senha === '') {
        $erro = "Senha é obrigatória.";
    } else {
        $sql = "SELECT id_usuario AS id, nome, email, senha FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {
            $u = $res->fetch_assoc();

            if (hash_equals((string) $u['senha'], $senha)) {
                $_SESSION['id_usuario'] = (int) $u['id'];
                $_SESSION['usuario_nome'] = $u['nome'];
                header("Location: dashboard.php");  // ajuste se necessário
                exit;
            }
        }
        $erro = "E-mail ou senha incorretos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fokus - Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/CSS/header/header.css">
    <link rel="stylesheet" href="/public/CSS/style.css">
    <link rel="stylesheet" href="../CSS/fundo.css">
    <link rel="stylesheet" href="/public/CSS/login.css">

    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet"
        href="https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
</head>

<body>
    <!-- fundo animado -->
    <div class="background"></div>

    <header>
        <nav id="navigation" class="navbar navbar-expand-lg px-4">
            <div class="nav-left">
                <img src="/public/img/LogoIcon.png" id="imgLogo" alt="Logo">
                <div class="LogoText">
                    <a href="/indexR.php" class="logo">Fokus</a>
                    <span>Plataforma de Estudos</span>
                </div>
            </div>
    </header>

    <main>
        <div class="login-wrapper">
            <div class="card p-4 shadow-lg login-card">
                <h2 class="text-center mb-4">Login</h2>
                <form method="POST" action="">
                    <!-- E-mail -->
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fi fi-rr-envelope"></i></span>
                            <input type="email" id="email" name="email" class="form-control"
                                placeholder="Digite seu e-mail" required>
                        </div>
                    </div>

                    <!-- Senha -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fi fi-rr-lock"></i></span>
                            <input type="password" id="password" name="senha" class="form-control"
                                placeholder="Digite sua senha" required>
                        </div>
                    </div>

                    <!-- Botão -->
                    <button type="submit" class="btn btn-primary w-100">Entrar</button>

                    <?php if (!empty($erro)): ?>
                        <div class="alert alert-danger text-center mt-3"><?= htmlspecialchars($erro) ?></div>
                    <?php endif; ?>

                    <!-- Links extras -->
                    <p class="text-center mt-2">Não tem conta? <a href="/src/views/cadastro.php">Cadastre-se</a></p>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/background.js"></script>
    <script src="../js/darkTheme.js"></script>
    <script src="../js/script.js"></script>

</body>

</html>