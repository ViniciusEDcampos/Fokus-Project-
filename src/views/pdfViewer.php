<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fokus - Aprenda com Foco</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="/public/CSS/material/bodyMaterial.css">
<link rel="stylesheet" href="/public/CSS/header/header.css">
<link rel="stylesheet" href="/public/CSS/style.css">

<link rel="stylesheet" href="CSS/style.css">

<link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-straight/css/uicons-regular-straight.css'>
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>
</head>
<body>

       <div class="background"></div>

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
        <li class="nav-item"><a href="index.html"><i class="fi fi-rs-edit"></i> Tarefas</a></li>
        <li class="nav-item"><a href="#"><i class="fi fi-rr-trophy"></i> Ranking</a></li> 
        <li class="nav-item"><a href="Material.html"><i class="fi fi-rs-document"></i> Material</a></li>
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

 <main class="container-principal">
    <section id="home" class="container">
      <!-- wrapper central para não ficar colado na borda -->
      <div class="page-inner">
        <div class="tituloInicial text-center">
          <h1><i class="fi fi-rr-book-alt text-primary "></i><strong>Material de Estudo</strong><i class="fi fi-rr-graduation-cap text-primary"></i></h1>
          <p>Organize seus materiais de estudo em PDF. Upload arquivos, organize por matéria e tenha <br>acesso rápido ao seu conteúdo.</p>
        </div>

        
     <!-- PDF Viewer -->
    <div class="md-6">
      <h3>Estude com seu PDF</h3>
      <div class="">
      </div>
      <input type="file" id="pdfUpload" accept="application/pdf" class="form-control mb-3">
      <iframe id="pdfViewer" width="100%" height="600px" style="border: 1px solid #ccc; display: none;"></iframe>
    </div>

    <h4 id="tituloSegundario">Seus Materiais Recentes</h4>
    
    <ul id="historicoPDFs" class="list-group mb-3"></ul>

    </section>
  </main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- JS --> 
<script src="/src/js/background.js"></script>
<script src="/src/js/todolist.js"></script>
<script src="/src/js/pdfViewer.js"></script>
<script src="/src/js/script.js"></script>

    
</body>
</html>