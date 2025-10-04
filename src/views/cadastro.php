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
            // insere no banco
            $sql = "INSERT INTO usuarios (nome, email, senha, datainicio) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $dataHoje = date("Y-m-d");
            $stmt->bind_param("ssss", $nome, $email, $senha, $dataHoje);

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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fokus - Cadastro</title>
 
  <!-- CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Mantenho seus caminhos como estavam no HTML novo -->
  <link rel="stylesheet" href="/public/CSS/header/header.css">
  <link rel="stylesheet" href="/public/CSS/style.css">
  <link rel="stylesheet" href="/public/CSS/cadastro.css">
 
  <!-- Ícones -->
  <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
</head>
<body>
  <div class="background"></div>
 
  <header>
    <nav id="navigation" class="navbar navbar-expand-lg px-4">
      <div class="nav-left">
        <img src="/public/img/LogoIcon.png" id="imgLogo" alt="Logo">
        <div class="LogoText">
          <a href="/index.php" class="logo">Fokus</a>
          <span>Plataforma de Estudos</span>
        </div>
      </div>
    </nav>
  </header>
 
  <main>
    <div class="cadastro-wrapper">
      <div class="cadastro-card shadow-lg">
        <h2 class="text-center mb-4">Cadastro</h2>
 
        <!-- IMPORTANTE: agora com method POST, action vazio (posta na mesma página) -->
        <form id="form-cadastro" method="POST" action="">
 
          <!-- Nome completo -->
          <div class="mb-3">
            <label for="nome" class="form-label">Nome completo</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fi fi-rr-user"></i></span>
              <!-- name adicionado -->
              <input type="text" id="nome" name="nome" class="form-control" placeholder="Digite seu nome completo" required>
            </div>
          </div>
 
          <!-- Username -->
          <div class="mb-3">
            <label for="username" class="form-label">Nome de usuário</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fi fi-rr-id-badge"></i></span>
              <input type="text" id="username" name="username" class="form-control" placeholder="Escolha um nome de usuário" required>
            </div>
          </div>
 
          <!-- E-mail -->
          <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fi fi-rr-envelope"></i></span>
              <input type="email" id="email" name="email" class="form-control" placeholder="Digite seu e-mail" required>
            </div>
          </div>
 
          <!-- Senha -->
          <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fi fi-rr-lock"></i></span>
              <input type="password" id="password" name="senha" class="form-control" placeholder="Digite sua senha" required>
            </div>
          </div>
 
          <!-- Confirmar Senha -->
          <div class="mb-3">
            <label for="confirm" class="form-label">Confirmar Senha</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fi fi-rr-check-circle"></i></span>
              <input type="password" id="confirm" name="confirmar" class="form-control" placeholder="Confirme sua senha" required>
            </div>
          </div>
 
          <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
 
          <p class="text-center mt-3 mb-0">
            Já tem conta? <a href="/src/views/login.php">Entrar</a>
          </p>
        </form>
 
        <!-- Mensagens -->
        <?php if ($erro): ?>
          <div class="alert alert-danger text-center mt-3"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($sucesso): ?>
          <div class="alert alert-success text-center mt-3"><?= htmlspecialchars($sucesso, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
      </div>
    </div>
  </main>
 
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Se você realmente usa esse script de fundo, mantenha. Senão, remova. -->
  <script src="../js/background.js"></script>
 
</body>
</html>