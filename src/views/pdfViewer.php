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

<link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-straight/css/uicons-regular-straight.css'>
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>
<link rel="stylesheet" href="/src/views/layout/header.php">
</head>
<body>

       <div class="background"></div>
       <?php include __DIR__ . "/layout/header.php"; ?>

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

  <?php include __DIR__ . "/layout/footer.php"; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- JS --> 
<script src="/src/js/background.js"></script>
<script src="/src/js/todolist.js"></script>
<script src="/src/js/pdfViewer.js"></script>
<script src="/src/js/darkTheme.js"></script>

    
</body>
</html>