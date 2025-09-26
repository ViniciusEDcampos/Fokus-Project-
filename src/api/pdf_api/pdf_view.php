<?php
declare(strict_types=1);
session_start();

if (empty($_SESSION['id_usuario'])) {
    http_response_code(401);
    exit("Não autenticado");
}

require __DIR__ . '/../../config/db.php';

$usuarioId = (int) $_SESSION['id_usuario'];
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    exit("ID inválido");
}

$sql = "SELECT conteudo, nome_arquivo FROM materiais_pdf WHERE id=? AND id_usuario=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $usuarioId);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

if (!$row) {
    http_response_code(404);
    exit("PDF não encontrado");
}

//visualizador nativo do navegador(a barra de opções que aparece acima do pdf)
header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename=\"" . basename($row['nome_arquivo']) . "\"");
echo $row['conteudo'];
