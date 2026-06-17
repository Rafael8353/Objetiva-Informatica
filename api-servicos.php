<?php
header('Content-Type: application/json; charset=utf-8');

$db_host = ''; 
$db_name = '';
$db_user = '';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Busca na tabela SERVICOS
    $stmt = $pdo->query("SELECT * FROM servicos ORDER BY titulo ASC");
    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($servicos);

} catch(PDOException $e) {
    echo json_encode(['erro' => $e->getMessage()]);
}
?>