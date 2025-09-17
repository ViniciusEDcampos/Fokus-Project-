
<?php
session_start();
include "../config/db.php"; 

$erro = "";

// Se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();

        if (password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario'] = $usuario['nome'];
            header("Location: dashboard.php");
            exit();
        } else {
            $erro = "E-mail ou senha incorretos!";
        }
    } else {
        $erro = "E-mail ou senha incorretos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Fokus - Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <main>
        <div class="login-wrapper">
            <div class="card p-4 shadow-lg login-card">
                <h2 class="text-center mb-4">Login</h2>

                <!-- Envia para valida.php -->
                <form action="valida.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fi fi-rs-user"></i></span>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fi fi-rs-lock"></i></span>
                            <input type="password" id="password" name="senha" class="form-control" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </form>
                <?php if (isset($_SESSION['erro'])): ?>
                    <p class="text-danger text-center mt-3">
                        <?= $_SESSION['erro'];
                        unset($_SESSION['erro']); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>

</html>