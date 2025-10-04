<?php
session_start();
require __DIR__ . '/../config/db.php';
 
$sql = "
  SELECT u.id_usuario, u.nome,
         COALESCE(SUM(s.duracao_segundos), 0) / 3600 AS total_horas
  FROM usuarios u
  LEFT JOIN sessoes_estudo s ON u.id_usuario = s.id_usuario
  GROUP BY u.id_usuario, u.nome
  ORDER BY total_horas DESC
  LIMIT 20
";
$res = $conn->query($sql);
 
$ranking = [];
while ($row = $res->fetch_assoc()) {
  $ranking[] = $row;
}
echo json_encode($ranking);
?>