<?php
session_start(); // Asegúrate de que la sesión esté activa para obtener el usuario logueado
require('../vendor/autoload.php');

try {
    // Conexión con MongoDB Atlas
    $uri = 'mongodb+srv://ialfper:ialfper21@alumnos.zoinj.mongodb.net/?retryWrites=true&w=majority&appName=alumnos';
    $client = new MongoDB\Client($uri);

    // Seleccionar base de datos y colección
    $db = $client->selectDatabase("kanva");
    $collection = $db->selectCollection("usuarios");

    // Obtener el ID del usuario logueado
    $usuarioLogueado = $_SESSION["usuario_id"] ?? null; // Suponiendo que el ID del usuario logueado está en la sesión

    if (!$usuarioLogueado) {
        echo json_encode(["success" => false, "error" => "Usuario no autenticado"]);
        exit;
    }

    // Obtener todos los usuarios y excluir al usuario logueado (creador)
    $usuarios = $collection->find([], ["projection" => ["_id" => 1, "nombre" => 1]]);
    $listaUsuarios = [];

    foreach ($usuarios as $usuario) {
        // Excluir al usuario logueado (creador) de la lista
        if ((string)$usuario["_id"] !== $usuarioLogueado) {
            $listaUsuarios[] = ["id" => (string)$usuario["_id"], "nombre" => $usuario["nombre"]];
        }
    }

    echo json_encode(["success" => true, "usuarios" => $listaUsuarios]);
} catch (Exception $error) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Error del servidor: " . $error->getMessage()]);
}
?>
