<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$userName = $_SESSION['user_name'] ?? 'Usuário';
$userRole = $_SESSION['user_role'] ?? 'Estudante';
$userIcon = strtoupper(substr($userName,0,1));

// Nome do arquivo atual (ex: "dashboard.php")
$current = basename($_SERVER['PHP_SELF']);
?>
<header id="navigation">
  <div class="nav-left">
    <a href="/src/views/dashboard.php" class="LogoText">
      <img src="/public/img/logo.png" id="imgLogo" alt="Logo">
      <span class="logo">Fokus</span>
    </a>
  </div>

  <!-- Botão menu mobile -->
  <button class="navbar-toggler menu" type="button" data-bs-toggle="collapse"
          data-bs-target="#navbarMenu" aria-controls="navbarMenu"
          aria-expanded="false" aria-label="Toggle navigation">
    <span class="bar"></span>
    <span class="bar"></span>
    <span class="bar"></span>
  </button>

  <!-- Menu -->
  <div class="collapse navbar-collapse navmenu-container" id="navbarMenu">
    <ul class="navmenu">
      <li><a href="/src/views/dashboard.php" class="<?= $current=='dashboard.php'?'active':'' ?>">
        <i class="fi fi-rs-home"></i> Início
      </a></li>
      <li><a href="/src/views/cronometro.php" class="<?= $current=='cronometro.php'?'active':'' ?>">
        <i class="fi fi-rr-alarm-clock"></i> Cronômetro
      </a></li>
      <li><a href="/src/views/cadastro.php" class="<?= $current=='cadastro.php'?'active':'' ?>">
        <i class="fi fi-rs-edit"></i> Tarefas
      </a></li>
      <li><a href="/src/views/hanking.php" class="<?= $current=='hanking.php'?'active':'' ?>">
        <i class="fi fi-rr-trophy"></i> Ranking
      </a></li>
      <li><a href="/src/views/andamento.php" class="<?= $current=='andamento.php'?'active':'' ?>">
        <i class="fi fi-rr-chat-arrow-grow"></i> Andamento
      </a></li>
    </ul>

    <div class="nav-right mt-3 mt-lg-0">
      <div class="btn-box">
        <button id="btn-tema"><i class="fi fi-rr-moon"></i></button>
      </div>
      <div class="user">
        <div class="user-icon"><?php echo $userIcon; ?></div>
        <span><?php echo $userName; ?><br><small><?php echo $userRole; ?></small></span>
      </div>
    </div>
  </div>
</header>