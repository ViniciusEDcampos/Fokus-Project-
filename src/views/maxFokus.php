<?php
session_start();
require __DIR__ . "/../config/db.php";

// Exigir login
if (empty($_SESSION['id_usuario'])) {
    header('Location: /src/views/login.php');
    exit;
}

$idUsuario = (int) $_SESSION['id_usuario'];

// Nome do usuário
$nome = $_SESSION['user_name'] ?? '';
if ($nome === '') {
    $stmt = $conn->prepare("SELECT nome FROM usuarios WHERE id_usuario = ? LIMIT 1");
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $nome = $stmt->get_result()->fetch_column() ?: 'Usuário';
}
$partes = preg_split('/\s+/', trim($nome));
$primeiroNome = htmlspecialchars($partes[0] ?? 'Usuário', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fokus - Plataforma Unificada</title>

    <!-- Bootstrap / Fonts / Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-straight/css/uicons-regular-straight.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>

    <!-- CSS Unificado -->
    <link rel="stylesheet" href="/public/CSS/maxFokusPage/maxFokusPage.css">
    <link rel="stylesheet" href="/public/CSS/footer/footer.css">
    <link rel="stylesheet" href="/public/CSS/maxFokusPage/headerMax.css">
    <link rel="stylesheet" href="/public/CSS/style.css">

</head>

<body>
    <div class="background"></div>
<header>
  <nav id="navigation" class="navbar navbar-expand-lg px-4 fixed-top">
    <div class="nav-left">
      <button id="btn-secret"><img src="/public/img/LogoIcon.png" id="imgLogo" alt="Logo"></button>
      <div class="LogoText">
        <a href="/indexR.php" class="logo" >Fokus</a>
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
                <li class="nav-item"><a href="/src/views/dashboard.php"><i class="fi fi-rr-refresh"></i>Voltar ao Modo Normal</a></li>
            </ul>

      <div class="nav-right mt-3 mt-lg-0">
        <div class="btn-box">
          <button id="btn-tema"><i class="fi fi-rr-moon"></i></button>
        </div>
        <div class="user">
            <div class="user">
                    <div class="user-icon"></div>
                    <span><?php echo $primeiroNome?></span>
                </div>
      </div>
    </div>
  </nav>
</header>

    <script src="/src/js/script.js"></script>


    <main class="container-principal flex-fill">

    <section class="page-section">
     <div class="tituloInicial text-center mt-2s">
                <h1><strong>Modo <span class="partesCentrais">Fokus</span>Total</strong></h1>
                <p>Otimize seus <span class="partesCentrais">estudos</span> ao maximo neste modo, e use Todas
                    ferramentas do site ao mesmo tempo<span class="partesCentrais">!</span>
                </p>
            </div>
    </section>   
    
        <!-- ================= CRONÔMETRO ================= -->
        <section id="cronometro" class="page-section">
        
            <div class="tituloInicial text-center">
                <h1><strong>Cronômetro de Estudos</strong></h1>
                <p>Use a técnica Pomodoro para maximizar sua concentração</p>
            </div>
            <div class="cronometro-area">
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
                            <li>
                                <p>25 min: Sessão de foco total</p>
                            </li>
                            <li>
                                <p>5 min: Pausa curta</p>
                            </li>
                            <li>
                                <p>15-30 min: Pausa longa (a cada 4 sessões)</p>
                            </li>
                            <li>
                                <p>Elimine distrações durante as sessões</p>
                            </li>
                        </ul>
                    </div>
                </aside>
            </div>
        </section>
        <!-- ================= TO-DO-LIST ================= -->
        <section id="todo" class="page-section">
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
                                <input type="text" id="taskInput" name="titulo" class="form-control flex-grow-1" placeholder="Adicione uma tarefa..." required />
                                <div class="linha-opcoes">
                                    <p class="label-text">Prioridade:</p>
                                    <select id="taskPriority" name="prioridade" class="form-select" aria-label="Prioridade">
                                        <option value="Baixa">Baixa</option>
                                        <option value="Média">Média</option>
                                        <option value="Alta">Alta</option>
                                    </select>
                                    <p class="label-text">Data:</p>
                                    <input type="date" id="taskDate" name="data_estudo" class="form-control">
                                    <p class="label-text">Tempo:</p>
                                    <input type="number" id="taskTime" name="tempo_min" class="form-control" placeholder="(minutos)" min="0">
                                </div>
                                <input type="text" id="taskNote" name="observacao" class="form-control flex-grow-1" placeholder="Observação (opcional)" />
                                <input type="text" id="taskMateria" name="materia" class="form-control flex-grow-1" placeholder="Matéria" />
                                <button id="addBtn" class="btn btn-primary btn-sm flex-grow-1 mt-3">Adicionar Tarefa</button>
                            </form>

                            <section class="boxes mb-3">
                                <div id="filtros" class="card p-3 filters-card ">
                                    <div class="row gy-3 align-items-start">

                                        <!-- Status  -->
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

                                        <!-- Prioridade -->
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

                                        <!-- Filtros avançados -->
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
        </section>
        <!-- ================= PDF VIEWER ================= -->
        <section id="pdf" class="page-section">
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

                        <input type="file" id="pdfUpload" name="pdf" accept="application/pdf" class="form-control mb-3">
                        <iframe id="pdfViewer" width="100%" height="600px" style="border: 1px solid #ccc; display: none;"></iframe>
                    </div>
                    <button class="btn btn-primary mb-1 mt-3" onclick="abrirTelaCheia()">
                        <i class="fas fa-expand"></i> Tela Cheia
                    </button>

                    <h4 id="tituloSegundario">Seus Materiais Recentes</h4>

                    <ul id="historicoPDFs" class="list-group mb-3"></ul>

            </section>
        </section>
    </main>

    <?php include __DIR__ . "/layout/footer.php"; ?>
    <!-- Scripts globais -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/src/js/background.js"></script>
    <script src="/src/js/cronometro.js"></script>
    <script src="/src/js/todolist.js"></script>
    <script src="/src/js/pdfViewer.js"></script>
    <script src="/src/js/darkTheme.js"></script>
    <script src="/src/js/script.js"></script>

</body>

</html>