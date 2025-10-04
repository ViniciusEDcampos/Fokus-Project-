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

// Consulta Ranking
$sql = "
  SELECT u.id_usuario, u.nome,
         COALESCE(SUM(s.duracao_segundos), 0) / 3600 AS total_horas
  FROM usuarios u
  LEFT JOIN sessoes_estudo s ON u.id_usuario = s.id_usuario
  GROUP BY u.id_usuario, u.nome
  ORDER BY total_horas DESC
  LIMIT 20
";
$res = $conn->query($sql);

$ranking = [];
while ($row = $res->fetch_assoc()) {
  $ranking[] = $row;
}

/* Estatísticas extras */

// Total de usuários
$res = $conn->query("SELECT COUNT(*) as total FROM usuarios");
$row = $res->fetch_assoc();
$totalUsuarios = $row['total'] ?? 0;

// Horas estudadas do usuário logado
$stmt = $conn->prepare("SELECT COALESCE(SUM(duracao_segundos),0)/3600 as horas
                        FROM sessoes_estudo
                        WHERE id_usuario = ?");
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$totalHoras = $row['horas'] ?? 0;

// Posição do usuário
$sqlPos = "
  SELECT COUNT(*)+1 as posicao
  FROM (
    SELECT u.id_usuario, SUM(s.duracao_segundos) AS total
    FROM usuarios u
    LEFT JOIN sessoes_estudo s ON u.id_usuario = s.id_usuario
    GROUP BY u.id_usuario
    HAVING total > (
      SELECT COALESCE(SUM(duracao_segundos),0)
      FROM sessoes_estudo WHERE id_usuario = $idUsuario
    )
  ) AS sub
";
$res = $conn->query($sqlPos);
$row = $res->fetch_assoc();
$posicaoUsuario = $row['posicao'] ?? $totalUsuarios;

// Frequência (dias seguidos)
$stmt = $conn->prepare("
  SELECT DISTINCT DATE(data_hora) AS dia
  FROM sessoes_estudo
  WHERE id_usuario = ?
  ORDER BY dia ASC
");
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$res = $stmt->get_result();

$datas = [];
while ($row = $res->fetch_assoc()) {
  $datas[] = $row['dia'];
}

$streak = 0;
$maxStreak = 0;
$atual = 0;
for ($i = 0; $i < count($datas); $i++) {
  if ($i === 0) {
    $atual = 1;
  } else {
    $diaAnterior = new DateTime($datas[$i - 1]);
    $diaAtual = new DateTime($datas[$i]);
    $diff = $diaAnterior->diff($diaAtual)->days;

    if ($diff === 1) {
      $atual++;
    } else {
      $maxStreak = max($maxStreak, $atual);
      $atual = 1;
    }
  }
}
$streak = $atual;
$maxStreak = max($maxStreak, $atual);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fokus - Ranking</title>
  <link rel="stylesheet" href="/public/CSS/style.css">
  <link rel="stylesheet" href="/public/CSS/ranking.css">
  <link rel="stylesheet" href="/public/CSS/dashbord.css">
  <link rel="stylesheet" href="/public/CSS/header/header.css">
  <link rel="stylesheet" href="/public/CSS/footer/footer.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-straight/css/uicons-regular-straight.css'>
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">



</head>

<body>
  <?php include __DIR__ . "/layout/header.php"; ?>
  <div class="background"></div>

  <!-- === Cards Estatísticas === -->
  <main class="ranking-container container">
    <!-- Coluna Esquerda -->
    <div class="ranking-col ranking-left-col">
      <!-- Cards Estatísticas -->
      <section class="stats-cards">
        <div class="card stat">
          <h3><?= $posicaoUsuario ?>º</h3>
          <p>Sua Posição</p>
        </div>
        <div class="card stat">
          <h3><?= $totalUsuarios ?></h3>
          <p>Participantes</p>
        </div>
        <div class="card stat">
          <h3><?= number_format($totalHoras, 1, ',', '.') ?>h</h3>
          <p>Suas Horas</p>
        </div>
        <div class="card stat">
          <h3><?= $streak ?></h3>
          <p>Dias Seguidos</p>
        </div>
      </section>

      <!-- Ranking de Usuários -->
      <section class="ranking-card">
        <h2>🏆 Ranking de Estudos</h2>
        <p class="subtitle">Posições baseadas em horas de estudo</p>
        <div class="ranking-list">
          <?php
          $pos = 1;
          foreach ($ranking as $user) {
            $nome  = htmlspecialchars($user['nome']);
            $xp = floor($user['total_horas'] * 100);
            $level = floor($xp / 1000);
            $xpAtual = $xp % 1000;
            $xpProgresso = ($xpAtual / 1000) * 100;

            $classe = "";
            if ($pos == 1) $classe = "primeiro";
            elseif ($pos == 2) $classe = "segundo";
            elseif ($pos == 3) $classe = "terceiro";

            $highlightClass = ($user['id_usuario'] == $idUsuario) ? 'highlight' : '';

            echo "
          <div class='ranking-item $classe $highlightClass'>
            <div class='ranking-left'>
              <img src='/public/img/astronauta.png' class='ranking-icon'>
              <div class='info'>
                <span class='nome'>$nome <span class='level'>Lv. $level</span></span>
                <span class='horas'>{$xp} XP</span>
                <div class='xp-bar'>
                  <div class='xp-bar-fill' style='width: {$xpProgresso}%;'></div>
                </div>
                <span class='xp-text'>{$xpAtual} / 1000 XP</span>
              </div>
            </div>
            <div class='ranking-right'>
              <span class='posicao'>{$pos}º</span>
            </div>
          </div>";
            $pos++;
          }
          ?>
        </div>
      </section>
    </div>

  </main>

   <?php include __DIR__ . "/layout/footer.php"; ?>

  <script>
    // 1) injeta o id do usuário logado e os dados atuais do ranking vindos do PHP
    const usuarioLogado = <?= (int)$idUsuario ?>;
    const dadosPHP = <?= json_encode($ranking, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    // 2) função de renderização única (usada pelo fallback e pela API)
    function renderRanking(data) {
      const container = document.querySelector(".ranking-list");
      if (!container) return;

      container.innerHTML = "";
      let pos = 1;

      data.forEach(user => {
        // Sanitiza nome para evitar quebrar o HTML
        const nomeSeguro = String(user.nome || "").replace(/[&<>"']/g, m => ({
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#39;'
        } [m]));

        let classe = "";
        let icone = ""; // imagem + fallback por emoji
        let nomeExibicao = nomeSeguro;

        if (pos === 1) {
          classe = "primeiro";
          icone = `<img src="/public/img/primeira.png" class="ranking-icon" onerror="this.replaceWith(document.createTextNode('🥇'))">`;
          nomeExibicao = `<span class="coroa">👑</span> ${nomeSeguro}`;
        } else if (pos === 2) {
          classe = "segundo";
          icone = `<img src="/public/img/segunda.png" class="ranking-icon" onerror="this.replaceWith(document.createTextNode('🥈'))">`;
        } else if (pos === 3) {
          classe = "terceiro";
          icone = `<img src="/public/img/terceira.png" class="ranking-icon" onerror="this.replaceWith(document.createTextNode('🥉'))">`;
        }

        // Cálculo de XP / Level
        const horas = Number(user.total_horas || 0);
        const xp = Math.floor(horas * 100);
        const level = Math.floor(xp / 1000);
        const xpAtual = xp % 1000;
        const xpProgresso = (xpAtual / 1000) * 100;

        const highlight = Number(user.id_usuario) === usuarioLogado ? 'highlight' : '';

        container.insertAdjacentHTML('beforeend', `
        <div class="ranking-item ${classe} ${highlight}">
          <div class="ranking-left">
            ${icone}
            <div class="info">
              <span class="nome">${nomeExibicao} <span class="level">Lv. ${level}</span></span>
              <span class="horas">${xp} XP</span>
              <div class="xp-bar">
                <div class="xp-bar-fill" style="width:${xpProgresso}%;"></div>
              </div>
              <span class="xp-text">${xpAtual} / 1000 XP</span>
            </div>
          </div>
          <div class="ranking-right">
            <span class="posicao">${pos}º</span>
          </div>
        </div>
      `);

        pos++;
      });
    }

    // 3) função que busca na API (se existir) e atualiza; caso falhe, fica com os dados do PHP
    async function atualizarRanking() {
      try {
        // ATENÇÃO: ajuste a rota para onde está seu endpoint de ranking!
        // Ex.: /src/api/ranking_api.php  (troque aqui se necessário)
        const res = await fetch('/src/api/ranking_api.php', {
          cache: 'no-store'
        });

        if (!res.ok) throw new Error('HTTP ' + res.status);
        const data = await res.json();

        if (!Array.isArray(data)) throw new Error('Resposta inválida da API');
        renderRanking(data);
      } catch (err) {
        console.warn('Falha ao carregar API. Usando dados do PHP. Detalhes:', err);
        renderRanking(dadosPHP);
      }
    }

    // 4) renderiza já com os dados do PHP e, em seguida, tenta atualizar pela API
    document.addEventListener('DOMContentLoaded', () => {
      renderRanking(dadosPHP); // aparece na hora
      atualizarRanking(); // tenta atualizar
    });
  </script>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/src/js/darkTheme.js"></script>
  <script src="/src/js/background.js"></script>
</body>

</html>