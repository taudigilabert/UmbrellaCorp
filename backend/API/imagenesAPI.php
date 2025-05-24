<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); // Permitir acceso desde cualquier dominio
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE"); // Métodos permitidos
header("Access-Control-Allow-Headers: Content-Type");


require_once '../config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getImagenes();
        break;
    case 'POST':
        addImagen();
        break;
    case 'DELETE':
        deleteImagen();
        break;
    default:
        echo json_encode(["message" => "Método no soportado"]);
}

//GET IMAGENES
function getImagenes()
{
    $db = getDB();

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 4; // Número de imágenes por página
    $offset = ($page - 1) * $limit;

    $query = "
        SELECT 
            i.id, 
            i.url, 
            i.descripcion,
            (SELECT COUNT(*) FROM valoraciones v WHERE v.imagen_id = i.id) AS megusta,
            (SELECT COUNT(*) FROM comentarios c WHERE c.imagen_id = i.id) AS comentarios
        FROM imagenes i
        ORDER BY i.id DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $imagenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($imagenes);
}



// AÑADIR IMAGEN
function addImagen()
{
    $db = getDB();

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image = $_FILES['image'] ?? null;
        $descripcion = htmlspecialchars($_POST['descripcion']);

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($image['type'], $allowedTypes)) {
            echo json_encode(["success" => false, "message" => "Tipo de archivo no permitido"]);
            return;
        }

        $imageName = uniqid() . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);

        $uploadDir = '../../imagenes/';
        $uploadFile = $uploadDir . $imageName;

        if (!is_writable($uploadDir)) {
            echo json_encode(["success" => false, "message" => "No se puede escribir en el directorio de imágenes"]);
            return;
        }

        // Mover el archivo a la carpeta imagenes
        if (move_uploaded_file($image['tmp_name'], $uploadFile)) {
            try {
                $query = "INSERT INTO imagenes (url, descripcion) VALUES (:url, :descripcion)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':url', $imageName);
                $stmt->bindParam(':descripcion', $descripcion);
                $stmt->execute();

                echo json_encode(["success" => true, "message" => "Imagen subida correctamente"]);
            } catch (PDOException $e) {
                echo json_encode(["success" => false, "message" => "Error al insertar en la base de datos: " . $e->getMessage()]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Error al mover el archivo"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "No se subió ninguna imagen o hubo un error"]);
    }
}

// ELIMINAR IMAGEN
function deleteImagen()
{
    $db = getDB();  // Asegúrate de que esta función devuelve la conexión correcta
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id'])) {
        $id = $data['id'];

        // Primero, obtener la URL de la imagen para eliminar el archivo físicamente
        $query = "SELECT url FROM imagenes WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            // Formar la ruta absoluta del archivo (usando $_SERVER['DOCUMENT_ROOT'])
            $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/M07/UF4/2425-daw2-uf4-a01-tomasaudi/imagenes/' . $image['url'];

            // Verificar si el archivo existe
            if (file_exists($imagePath)) {
                // Intentar eliminar el archivo
                if (unlink($imagePath)) {
                    // Eliminar la imagen de la base de datos
                    $query = "DELETE FROM imagenes WHERE id = :id";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        echo json_encode(["success" => true, "message" => "Imagen eliminada correctamente"]);
                    } else {
                        echo json_encode(["success" => false, "message" => "No se pudo eliminar la imagen de la base de datos"]);
                    }
                } else {
                    echo json_encode(["success" => false, "message" => "No se pudo eliminar el archivo físico"]);
                }
            } else {
                // Enviar un mensaje detallado de la ruta que no se encontró
                echo json_encode(["success" => false, "message" => "El archivo no existe en el servidor", "ruta" => $imagePath]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Imagen no encontrada"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Falta el parámetro id"]);
    }
}