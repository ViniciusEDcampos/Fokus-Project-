<?php
session_start();
// calcula a base a partir do caminho do script. No seu caso vira "/Fokus-Project-/"
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fokus - Início</title>

  <!-- Base URL para todos os links/arquivos desta página -->
  <base href="<?= htmlspecialchars($base, ENT_QUOTES) ?>">

  <!-- CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <!-- Hero Section -->
  <section class="d-flex flex-column justify-content-center align-items-center text-center vh-100 bg-light">
    <div class="container">
      <h1 class="display-3 fw-bold mb-4">🚀 Fokus</h1>
      <p class="lead mb-5">Sua plataforma de estudos gamificada para melhorar seu foco e produtividade.</p>

      <div class="d-flex gap-3 justify-content-center">
        <a href="src/views/cadastro.php" class="btn btn-success btn-lg px-4">Cadastrar</a>
        <a href="src/views/login.php" class="btn btn-primary btn-lg px-4">Login</a>
        <a href="src/views/ranking.php" class="btn btn-primary btn-lg px-4">Ranking</a>
      </div>
    </div>
  </section>

</body>
</html>