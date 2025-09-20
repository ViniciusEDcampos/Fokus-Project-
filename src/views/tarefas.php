<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fokus - Aprenda com Foco</title>
 
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- link com CSS -->

<link rel="stylesheet" href="/public/CSS/pdfViewer/pdfViewer.css">
<link rel="stylesheet" href="/public/CSS/header/header.css">
<link rel="stylesheet" href="/public/CSS/style.css">

<link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-straight/css/uicons-regular-straight.css'>
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>
</head>
<body>
 
       <div class="background"></div>
    
          <?php include __DIR__ . "/layout/header.php"; ?>
 
 <main class="container-principal">
    <section id="home" class="container">
      <!-- wrapper central para não ficar colado na borda -->
      <div class="page-inner">
        <div class="tituloInicial text-center">
          <h1><strong>Gerenciador de Tarefas</strong></h1>
          <p>Organize seus estudos e acompanhe seu progresso</p>
        </div>
 
        <section id="info-boxes" class="d-flex flex-wrap justify-content-around">
          <div class="box-info">
            <div class="iconbox">
              <i class="fi fi-rr-chart-histogram" id="grafico-icon"></i>
            </div>
            <div class="info">
              <span id="total-tarefas">0</span>
              <p>Total de Tarefas</p>
            </div>
          </div>
 
          <div class="box-info">
            <div class="iconbox">
              <i class="fi fi-sr-check" id="concluidas-icon"></i>
            </div>
            <div class="info">
               <span id="tarefas-concluidas">0</span>
              <p>Concluídas</p>
            </div>
          </div>
 
          <div class="box-info">
            <div class="iconbox">
              <i class="fi fi-rr-time-fast" id="pendentes-icon"></i>
            </div>
            <div class="info">
              <span id="tarefas-pendentes">0</span>
              <p>Pendentes</p>
            </div>
          </div>
 
          <div class="box-info">
            <div class="iconbox">
              <i class="fi fi-rr-tags" id="prioridades-icon"></i>
            </div>
            <div class="info">
              <span id="tarefas-alta">0</span>
              <p>Alta Prioridade</p>
            </div>
          </div>
        </section>
 
        <section class="Tela-Principal mt-4">
          <div class="to-do-list-container">
            <div class="titulos d-flex w-100 justify-content-between align-items-center">
              <h3>Lista de Tarefas - Fokus</h3>
     
            </div>
 
            <form id="addForm" class="d-flex gap-2 mb-3 flex-wrap" onsubmit="return false">
             
              <input type="text" id="taskInput" class="form-control flex-grow-1" placeholder="Adicione uma tarefa..." />
             
            <div class="linha-opcoes">
            <p class="label-text">Prioridade:</p>
            <select id="taskPriority" class="form-select" aria-label="Prioridade">
                <option value="Baixa">Baixa</option>
                <option value="Média">Média</option>
                <option value="Alta">Alta</option>
            </select>
 
            <p class="label-text">Data:</p>
            <input type="date" id="taskDate" class="form-control">
 
            <p class="label-text">Tempo:</p>
            <input type="number" id="taskTime" class="form-control" placeholder="(minutos)">
           </div>
              <input type="text" id="taskNote" class="form-control flex-grow-1" placeholder="Observação (opcional)" />
              <input type="text" id="taskMateria" class="form-control flex-grow-1" placeholder="Matéria">
              <button id="addBtn" class="btn btn-primary btn-sm flex-grow-1 mt-3">Adicionar Tarefa</button>
           </form>
 
            <!-- LINHA ÚNICA: Atividades (esquerda)  —  Prioridade (direita com label acima) -->
            <section class="boxes mb-3">
  <div id="filtros" class="card p-3 filters-card ">
    <div class="row gy-3 align-items-start">
      <!-- Status (esquerda em desktop, 1ª linha no mobile) -->
      <div class="col-12 col-lg-6">
        <p class="label-text mb-2">Atividades Selecionadas:</p>
        <div class="btn-toolbar flex-wrap gap-2" role="toolbar" aria-label="Filtros de status">
          <div class="btn-group btn-group-sm flex-wrap w-100 w-sm-auto" role="group" id="boxPrioridades">
            <button class="btn btn-outline-primary filter active" data-filter="all" id="all-btn">Todas</button>
            <button class="btn btn-outline-primary filter" data-filter="active" id="on-btn">Ativas</button>
            <button class="btn btn-outline-primary filter" data-filter="completed" id="off-btn">Concluídas</button>
          </div>
        </div>
      </div>
 
      <!-- Prioridade (direita em desktop, 2ª no mobile) -->
      <div class="col-12 col-lg-6">
        <p class="label-text mb-2">Nível de Prioridade:</p>
        <div class="btn-toolbar flex-wrap gap-2" role="toolbar" aria-label="Filtros de prioridade">
          <div class="btn-group btn-group-sm flex-wrap w-100 w-sm-auto priority-buttons" role="group">
            <button class="btn btn-outline-primary priority-filter active" data-priority="all">Todas Prioridades</button>
            <button class="btn btn-outline-success priority-filter" data-priority="Baixa">Baixa</button>
            <button class="btn btn-outline-warning priority-filter" data-priority="Média">Média</button>
            <button class="btn btn-outline-danger priority-filter" data-priority="Alta">Alta</button>
          </div>
        </div>
      </div>
 
      <!-- Filtros avançados (linha inteira) -->
      <div class="col-12">
        <p class="label-text mb-2">Filtros Avançados:</p>
        <div class="row g-2">
          <div class="col-12 col-md-4">
            <input type="text" id="filterMateria" class="form-control" placeholder="Filtrar por matéria...">
          </div>
          <div class="col-12 col-md-4">
            <input type="date" id="filterData" class="form-control" placeholder="Filtrar por data">
          </div>
          <div class="col-12 col-md-4">
            <input type="number" id="filterTempo" class="form-control" min="0" placeholder="Tempo máximo (min)">
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
 
            <ul id="taskList" class="list-group"></ul>
 
            <div class="d-flex justify-content-between align-items-center mt-3 w-100">
              <div><span id="counter">0 tarefas</span></div>
              <div class="progress mt-2" style="height:10px; width:50%;">
                <div id="bar" class="progress-bar bg-success" role="progressbar" style="width:0%"></div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </section>
  </main>
 
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
 
<!-- JS -->
<script src="/src/js/darkTheme.js"></script>
<script src="/src/js/todolist.js"></script>
<script src="/src/js/background.js"></script>

</body>
</html>