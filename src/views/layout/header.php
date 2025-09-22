
<header>
  <nav id="navigation" class="navbar navbar-expand-lg px-4 fixed-top">
    <div class="nav-left">
      <img src="/public/img/LogoIcon.png" id="imgLogo" alt="Logo">
      <div class="LogoText">
        <a href="#" class="logo" >Fokus</a>
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
        <li class="nav-item"><a href="#"><i class="fi fi-rs-home"></i> Inicio</a></li>
        <li class="nav-item"><a href="#"><i class="fi fi-rr-alarm-clock"></i> Cronometro</a></li>
        <li class="nav-item"><a href="/src/views/tarefas.php"><i class="fi fi-rs-edit"></i> Tarefas</a></li>
        <li class="nav-item"><a href="#"><i class="fi fi-rr-trophy"></i> Ranking</a></li> 
        <li class="nav-item"><a href="/src/views/pdfViewer.php"><i class="fi fi-rs-document"></i> Material</a></li>
        <li class="nav-item"><a href="#"><i class="fi fi-rr-chat-arrow-grow"></i> Andamento</a></li>
      </ul>

      <div class="nav-right mt-3 mt-lg-0">
        <div class="btn-box">
          <button id="btn-tema"><i class="fi fi-rr-moon"></i></button>
        </div>
        <div class="user">
          <div class="user-icon">U</div>
          <span>Usuário<br><small>Estudante</small></span>
        </div>
      </div>
    </div>
  </nav>
</header>