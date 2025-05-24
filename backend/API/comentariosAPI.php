<?php
ob_clean();
header('Content-Type: application/json');
include '../config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getComentarios();
        break;
    case 'POST':
        addComentario();
        break;
    default:
        echo json_encode(["success" => false, "message" => "Método no soportado"]);
}

function getComentarios()
{
    $db = getDB();

    if (!isset($_GET['imagen_id'])) {
        echo json_encode(["success" => false, "message" => "Falta el parámetro imagen_id"]);
        return;
    }

    $imagen_id = $_GET['imagen_id'];

    $query = "SELECT * FROM comentarios WHERE imagen_id = :imagen_id ORDER BY fecha_comentario DESC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':imagen_id', $imagen_id, PDO::PARAM_INT);
    $stmt->execute();

    $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "comentarios" => $comentarios
    ]);
}

function addComentario()
{
    error_reporting(E_ERROR | E_PARSE);

    $db = getDB();
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['imagen_id'], $data['comentario'])) {
        echo json_encode(["success" => false, "message" => "Faltan datos"]);
        return;
    }

    $imagen_id = $data['imagen_id'];
    if (!is_numeric($imagen_id)) {
        echo json_encode(["success" => false, "message" => "ID de imagen inválido"]);
        return;
    }

    $comentario = trim($data['comentario']);
    if (empty($comentario)) {
        echo json_encode(["success" => false, "message" => "El comentario no puede estar vacío"]);
        return;
    }

    if (strlen($comentario) < 3) {
        echo json_encode(["success" => false, "message" => "El comentario debe tener al menos 3 caracteres"]);
        return;
    }

    $usuario = "Anónimo";

    try {
        $query = "INSERT INTO comentarios (imagen_id, comentario, usuario) VALUES (:imagen_id, :comentario, :usuario)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':imagen_id', $imagen_id);
        $stmt->bindParam(':comentario', $comentario);
        $stmt->bindParam(':usuario', $usuario);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Comentario añadido correctamente"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al insertar el comentario"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error en la base de datos: " . $e->getMessage()]);
    }
}
