
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

require __DIR__ . "/../config/db.php"; // ajusta caminho se precisar
/* === HORAS ESTUDADAS HOJE === */
$sql = "SELECT SUM(duracao_segundos) AS total_min 
        FROM sessoes_estudo 
        WHERE id_usuario = ? AND DATE(data_hora) = CURDATE()";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$horasHoje = round(($res['total_min'] ?? 0) / 60, 1);

/* === TAREFAS CONCLUÍDAS HOJE === */
$sql = "SELECT 
           SUM(CASE WHEN status='concluida' THEN 1 ELSE 0 END) AS concluidas,
           COUNT(*) AS total
        FROM tarefas
        WHERE id_usuario = ? AND DATE(data_criacao) = CURDATE()";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$tarefasConcluidas = $res['concluidas'] ?? 0;
$tarefasTotais     = $res['total'] ?? 0;

/* === ÚLTIMAS SESSÕES DE ESTUDO === */
$sql = "SELECT materia, duracao_segundos, data_hora
        FROM sessoes_estudo 
        WHERE id_usuario = ?
        ORDER BY data_hora DESC 
        LIMIT 4";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$ultimas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* === CONQUISTAS DO USUÁRIO === */
$sql = "SELECT c.nome, c.descricao, uc.data_alcance
        FROM usuarios_conquistas uc
        JOIN conquistas c ON c.id_conquista = uc.id_conquista
        WHERE uc.id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$conquistas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fokus - Início</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/public/CSS/dashbord.css">
  <link rel="stylesheet" href="/public/CSS/header/header.css">
  <link rel="stylesheet" href="/public//CSS/style.css">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-straight/css/uicons-regular-straight.css'>
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>

</head>
<body>
<?php include __DIR__ . "/layout/header.php"; ?>
<main class="container" style="margin-top:100px">
  <h1>Bem-vindo de volta, <span style="color:#2563eb;">
    <?php echo $primeiroNome; ?>
  </span>!</h1>
  <p class="subtitle">Aqui está um resumo do seu progresso nos estudos</p>

  <!-- MÉTRICAS -->
  <section class="grid">
    <div class="card metric"><strong><?php echo $horasHoje; ?>h</strong><span>Estudadas Hoje</span></div>
    <div class="card metric"><strong><?php echo "$tarefasConcluidas/$tarefasTotais"; ?></strong><span>Tarefas Hoje</span></div>
    <div class="card metric"><strong>—</strong><span>Dias Seguidos (a implementar)</span></div>
    <div class="card metric"><strong>—</strong><span>Posição Ranking</span></div>
  </section>

  <!-- PROGRESSOS -->
  <section class="card">
    <h2>Meta Semanal</h2>
    <p>Progresso da sua meta de 30 horas por semana</p>
    <?php 
      $sql = "SELECT SUM(duracao_segundos) AS total_min 
              FROM sessoes_estudo 
              WHERE id_usuario = ? 
              AND YEARWEEK(data_hora, 1) = YEARWEEK(CURDATE(), 1)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $idUsuario);
      $stmt->execute();
      $res = $stmt->get_result()->fetch_assoc();
      $horasSemana = round(($res['total_min'] ?? 0) / 60, 1);
      $meta = 30;
      $pctSemana = min(($horasSemana / $meta) * 100, 100);
    ?>
    <div class="progress-bar"><span style="width:<?php echo $pctSemana; ?>%"></span></div>
    <small><?php echo $horasSemana; ?>h estudadas • faltam <?php echo max(0, $meta - $horasSemana); ?>h</small>
  </section>

  <section class="card">
    <h2>Progresso Diário</h2>
    <p>Suas tarefas e atividades de hoje</p>
    <?php 
      $pct = $tarefasTotais > 0 ? ($tarefasConcluidas / $tarefasTotais * 100) : 0;
    ?>
    <div class="progress-bar green"><span style="width:<?php echo $pct; ?>%"></span></div>
    <small><?php echo "$tarefasConcluidas de $tarefasTotais tarefas concluídas"; ?></small>
  </section>

  <!-- GRID INFERIOR -->
  <div class="grid">
    <section class="card">
      <h2>Atividade Recente</h2>
      <ul class="list">
        <?php foreach($ultimas as $u): ?>
          <li>📘 <?php echo htmlspecialchars($u['materia']); ?> 
            <span>
              <?php 
                echo floor($u['duracao_segundos']/60)."h ".($u['duracao_segundos']%60)."min";
              ?>
            </span>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>

    <section class="card">
      <h2>Conquistas</h2>
      <ul class="list">
        <?php if ($conquistas): ?>
          <?php foreach($conquistas as $c): ?>
            <li>🏆 <?php echo htmlspecialchars($c['nome']); ?>
              <span>(<?php echo date('d/m/Y', strtotime($c['data_alcance'])); ?>)</span>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <li>Sem conquistas ainda 😅</li>
        <?php endif; ?>
      </ul>
    </section>

    <section class="card">
      <h2>Ações Rápidas</h2>
     <a href="/src/views/cronometro.php"><button class="btn blue">⏱ Iniciar Cronômetro</button></a>
      <a href="/src/views/tarefas.php"><button class="btn green">➕ Adicionar Tarefa</button></a>
    <a href="/src/views/andamento.php"> <button class="btn purple">📊 Ver Progresso</button></a>
    </section>
  </div>
</main>
 <?php include __DIR__ . "/layout/footer.php"; ?>
<script src="script.js"></script>
<script src="/src/js/background.js"></script>
</body>

</html>