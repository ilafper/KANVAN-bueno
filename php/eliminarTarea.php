<?php
// Iniciar la sesión
session_start();
require('../vendor/autoload.php');

// Verifica que se haya recibido un ID
if (isset($_POST['tareaId'])) { 
    $id = $_POST['tareaId'];
    try {
        // Conexión con MongoDB
        $uri = 'mongodb+srv://ialfper:ialfper21@alumnos.zoinj.mongodb.net/?retryWrites=true&w=majority&appName=alumnos';
        $client = new MongoDB\Client($uri);

        // Selección de base de datos y colección
        $db = $client->selectDatabase('kanva');  // Asegúrate de que esta es tu base de datos
        $collection = $db->selectCollection('tareas');  // Asegúrate de que esta es tu colección de tareas

        // Convertir el id a un ObjectId de MongoDB
        $objectId = new MongoDB\BSON\ObjectId($id);

        // Intentar eliminar la tarea con el ID
        $result = $collection->deleteOne(['_id' => $objectId]);
        
        if ($result->getDeletedCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Tarea eliminada con éxito']);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo eliminar la tarea']);
        }

    } catch (Exception $e) {
        // Si hay algún error en la conexión o eliminación
        echo json_encode(['success' => false, 'error' => 'Error al conectar con MongoDB: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No se ha recibido el ID de la tarea']);
}
?>
