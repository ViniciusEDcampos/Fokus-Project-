<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['ok'=>false,'msg'=>'Não autenticado']);
    exit;
}
require __DIR__ . '/../config/db.php';

$usuarioId = (int) $_SESSION['id_usuario'];

// filtros opcionais
$fStatusUi = $_GET['status'] ?? '';     // pendente | feito
$fPriorUi  = $_GET['prioridade'] ?? ''; // Baixa | Média | Alta
$fMateria  = trim($_GET['materia'] ?? '');

// UI->DB
$statusDb = '';
if ($fStatusUi === 'pendente') $statusDb = 'pendente';
if ($fStatusUi === 'feito')    $statusDb = 'concluida';

$priorMap = ['Baixa'=>'baixa','Média'=>'media','Alta'=>'alta'];
$priorDb  = $priorMap[$fPriorUi] ?? '';

$where = ["id_usuario=?"];
$params = [$usuarioId];
$types  = "i";

if ($statusDb !== '') { $where[]="status=?";     $params[]=$statusDb; $types.="s"; }
if ($priorDb  !== '') { $where[]="prioridade=?"; $params[]=$priorDb;  $types.="s"; }
if ($fMateria !== '') { $where[]="materia LIKE ?"; $params[]="%$fMateria%"; $types.="s"; }

$sql = "SELECT id_tarefa, titulo, materia, prioridade, status, observacao, data_criacao,
               data_estudo, tempo_min
        FROM tarefas
        WHERE ".implode(' AND ', $where)."
        ORDER BY status, data_criacao DESC, id_tarefa DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'msg'=>'Erro prepare','erro'=>$conn->error]);
    exit;
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

$priorBack  = ['baixa'=>'Baixa','media'=>'Média','alta'=>'Alta'];
$statusBack = ['pendente'=>'pendente','concluida'=>'feito'];

$items = [];
while ($row = $res->fetch_assoc()) {
    $items[] = [
        'id'         => (int)$row['id_tarefa'],
        'titulo'     => $row['titulo'],
        'materia'    => $row['materia'],
        'prioridade' => $priorBack[$row['prioridade']] ?? 'Média',
        'status'     => $statusBack[$row['status']] ?? 'pendente',
        'observacao' => $row['observacao'],
        'data'       => $row['data_criacao'],
        'data_estudo'=> $row['data_estudo'],
        'tempo_min'  => $row['tempo_min'] !== null ? (int)$row['tempo_min'] : null,
    ];
}

// contadores
$sqlc = "SELECT
           COUNT(*) AS total,
           SUM(status='concluida') AS concluidas,
           SUM(status='pendente')  AS pendentes,
           SUM(prioridade='alta')  AS alta
         FROM tarefas WHERE id_usuario=?";
$stmtc = $conn->prepare($sqlc);
$stmtc->bind_param('i', $usuarioId);
$stmtc->execute();
$cnt = $stmtc->get_result()->fetch_assoc() ?? ['total'=>0,'concluidas'=>0,'pendentes'=>0,'alta'=>0];

echo json_encode([
    'ok'=>true,
    'items'=>$items,
    'meta'=>[
        'total'=>(int)$cnt['total'],
        'concluidas'=>(int)$cnt['concluidas'],
        'pendentes'=>(int)$cnt['pendentes'],
        'alta'=>(int)$cnt['alta'],
    ]
]);
