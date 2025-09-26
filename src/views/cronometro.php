<?php
session_start();
require __DIR__ . "/../config/db.php";

/* 1) Exigir login */
if (empty($_SESSION['id_usuario'])) {
  header('Location: /src/views/login.php');
  exit;
}

/* 2) Pegar id do usuário da sessão */
$idUsuario = (int) $_SESSION['id_usuario'];

/* 3) Nome do usuário: sessão -> banco -> fallback */
$nome = $_SESSION['user_name'] ?? '';
if ($nome === '') {
  $stmt = $conn->prepare("SELECT nome FROM usuarios WHERE id_usuario = ? LIMIT 1");
  $stmt->bind_param("i", $idUsuario);
  $stmt->execute();
  $nome = $stmt->get_result()->fetch_column() ?: 'Usuário';
}

/* 4) Primeiro nome bonito (com acentos) */
$partes = preg_split('/\s+/', trim($nome));
$primeiroNome = $partes[0] ?? 'Usuário';
if (function_exists('mb_convert_case')) {
  $primeiroNome = mb_convert_case($primeiroNome, MB_CASE_TITLE, 'UTF-8');
} else {
  $primeiroNome = ucwords(strtolower($primeiroNome));
}
$primeiroNome = htmlspecialchars($primeiroNome, ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fokus - Cronômetro de Estudos</title>

  <!-- CSS -->
  <link rel="stylesheet" href="/public/CSS/style.css">
  <link rel="stylesheet" href="/public/CSS/cronometro.css" />
  <link rel="stylesheet" href="/public/CSS/footer/footer.css" />
  <link rel="stylesheet" href="/public/CSS/" />

  <link rel="stylesheet" href="/public/CSS/style.css" />
  
  


  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/public/CSS/header/header.css">
  <link rel="stylesheet" href="/public//CSS/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-straight/css/uicons-regular-straight.css'>
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>

</head>

<body>
  <header>

  </header>
   <?php include __DIR__ . "/layout/header.php"; ?>
  <div class="background"></div>
  <div class="global"  style="margin-top:50px">
    <div class="titulo">
      <h1>Cronômetro de Estudos</h1>
      <p>Use a técnica Pomodoro para maximizar sua concentração</p>
    </div>

    <main>
      <!-- CRONÔMETRO -->
      <section class="cronometro">
        <div class="timer">
          <svg width="280" height="280" class="progress-ring">
            <circle class="progress-ring__bg" cx="140" cy="140" r="124" stroke="#e5e7eb" stroke-width="8" fill="none"></circle>
            <circle class="progress-ring__progress" cx="140" cy="140" r="124" stroke="#1b61fc" stroke-width="8" fill="none" stroke-dasharray="779.42" stroke-dashoffset="779.42" transform="rotate(-90 140 140)"></circle>
          </svg>
          <div class="timer-content">
            <h2>25:00</h2>
            <h3>Pronto para começar</h3>
          </div>
        </div>

        <div class="opcoes">
          <button data-time="15">15min</button>
          <button data-time="25" class="active">25min</button>
          <button data-time="45">45min</button>
          <button data-time="60">60min</button>
        </div>

        <div class="controle">
          <button class="start active">
            <ion-icon name="play-outline"></ion-icon>
            <p>Iniciar</p>
          </button>
          <button class="reset">
            <ion-icon name="refresh-outline"></ion-icon>
            <p>Reiniciar</p>
          </button>
        </div>
      </section>

      <!-- SIDEBAR -->
      <aside>
        <div class="conteiner status">
          <h3>Estatísticas de Hoje</h3>
          <p>Tempo total: <strong id="tempo-total">00:00</strong></p>
          <p>Sessão atual: <strong id="tempo-sessao">25min</strong></p>
          <p>Status: <strong id="status-sessao">Pausado</strong></p>
        </div>
        <div class="conteiner topicos">
          <h3>Dicas da Técnica Pomodoro</h3>
          <ul>
            <li>25 min: Sessão de foco total</li>
            <li>5 min: Pausa curta</li>
            <li>15-30 min: Pausa longa (a cada 4 sessões)</li>
            <li>Elimine distrações durante as sessões</li>
          </ul>
        </div>
      </aside>
    </main>
  </div>

   <?php include __DIR__ . "/layout/footer.php"; ?>
  <!-- Scripts -->
  <script src="/src/js/background.js"></script>
  <script src="/src/js/cronometro.js"></script>
  <script src="/src/js/darkTheme.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/src/js/script.js"></script>
</body>
</html>
