<?php
session_start();
require('../vendor/autoload.php');
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = $_POST["titulo"] ?? null;
    $descripcion = $_POST["descripcion"] ?? null;
    $colaboradores = $_POST["colaboradores"] ?? [];  // Obtener los colaboradores
    if (empty($titulo) || empty($descripcion)) {
        echo json_encode(["success" => false, "error" => "Datos incompletos"]);
        exit;
    }

    try {
        // Conexión con MongoDB Atlas
        $uri = 'mongodb+srv://ialfper:ialfper21@alumnos.zoinj.mongodb.net/?retryWrites=true&w=majority&appName=alumnos';
        $client = new MongoDB\Client($uri);     

        // Seleccionar base de datos y colecciones
        $db = $client->selectDatabase("kanva");
        $tareasCollection = $db->selectCollection("tareas");
        $usuariosCollection = $db->selectCollection("usuarios");

        // Obtener datos del creador
        $creadorId = $_SESSION["usuario_id"] ?? null;
        $creadorNombre = $_SESSION["usuario_nombre"] ?? "Desconocido";  // Asume que tienes el nombre en la sesión

        // Datos de la nueva tarea
        $nuevaTarea = [
            "titulo" => $titulo,
            "descripcion" => $descripcion,
            "creador" => [
                "id" => $creadorId,
                "nombre" => $creadorNombre
            ],
            "colaboradores" => []  // Almacenar los colaboradores con su ID y nombre
        ];

        // Obtener los nombres y IDs de los colaboradores
        foreach ($colaboradores as $colaboradorId) {
            $usuarioColaborador = $usuariosCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($colaboradorId)]);
            if ($usuarioColaborador) {
                $nuevaTarea['colaboradores'][] = [
                    "id" => (string)$usuarioColaborador["_id"],
                    "nombre" => $usuarioColaborador["nombre"]
                ];
            }
        }

        // Insertar la tarea en la base de datos
        $resultado = $tareasCollection->insertOne($nuevaTarea);

        if ($resultado->getInsertedCount() > 0) {
            echo json_encode(["success" => true, "message" => "Tarea creada con éxito"]);
        } else {
            echo json_encode(["success" => false, "error" => "No se pudo crear la tarea"]);
        }
    } catch (Exception $error) {
        error_log($error->getMessage()); // Registra el error para que puedas revisar los detalles en los logs del servidor
        http_response_code(500);
        echo json_encode(["success" => false, "error" => "Error del servidor: " . $error->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Método no permitido"]);
}
?>
