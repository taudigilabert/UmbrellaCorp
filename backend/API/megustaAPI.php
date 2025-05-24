<?php
header('Content-Type: application/json'); 

require_once '../config.php';

$db = getDB();
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['imagen_id'])) {
    echo json_encode(["success" => false, "message" => "Faltan datos"]);
    exit;
}

$imagen_id = $data['imagen_id'];
$usuario = 'anonimo'; // DEMOMENTO SERA ANONIMO

// Comprobar si ya existe
$check = $db->prepare("SELECT id FROM megustas WHERE imagen_id = :imagen_id AND usuario = :usuario");
$check->execute([':imagen_id' => $imagen_id, ':usuario' => $usuario]);
$existing = $check->fetch();

if ($existing) {
    // Quitar Me Gusta
    $delete = $db->prepare("DELETE FROM megustas WHERE id = :id");
    $delete->execute([':id' => $existing['id']]);
    $estado = 'quitado';
} else {
    // Añadir Me Gusta
    $insert = $db->prepare("INSERT INTO megustas (imagen_id, usuario) VALUES (:imagen_id, :usuario)");
    $insert->execute([':imagen_id' => $imagen_id, ':usuario' => $usuario]);
    $estado = 'dado';
}

// Obtener total actualizado
$count = $db->prepare("SELECT COUNT(*) FROM megustas WHERE imagen_id = :imagen_id");
$count->execute([':imagen_id' => $imagen_id]);
$total = $count->fetchColumn();

// Utilizar $total en lugar de $nuevo_contador
echo json_encode([
    "success" => true,
    "megusta_actualizado" => $total,
    "estado" => $estado
]);
