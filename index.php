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
  <meta name="description" content="A plataforma Fokus transforma a forma de estudar com Pomodoro, gerenciamento de tarefas, ranking competitivo e mais. Prepare-se para o ENEM e Concursos.">
  <title>Fokus - Plataforma de Estudos</title>

  <!-- CSS Files -->
  <link rel="stylesheet" href="/public/CSS/index/headerLeadingP.css">
  <link rel="stylesheet" href="/public/CSS/index/index.css">
  <link rel="stylesheet" href="/public/CSS/style.css">
  <link rel="stylesheet" href="/public/CSS/footer/footer.css">

  <!-- External Libraries -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-straight/css/uicons-regular-straight.css'>
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
</head>

<body>
  <header>
    <nav id="navigation" class="navbar navbar-expand-lg px-4 fixed-top">
      <div class="nav-left">
        <img src="/public/img/LogoIcon.png" id="imgLogo" alt="Logo">
        <div class="LogoText">
          <a href="/indexR.php" class="logo">Fokus</a>
          <span>Plataforma de Estudos</span>
        </div>
      </div>
      
      <!--  Menu mobile -->
      <button class="navbar-toggler menu" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="bar .text-primary"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </button>

      <div class="collapse navbar-collapse navmenu-container" id="navbarMenu">
        <ul class="navmenu">
        </ul>

          <a href="src/views/login.php" class="btn primary">Entrar na Plataforma</a>
        </div>
      </div>
    </nav>
  </header>

  <!-- Background Element -->
  <div class="background"></div>

  <!-- Hero Section -->
  <section class="hero mt-5">
    <span class="tag mt-2">📌 Prepare-se para o ENEM e Concursos</span>
    <h1>Decole seus <span class="highlight">estudos com Fokus</span></h1>
    <p>A plataforma completa que transforma sua forma de estudar com cronômetro Pomodoro, gerenciamento de tarefas, ranking competitivo e muito mais.</p>
    <div class="buttons">
      <a href="src/views/login.php" class="btn primary">▶️ Começar a Estudar</a>
    </div>
    <div class="stats">
      <div><strong>+10.000</strong><br>Estudantes</div>
      <div><strong>Método</strong><br>Comprovado</div>
      <div><strong>Resultados</strong><br>Reais</div>
    </div>
  </section>

  <!-- Funcionalidades -->
  <section class="features">
    <h2>Funcionalidades que Transformam seus Estudos</h2>
    <p>Cada ferramenta foi pensada para maximizar seu aprendizado</p>

    <div class="feature-card blue">
      <div class="feature-icon">
        <i class="fi fi-sr-alarm-clock"></i>
      </div>
      <div class="feature-text">
        <span class="tag">⏱ Cronômetro Pomodoro</span>
        <p>Use intervalos de 25 minutos de estudo intenso seguidos de pausas estratégicas...</p>
      </div>
    </div>

    <div class="feature-card green">
      <div class="feature-icon">
        <i class="fi fi-rr-list-check"></i>
      </div>
      <div class="feature-text">
        <span class="tag">✅ Gerenciamento de Tarefas</span>
        <h3>Organize seus Estudos por Matéria</h3>
        <p>Crie listas de tarefas organizadas por disciplina...</p>
      </div>
    </div>

    <div class="feature-card purple">
      <div class="feature-icon">
        <i class="fi fi-rr-folder"></i>
      </div>
      <div class="feature-text">
        <span class="tag">📂 Upload de Material</span>
        <h3>Centralize todos seus Materiais</h3>
        <p>Faça upload de PDFs, organize por matéria...</p>
      </div>
    </div>

    <div class="feature-card yellow">
      <div class="feature-icon">
        <i class="fi fi-rr-trophy"></i>
      </div>
      <div class="feature-text">
        <span class="tag">🏆 Ranking Competitivo</span>
        <h3>Compete e se Motive</h3>
        <p>Sistema de pontuação baseado em horas de estudo...</p>
      </div>
    </div>

    <div class="feature-card red">
      <div class="feature-icon">
        <i class="fi fi-rr-chart-pie-alt"></i>
      </div>
      <div class="feature-text">
        <span class="tag">📊 Acompanhamento de Progresso</span>
        <h3>Visualize sua Evolução</h3>
        <p>Gráficos detalhados mostram seu desempenho...</p>
      </div>
    </div>
  </section>


  <!-- Call to Action -->
  <section class="cta">
    <h2>Pronto para decolar seus <span class="highlight">estudos?</span></h2>
    <p>Junte-se a milhares de estudantes que já transformaram seus resultados com Fokus.</p>
    <a href="#" class="btn primary">🚀 Começar Agora - É Grátis</a>
  </section>

  <!-- Footer Section -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/src/js/script.js"></script>
  <script src="/src/js/background.js"></script>
  <script src="/src/js/darkTheme.js"></script>

</body>

</html>