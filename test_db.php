<?php
include "./src/config/db.php";

$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result) {
    echo "<h2>Conexão bem-sucedida com o banco <b>$db</b>!</h2>";
    echo "<p>Tabelas encontradas:</p><ul>";
    while ($row = $result->fetch_array()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
} else {
    echo "Conexão feita, mas não consegui listar as tabelas.";
}
?>
