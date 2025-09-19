<?php
session_start();
ini_set('display_errors',1); error_reporting(E_ALL);
require __DIR__ . '/../config/db.php'; // expõe $conn (MySQLi)

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = strtolower(trim($_POST['email'] ?? ''));
  $senha = (string)($_POST['senha'] ?? '');

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erro = "E-mail inválido.";
  } elseif ($senha === '') {
    $erro = "Senha é obrigatória.";
  } else {
    $sql  = "SELECT id_usuario AS id, nome, email, senha FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows === 1) {
      $u = $res->fetch_assoc();
      // COMPARAÇÃO EM TEXTO PURO (inseguro)
      if (hash_equals((string)$u['senha'], $senha)) {
        $_SESSION['id_usuario']   = (int)$u['id'];
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
  <meta charset="UTF-8" />
  <title>Fokus - Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- ajuste o caminho do CSS: se seus arquivos estão em /public/css -->
  <link rel="stylesheet" href="/public/css/login.css" />
</head>
<body>
  <main class="container py-5">
    <div class="card p-4 shadow-lg mx-auto" style="max-width:400px;">
      <h2 class="text-center mb-4">Login</h2>

      <form method="POST" action="">
        <div class="mb-3">
          <label for="email" class="form-label">E-mail</label>
          <input type="email" id="email" name="email" class="form-control" required />
        </div>

        <div class="mb-3">
          <label for="senha" class="form-label">Senha</label>
          <input type="password" id="senha" name="senha" class="form-control" required />
        </div>

        <button type="submit" class="btn btn-primary w-100">Entrar</button>
      </form>

      <?php if (!empty($erro)): ?>
        <div class="alert alert-danger text-center mt-3"><?= htmlspecialchars($erro) ?></div>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>