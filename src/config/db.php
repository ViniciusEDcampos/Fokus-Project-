<?php
$host = "localhost";
$user = "root";
$pass = "1234";       
$db   = "bancofoks"; 

$conn = new mysqli($host, $user, $pass, $db);

// Verifica se houve erro
if ($conn->connect_error) {
    die("Erro na conexão com o banco: " . $conn->connect_error);
}

// Opcional: define charset para evitar problema com acentos
$conn->set_charset("utf8mb4");
?>