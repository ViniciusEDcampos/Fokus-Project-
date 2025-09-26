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

// 1) Consulta Ranking
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
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fokus - Ranking</title>
  <link rel="stylesheet" href="/public/CSS/style.css">
  <link rel="stylesheet" href="/public/CSS/ranking.css">
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

  <main class="container">
    <h1>🏆 Ranking de Usuários</h1>
    <p class="subtitle">Veja quem mais estudou desde o início da plataforma</p>

    <section class="ranking-card">
      <table class="ranking-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Usuário</th>
            <th>Total de Horas</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $pos = 1;
          foreach ($ranking as $user) {
            $nome = htmlspecialchars($user['nome']);
            $horas = number_format($user['total_horas'], 1, ',', '.');

            // Corrigido: usando as aspas corretamente para interpolação do PHP dentro da string
            $highlightClass = ($user['id_usuario'] == $idUsuario) ? 'highlight' : ''; // Variável para a classe

            echo "
        <tr class='$highlightClass'>
            <td>{$pos}º</td>
            <td><img src='/public/img/avatars/{$user['id_usuario']}.png' onerror='this.src=\"/public/img/default.png\"'> {$nome}</td>
            <td>
                <div class='progress' style='height: 10px;'>
                    <div class='progress-bar' style='width: " . ($user['total_horas'] * 5) . "%'></div>
                </div>
                <span>{$horas} h</span>
            </td>
        </tr>";

            $pos++;
          }

          ?>
        </tbody>
      </table>
    </section>
  </main>

  <script>
    // Função para atualizar o ranking via AJAX
    async function atualizarRanking() {
      const res = await fetch("ranking_api.php");
      const data = await res.json();
      const tbody = document.querySelector(".ranking-table tbody");
      tbody.innerHTML = "";
      let pos = 1;
      data.forEach(user => {
        let medalha = pos === 1 ? "🥇" : pos === 2 ? "🥈" : pos === 3 ? "🥉" : "";
        tbody.innerHTML += `
          <tr class="${user.id_usuario == <?= $idUsuario ?> ? 'highlight' : ''}">
            <td>${pos}º ${medalha}</td>
            <td><img src="/public/img/avatars/${user.id_usuario}.png" onerror="this.src='/public/img/default.png'"> ${user.nome}</td>
            <td>
              <div class="progress" style="height: 10px;">
                <div class="progress-bar" style="width:${user.total_horas * 5}%"></div>
              </div>
              <span>${user.total_horas.toFixed(1).replace('.', ',')} h</span>
            </td>
          </tr>`;
        pos++;
      });
    }

    // Atualizar o ranking a cada 30 segundos
    setInterval(atualizarRanking, 30000);
    window.onload = atualizarRanking;
  </script>
</body>

</html>