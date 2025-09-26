<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'msg' => 'Usuário não autenticado']);
    exit;
}

require __DIR__ . '/../config/db.php';

$usuarioId = (int) $_SESSION['id_usuario'];
$id        = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$statusUi  = $_POST['status'] ?? ''; // 'feito' | 'pendente'

// ===== Validação =====
if (!$id || !in_array($statusUi, ['feito', 'pendente'], true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'msg' => 'Dados inválidos']);
    exit;
}

// ===== UI → DB =====
$statusDb = $statusUi === 'feito' ? 'concluida' : 'pendente';

// ===== Query =====
$sql = "UPDATE tarefas SET status=? WHERE id_tarefa=? AND id_usuario=?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => 'Erro prepare', 'erro' => $conn->error]);
    exit;
}

$stmt->bind_param('sii', $statusDb, $id, $usuarioId);
$ok = $stmt->execute();

// ===== Retorno =====
if (!$ok) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => 'Erro ao atualizar', 'erro' => $stmt->error]);
    exit;
}

echo json_encode([
    'ok'  => true,
    'msg' => 'Status atualizado',
    'id'  => $id,
    'status' => $statusUi // devolve no formato que o JS entende
]);
