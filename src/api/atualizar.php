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

$usuarioId  = (int) $_SESSION['id_usuario'];
$id         = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'msg' => 'ID inválido']);
    exit;
}

// ===== Dados opcionais =====
$fields = [];
$params = [];
$types  = '';

if (isset($_POST['titulo'])) {
    $titulo = trim($_POST['titulo']);
    $fields[] = "titulo = NULLIF(?, '')";
    $params[] = $titulo;
    $types   .= 's';
}

if (isset($_POST['materia'])) {
    $materia = trim($_POST['materia']);
    $fields[] = "materia = NULLIF(?, '')";
    $params[] = $materia;
    $types   .= 's';
}

if (isset($_POST['observacao'])) {
    $observacao = trim($_POST['observacao']);
    $fields[] = "observacao = NULLIF(?, '')";
    $params[] = $observacao;
    $types   .= 's';
}

if (isset($_POST['prioridade'])) {
    $priorUi = $_POST['prioridade'];
    $priorMap = ['Baixa'=>'baixa','Média'=>'media','Alta'=>'alta'];
    $priorDb  = $priorMap[$priorUi] ?? '';
    if ($priorDb !== '') {
        $fields[] = "prioridade = ?";
        $params[] = $priorDb;
        $types   .= 's';
    }
}

if (empty($fields)) {
    echo json_encode(['ok' => false, 'msg' => 'Nenhum campo para atualizar']);
    exit;
}

// ===== Query dinâmica =====
$sql = "UPDATE tarefas SET " . implode(", ", $fields) . " WHERE id_tarefa=? AND id_usuario=?";
$params[] = $id;
$params[] = $usuarioId;
$types   .= 'ii';

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => 'Erro prepare', 'erro' => $conn->error]);
    exit;
}

$stmt->bind_param($types, ...$params);
$ok = $stmt->execute();

if (!$ok) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => 'Erro ao atualizar', 'erro' => $stmt->error]);
    exit;
}

echo json_encode([
    'ok'  => true,
    'msg' => 'Tarefa atualizada',
    'id'  => $id
]);
