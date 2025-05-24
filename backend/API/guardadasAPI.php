<?php
header('Content-Type: application/json');
include '../config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'POST':
        addGuardada();
        break;
    default:
        echo json_encode(["message" => "Método no soportado"]);
}

function addGuardada() {
    $db = getDB();
    $data = json_decode(file_get_contents("php://input"), true);

    $imagen_id = $data['imagen_id'];
    $usuario_id = $data['usuario_id']; // null por ahora

    $query = "INSERT INTO guardadas (imagen_id, usuario_id) VALUES (:imagen_id, :usuario_id)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':imagen_id', $imagen_id);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->execute();

    echo json_encode(["message" => "Imagen guardada correctamente"]);
}
?>
