<?php
session_start();
require 'conexion.php';  // Asegúrate de tener conexión a la BD

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tareaId = $_POST['tareaId'];
    $usuarioActual = $_SESSION['usuario_id'];
    $rolUsuario = $_SESSION['rol']; // admin o usuario normal

    try {
        $collection = $db->tareas;
        $tarea = $collection->findOne(['_id' => new MongoDB\BSON\ObjectID($tareaId)]);

        if (!$tarea) {
            echo json_encode(['success' => false, 'error' => 'Tarea no encontrada']);
            exit();
        }

        // Verificar si el usuario es el creador o tiene rol de admin
        if ($tarea['creador'] == $usuarioActual || $rolUsuario == "admin") {
            $result = $collection->deleteOne(['_id' => new MongoDB\BSON\ObjectID($tareaId)]);

            if ($result->getDeletedCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Tarea eliminada correctamente']);
            } else {
                echo json_encode(['success' => false, 'error' => 'No se pudo eliminar la tarea']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'No tienes permisos para eliminar esta tarea']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
    }
}
?>
