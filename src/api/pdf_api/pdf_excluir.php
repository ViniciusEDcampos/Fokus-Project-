<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['ok'=>false,'msg'=>'Usuário não autenticado']);
    exit;
}

require __DIR__ . '/../../config/db.php';

$usuarioId = (int) $_SESSION['id_usuario'];
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    echo json_encode(['ok'=>false,'msg'=>'ID inválido']);
    exit;
}

$sql = "DELETE FROM materiais_pdf WHERE id=? AND id_usuario=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $usuarioId);
$ok = $stmt->execute();

echo json_encode(['ok'=>$ok,'msg'=>$ok?'Excluído':'Nada excluído']);
