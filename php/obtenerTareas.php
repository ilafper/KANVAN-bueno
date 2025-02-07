<?php
session_start();
require('../vendor/autoload.php');

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    try {
        // Conexión con MongoDB Atlas
        $uri = 'mongodb+srv://ialfper:ialfper21@alumnos.zoinj.mongodb.net/?retryWrites=true&w=majority&appName=alumnos';
        $client = new MongoDB\Client($uri);

        // Seleccionar base de datos y colección
        $db = $client->selectDatabase("kanva");
        $tareasCollection = $db->selectCollection("tareas");

        // Obtener el ID del usuario logueado
        $usuarioId = $_SESSION["usuario_id"] ?? null; // Asegúrate de tener el ID del usuario en la sesión

        if (!$usuarioId) {
            echo json_encode(["success" => false, "error" => "Usuario no autenticado"]);
            exit;
        }

        // Buscar tareas donde el usuario sea creador o colaborador
        $tareas = $tareasCollection->find([
            '$or' => [
                ['creador' => $usuarioId],
                ['colaboradores' => $usuarioId]
            ]
        ]);

        // Convertir el cursor a un array
        $tareasArray = iterator_to_array($tareas);

        // Retornar las tareas en formato JSON
        echo json_encode([
            "success" => true,
            "tareas" => $tareasArray
        ]);
    } catch (Exception $error) {
        http_response_code(500);
        echo json_encode(["success" => false, "error" => "Error del servidor: " . $error->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Método no permitido"]);
}
?>
