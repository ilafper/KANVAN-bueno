<?php
session_start();
require('../vendor/autoload.php');

// Obtener datos JSON
$data = json_decode(file_get_contents('php://input'), true);

// Verifica si los datos están presentes
if (isset($data['id']) && isset($data['titulo']) && isset($data['descripcion']) && isset($data['estado']) && isset($data['creador'])) {
    $id = $data['id'];
    $titulo = $data['titulo'];
    $descripcion = $data['descripcion'];
    $estado = $data['estado'];
    $creador = $data['creador'];
    $colaboradores = isset($data['colaboradores']) ? $data['colaboradores'] : [];

    try {
        // Conexión con MongoDB
        $uri = 'mongodb+srv://ialfper:ialfper21@alumnos.zoinj.mongodb.net/?retryWrites=true&w=majority&appName=alumnos';
        $client = new MongoDB\Client($uri);
        $db = $client->selectDatabase('kanva');
        $collection = $db->selectCollection('tareas');

        // Convertir el id a un ObjectId
        $objectId = new MongoDB\BSON\ObjectId($id);

        // Actualizar los datos de la tarea
        $result = $collection->updateOne(
            ['_id' => $objectId], // Filtra por ID
            ['$set' => [
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'estado' => $estado,
                'creador' => $creador,
                'colaboradores' => $colaboradores
            ]]
        );

        if ($result->getModifiedCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Tarea actualizada con éxito']);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo actualizar la tarea']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error al conectar con MongoDB: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Faltan datos necesarios']);
}
?>
