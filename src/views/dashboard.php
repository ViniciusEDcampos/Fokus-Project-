<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="UTF-8"><title>Dashboard</title></head>
<body>
  <h1>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']); ?>!</h1>
  <a href="logout.php">Sair</a>
</body>
</html>