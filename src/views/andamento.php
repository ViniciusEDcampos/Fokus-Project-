<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
// ----------------------
// 1) Total estudado na semana
// ----------------------
$sqlSemana = "
  SELECT SUM(duracao_segundos) as total_semana
  FROM sessoes_estudo
  WHERE id_usuario = ?
  AND YEARWEEK(data_hora, 1) = YEARWEEK(CURDATE(), 1)
";

$stmt = $conn->prepare($sqlSemana);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$stmt->bind_result($totalSemana);
$stmt->fetch();
$stmt->close();

$totalSemana = $totalSemana ?? 0;
$totalHorasSemana = round($totalSemana / 3600, 1); // em horas

//Progresso diário (últimos 7 dias) sempre atualizado

$sqlDia = "
  SELECT DATE(data_hora) as dia, SUM(duracao_segundos) as total
  FROM sessoes_estudo
  WHERE id_usuario = ?
  AND YEARWEEK(data_hora, 1) = YEARWEEK(CURDATE(), 1)
  GROUP BY DATE(data_hora)
  ORDER BY DATE(data_hora)
";;

$stmt = $conn->prepare($sqlDia);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$resDias = $stmt->get_result();

$progressoDiario = [];
$map = [
    'Mon' => 'Seg',
    'Tue' => 'Ter',
    'Wed' => 'Qua',
    'Thu' => 'Qui',
    'Fri' => 'Sex',
    'Sat' => 'Sáb',
    'Sun' => 'Dom'
];

while ($row = $resDias->fetch_assoc()) {
    $diaSemana = date('D', strtotime($row['dia'])); // Mon, Tue...
    $dia = $map[$diaSemana] ?? $diaSemana;
    $progressoDiario[$dia] = round($row['total'] / 3600, 1);
}
$stmt->close();

// ----------------------
// 3) Meta semanal
// ----------------------
$meta = 30; // horas por semana
$progressoGeral = $meta > 0 ? round(($totalHorasSemana / $meta) * 100, 0) : 0;


$meses = 6; // últimos 6 meses

$hoje = new DateTime();
$primeiroDia = (clone $hoje)->modify("-$meses month")->modify('first day of this month')->format('Y-m-d');
$ultimoDia = $hoje->format('Y-m-d');

// Consulta as ações do usuário no período
$sql = "
    SELECT DATE(data_hora) AS dia, COUNT(*) AS total
    FROM sessoes_estudo
    WHERE id_usuario = ?
    AND data_hora >= ? AND data_hora <= ?
    GROUP BY DATE(data_hora)
    ORDER BY DATE(data_hora)
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $idUsuario, $primeiroDia, $ultimoDia);
$stmt->execute();
$res = $stmt->get_result();

$atividades = [];
while($row = $res->fetch_assoc()) {
    $dia = $row['dia']; 
    $atividades[$dia] = (int)$row['total']; // força inteiro
}
$stmt->close();

$days = [];
$period = new DatePeriod(
    new DateTime($primeiroDia),
    new DateInterval('P1D'),
    (new DateTime($ultimoDia))->modify('+1 day')
);

foreach($period as $date) {
    $diaStr = $date->format('Y-m-d');
    $days[$diaStr] = $atividades[$diaStr] ?? 0; // 0 se não houver ação
}
// Prepara rótulos dos meses
$mesLabels = [];
foreach ($days as $dia => $total) {
    $mes = date('M Y', strtotime($dia));
    $mesLabels[$mes][] = $dia;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fokus - Andamento</title>
    <link rel="stylesheet" href="/public/CSS/andamento.css">
    <link rel="stylesheet" href="/public/CSS/style.css">
    <link rel="stylesheet" href="/public/CSS/header/header.css">
     <link rel="stylesheet" href="/public/CSS/footer/footer.css">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-straight/css/uicons-regular-straight.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>
</head>


<body>
    <?php include __DIR__ . "/layout/header.php"; ?>

    <div class="background"></div>

    <main class="container">
        <h1>Progresso dos Estudos</h1>
        <p class="subtitle">Acompanhe sua evolução e desempenho ao longo do tempo</p>

        <!-- META SEMANAL -->
        <section class="card">
            <div class="meta-top">
                <h2>Meta Semanal</h2>
            </div>
            <!-- Barra de progresso da meta -->
            <div class="progress-bar">
                <span style="width: <?= $progressoGeral ?>%;"></span>
            </div>
            <div class="kpis">
                <div class="kpi"><strong><?= $progressoGeral ?>%</strong><span>Progresso Geral</span></div>
                <div class="kpi"><strong><?= $totalHorasSemana ?>h</strong><span>Total de Estudo</span></div>
                <div class="kpi"><strong><?= max($meta - $totalHorasSemana, 0) ?>h</strong><span>Restantes</span></div>
            </div>
        </section>

        <!-- PROGRESSO DIÁRIO -->
        <section class="card">
            <h2>Progresso Diário</h2>
            <p>Horas estudadas por dia desta semana</p>
            <div class="list">
                <?php
                $diasSemana = ["Seg", "Ter", "Qua", "Qui", "Sex", "Sáb", "Dom"];
                foreach ($diasSemana as $dia) {
                    $horas = $progressoDiario[$dia] ?? 0;
                    $largura = min(($horas / 6) * 100, 100); // meta 6h por dia
                    $classe = $horas > 0 ? "green" : "gray";
                    echo "
                <div class='item'>
                    <span>$dia</span><span>{$horas}h</span>
                    <div class='bar $classe'>
                        <span style='width:{$largura}%'></span>
                    </div>
                </div>
                ";
                }
                ?>
            </div>
        </section>
        <section class="card">
            <section class="grafico-meses">
                <h2>Contrubuições nos ultimos 6 meses</h2>
                <div class="meses-labels">
                    <?php foreach (array_keys($mesLabels) as $mes): ?>
                        <span class="mes-label"><?= $mes ?></span>
                    <?php endforeach; ?>
                </div>
                <div class="grid-meses">
                    <?php foreach ($days as $dia => $total): ?>
                        <?php $cor = $total > 0 ? 'ativo' : 'inativo'; ?>
                        <div class="dia <?= $cor ?>" title="<?= $dia ?> - <?= $total ?> ação(ões)"></div>
                    <?php endforeach; ?>
                </div>
            </section>
        </section>

        <!-- RODAPÉ -->
        <section class="card highlight">
            <h2>Excelente Progresso!</h2>
            <p>Você está muito bem para atingir sua meta. Continue assim 🚀</p>
            <div class="kpis">
                <div class="kpi"><strong><?= $totalHorasSemana ?>h</strong><span>Total Estudado</span></div>
                <div class="kpi"><strong><?= $progressoGeral ?>%</strong><span>Meta Alcançada</span></div>
                <div class="kpi"><strong>+15%</strong><span>vs Período Anterior</span></div>
            </div>
        </section>
    </main>
    <?php include __DIR__ . "/layout/footer.php"; ?>
    <!-- Bootstrap JS -->
    
    <!-- JS -->
    <script src="/src/js/darkTheme.js"></script>
    <script src="/src/js/background.js"></script>
</body>

</html>

</html>