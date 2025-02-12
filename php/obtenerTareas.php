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
        $usuarioId = $_SESSION["usuario_id"] ?? null;

        if (!$usuarioId) {
            echo json_encode(["success" => false, "error" => "Usuario no autenticado"]);
            exit;
        }

        // Buscar tareas donde el usuario sea creador o colaborador
        $tareas = $tareasCollection->find([
            '$or' => [
                ['creador' => $_SESSION["usuario_nombre"]],  // Comparar con el nombre del creador
                ['colaboradores' => $_SESSION["usuario_nombre"]] // Comparar con la lista de nombres de colaboradores
            ]
        ]);

        // Convertir el cursor a un array con los datos necesarios
        $tareasArray = [];
        foreach ($tareas as $tarea) {
            $tareasArray[] = [
                "id" => (string) $tarea["_id"], 
                "estado"=>$tarea["estado"],
                "titulo" => $tarea["titulo"],
                "descripcion" => $tarea["descripcion"],
                "creador" => $tarea["creador"], // Directamente el nombre del creador
                "colaboradores" => $tarea["colaboradores"] ?? [] // Lista de strings con nombres de colaboradores
            ];
        }

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
