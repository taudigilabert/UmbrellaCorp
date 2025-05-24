<?php
define('DB_PATH', __DIR__ . '/imagenes.db'); 

function getDB() {
    try {
        $db = new PDO('sqlite:' . DB_PATH);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        echo "Error al conectar con la base de datos: " . $e->getMessage();
        exit;
    }
}
?>
