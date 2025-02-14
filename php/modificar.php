<?php
session_start();
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $tareaId = $data['id'];
    $titulo = $data['titulo'];
    $descripcion = $data['descripcion'];
    $estado = $data['estado'];
    $creador = $data['creador'];
    $colaboradores = $data['colaboradores'];

    $usuarioActual = $_SESSION['usuario_id'];
    $rolUsuario = $_SESSION['rol'];

    try {
        $collection = $db->tareas;
        $tarea = $collection->findOne(['_id' => new MongoDB\BSON\ObjectID($tareaId)]);

        if (!$tarea) {
            echo json_encode(['success' => false, 'error' => 'Tarea no encontrada']);
            exit();
        }

        if ($tarea['creador'] == $usuarioActual || $rolUsuario == "admin") {
            $updateResult = $collection->updateOne(
                ['_id' => new MongoDB\BSON\ObjectID($tareaId)],
                ['$set' => [
                    'titulo' => $titulo,
                    'descripcion' => $descripcion,
                    'estado' => $estado,
                    'colaboradores' => $colaboradores
                ]]
            );

            if ($updateResult->getModifiedCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Tarea actualizada']);
            } else {
                echo json_encode(['success' => false, 'error' => 'No se realizaron cambios']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'No tienes permisos para modificar esta tarea']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
    }
}
?>
