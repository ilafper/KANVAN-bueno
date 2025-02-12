<?php
header('Content-Type: application/json');

require('../vendor/autoload.php');

try {
    // Conectar a MongoDB Atlas
    $uri = 'mongodb+srv://ialfper:ialfper21@alumnos.zoinj.mongodb.net/?retryWrites=true&w=majority&appName=alumnos';
    $client = new MongoDB\Client($uri);
    $db = $client->kanva; // Base de datos
    $collection = $db->tareas; // Colección de tareas

    // Obtener los datos enviados en JSON
    $jsonData = file_get_contents("php://input");
    $data = json_decode($jsonData, true);

    if (!$data) {
        echo json_encode(["status" => "error", "message" => "No se recibieron datos"]);
        exit;
    }

    // Extraer los datos
    $id = isset($data['id']) ? $data['id'] : null;
    $titulo = isset($data['titulo']) ? $data['titulo'] : null;
    $descripcion = isset($data['descripcion']) ? $data['descripcion'] : null;
    $estado = isset($data['estado']) ? $data['estado'] : null;
    $creador = isset($data['creador']) ? $data['creador'] : null;
    $colaboradores = isset($data['colaboradores']) ? $data['colaboradores'] : [];

    // Verificar que todos los datos sean válidos
    if (!$id || !$titulo || !$descripcion || !$estado || !$creador) {
        echo json_encode(["status" => "error", "message" => "Faltan datos"]);
        exit;
    }

    // Convertir el ID a un ObjectId de MongoDB
    $objectId = new MongoDB\BSON\ObjectId($id);

    // Crear la actualización en MongoDB
    $resultado = $collection->updateOne(
        ['_id' => $objectId],
        ['$set' => [
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'estado' => $estado,
            'creador' => $creador,
            'colaboradores' => $colaboradores
        ]]
    );

    if ($resultado->getModifiedCount() > 0) {
        echo json_encode(["status" => "success", "message" => "Tarea actualizadaPatata"]);
    } else {
       
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
