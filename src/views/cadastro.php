<?php
session_start();
include __DIR__ . "/../config/db.php";

$erro = "";
$sucesso = "";

// Se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome      = trim($_POST['nome'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $email     = strtolower(trim($_POST['email'] ?? ''));
    $senha     = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    // validações simples
    if ($senha !== $confirmar) {
        $erro = "As senhas não conferem!";
    } else {
        // verifica se já existe usuário com esse e-mail
        $check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $erro = "E-mail já cadastrado!";
        } else {
            // gera hash seguro da senha
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            // insere no banco
            $sql = "INSERT INTO usuarios (nome, email, senha, datainicio) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $dataHoje = date("Y-m-d");
            $stmt->bind_param("ssss", $nome, $email, $senhaHash, $dataHoje);

            if ($stmt->execute()) {
                $sucesso = "Cadastro realizado com sucesso! Agora você pode fazer login.";
            } else {
                $erro = "Erro ao cadastrar. Tente novamente.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Fokus - Cadastro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./cadastro.css">
</head>
<body>
  <main class="container py-5">
    <div class="cadastro-card shadow-lg p-4 mx-auto" style="max-width:500px;">
      <h2 class="text-center mb-4">Cadastro</h2>

      <form method="POST" action="">
        <!-- Nome -->
        <div class="mb-3">
          <label class="form-label">Nome completo</label>
          <input type="text" name="nome" class="form-control" required>
        </div>

        <!-- Username -->
        <div class="mb-3">
          <label class="form-label">Nome de usuário</label>
          <input type="text" name="username" class="form-control" required>
        </div>

        <!-- E-mail -->
        <div class="mb-3">
          <label class="form-label">E-mail</label>
          <input type="email" name="email" class="form-control" required>
        </div>

        <!-- Senha -->
        <div class="mb-3">
          <label class="form-label">Senha</label>
          <input type="password" name="senha" class="form-control" required>
        </div>

        <!-- Confirmar -->
        <div class="mb-3">
          <label class="form-label">Confirmar senha</label>
          <input type="password" name="confirmar" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Cadastrar</button>

        <p class="text-center mt-3 mb-0">Já tem conta? <a href="login.php">Entrar</a></p>
      </form>

      <!-- Mensagens -->
      <?php if ($erro): ?>
        <div class="alert alert-danger text-center mt-3"><?= $erro ?></div>
      <?php endif; ?>
      <?php if ($sucesso): ?>
        <div class="alert alert-success text-center mt-3"><?= $sucesso ?></div>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>