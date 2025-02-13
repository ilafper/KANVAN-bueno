<?php
session_start();
require('../vendor/autoload.php');

// Obtener datos JSON
$data = json_decode(file_get_contents('php://input'), true);

// Verifica si los datos están presentes
if (isset($data['id']) && isset($data['estado'])) {
    $id = $data['id'];
    $nuevoEstado = $data['estado'];

    try {
        // Conexión con MongoDB
        $uri = 'mongodb+srv://ialfper:ialfper21@alumnos.zoinj.mongodb.net/?retryWrites=true&w=majority&appName=alumnos';
        $client = new MongoDB\Client($uri);
        $db = $client->selectDatabase('kanva');
        $collection = $db->selectCollection('tareas');

        // Convertir el id a un ObjectId
        $objectId = new MongoDB\BSON\ObjectId($id);

        // Actualizar el estado de la tarea
        $result = $collection->updateOne(
            ['_id' => $objectId], // Filtra por ID
            ['$set' => ['estado' => $nuevoEstado]] // Actualiza el campo "estado"
        );

        if ($result->getModifiedCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Estado de la tarea actualizado con éxito']);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo actualizar el estado de la tarea']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error al conectar con MongoDB: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No se ha recibido el ID de la tarea o el nuevo estado']);
}
?>
