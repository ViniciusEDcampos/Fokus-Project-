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

$sql = "SELECT id, nome_arquivo, tamanho_mb, data_upload 
        FROM materiais_pdf 
        WHERE id_usuario=? 
        ORDER BY data_upload DESC LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuarioId);
$stmt->execute();
$res = $stmt->get_result(); //pegando o resultado da consulta que foi feita no bancooooooooooo

//criando array para receber as informações
$items = [];
while ($row = $res->fetch_assoc()) {
    $items[] = [
        'id' => (int)$row['id'],
        'nome' => $row['nome_arquivo'],
        'tamanho' => $row['tamanho_mb'],
        'data' => $row['data_upload'],
        'url' => '/src/api/pdf_api/pdf_view.php?id=' . $row['id']
    ];
}

echo json_encode(['ok' => true, 'items' => $items]);
