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

if (!$id) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'msg' => 'ID inválido']);
    exit;
}

$sql = "DELETE FROM tarefas WHERE id_tarefa=? AND id_usuario=?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => 'Erro prepare', 'erro' => $conn->error]);
    exit;
}

$stmt->bind_param('ii', $id, $usuarioId);
$ok = $stmt->execute();

if (!$ok) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => 'Erro ao excluir', 'erro' => $stmt->error]);
    exit;
}

if ($stmt->affected_rows > 0) {
    echo json_encode(['ok' => true, 'msg' => 'Tarefa excluída', 'id' => $id]);
} else {
    echo json_encode(['ok' => false, 'msg' => 'Nada alterado']);
}
