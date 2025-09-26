<?php
declare(strict_types=1);
session_start();

// Sempre JSON
header('Content-Type: application/json; charset=utf-8');

// Se não logado → retorna JSON com 401
if (empty($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'msg' => 'Usuário não autenticado']);
    exit;
}

require __DIR__ . '/../config/db.php';

// ====== Sanitização dos dados ======
$usuarioId  = (int) $_SESSION['id_usuario'];
$titulo     = trim($_POST['titulo'] ?? '');
$materia    = trim($_POST['materia'] ?? '');
$observacao = trim($_POST['observacao'] ?? '');
$priorUi    = $_POST['prioridade'] ?? 'Baixa'; // UI: Baixa|Média|Alta
$dataEstudo = !empty($_POST['data_estudo']) ? $_POST['data_estudo'] : null; 
$tempoMin   = ($_POST['tempo_min'] ?? '') !== '' ? (int)$_POST['tempo_min'] : null;

if ($titulo === '') {
    echo json_encode(['ok' => false, 'msg' => 'Título é obrigatório']);
    exit;
}

// ====== Mapeamento prioridade ======
$priorMap = ['Baixa' => 'baixa', 'Média' => 'media', 'Alta' => 'alta'];
$priorDb  = $priorMap[$priorUi] ?? 'media';

// ====== Query ======
$sql = "INSERT INTO tarefas 
            (id_usuario, titulo, materia, prioridade, status, observacao, data_estudo, tempo_min)
        VALUES 
            (?, ?, NULLIF(?,''), ?, 'pendente', NULLIF(?,''), ?, ?)";
            

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => 'Erro na preparação da query', 'erro' => $conn->error]);
    exit;
}

$stmt->bind_param(
    'isssssi',
    $usuarioId,
    $titulo,
    $materia,
    $priorDb,
    $observacao,
    $dataEstudo,
    $tempoMin
);

$ok = $stmt->execute();

// ====== Resposta ======
if (!$ok) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => 'Erro ao salvar', 'erro' => $conn->error]);
    exit;
}

echo json_encode([
    'ok'  => true,
    'msg' => 'Tarefa criada',
    'id'  => $stmt->insert_id
]);
