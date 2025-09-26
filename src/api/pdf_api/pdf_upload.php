<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'msg' => 'Usuário não autenticado']);
    exit;
}
require __DIR__ . '/../../config/db.php';


$usuarioId = (int) $_SESSION['id_usuario'];

// Verifica se o arquivo foi enviado e devolve uma mensagem de erro caso não der certo
if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['ok' => false, 'msg' => 'Erro no upload']);
    exit;
}

//pegando os metadados que passei do arquivo pdf (Nome: nome original do arquivo que o usuario enviou, tmp: caminho completo )
$tmp     = $_FILES['pdf']['tmp_name'];
$nome    = $_FILES['pdf']['name'];
$tamanho = $_FILES['pdf']['size'] / (1024*1024); //essa conta converte para mb (1024*1024)
 // em MB (Caso o arquivo for de um tamanho muito grande, ira dar problema. 
 //(para alterar isso iria dar trabalho e eu teria que mecher no php.ini dentro do meu cmd :/ ))
$ext     = strtolower(pathinfo($nome, PATHINFO_EXTENSION));

// 🔎 valida só pela extensão
if ($ext !== 'pdf') {
    echo json_encode(['ok' => false, 'msg' => 'Somente PDFs são permitidos']);
    exit;
}

// Limite de tamanho (50 MB)
$maxMB = 50;
if ($_FILES['pdf']['size'] > $maxMB * 1024 * 1024) {
    echo json_encode(['ok' => false, 'msg' => 'Arquivo muito grande. Máx: '.$maxMB.' MB']);
    exit;
}

// Lê o conteúdo binário
$conteudo = file_get_contents($tmp);
if ($conteudo === false) {
    echo json_encode(['ok' => false, 'msg' => 'Falha ao ler o arquivo']);
    exit;
}

// Sempre PDF
$tipo = 'application/pdf';

// Insere no banco (com campo `tipo`)
$sql = "INSERT INTO materiais_pdf (id_usuario, nome_arquivo, tamanho_mb, conteudo, tipo, data_upload)
        VALUES (?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['ok' => false, 'msg' => 'Erro prepare', 'erro' => $conn->error]);
    exit;
}

// Modelo de como vai ser enviado para o banco (i: int, S: string, D:double)
$stmt->bind_param("isdss", $usuarioId, $nome, $tamanho, $conteudo, $tipo);
$stmt->send_long_data(3, $conteudo); //O BLOB(binario do pdf)
$ok = $stmt->execute(); 

//retorno caso haja algum erro ou problema 
if (!$ok) {
    echo json_encode(['ok' => false, 'msg' => 'Erro ao salvar', 'erro' => $conn->error]);
    exit;
}

// Retorno caso de certo
echo json_encode([
    'ok' => true,
    'msg' => 'PDF salvo com sucesso',
    'arquivo' => [
        'id' => $stmt->insert_id,
        'nome' => $nome,
        'tamanho' => round($tamanho, 2),
        'url' => '/src/api/pdf_api/pdf_visualizar.php?id=' . $stmt->insert_id
    ]
]);
