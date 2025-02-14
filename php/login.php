<?php
session_start();
require('../vendor/autoload.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST["username"] ?? null;
    $contra = $_POST["password"] ?? null;

    if (empty($usuario) || empty($contra)) {
        header("location:../html/login.html");
        exit;
    }

    try {
        $uri = 'mongodb+srv://ialfper:ialfper21@alumnos.zoinj.mongodb.net/?retryWrites=true&w=majority&appName=alumnos';
        $client = new MongoDB\Client($uri);

        $db = $client->selectDatabase("kanva");
        $collection = $db->selectCollection("usuarios");

        $encontrado = $collection->findOne(["nombre" => $usuario]);

        if ($encontrado && $contra === $encontrado["password"]) {
            // Guardar el ID del usuario en la sesión
            $_SESSION["usuario_id"] = (string) $encontrado["_id"];  // Convertir el ObjectId a string
            $_SESSION["usuario_nombre"] = $encontrado["nombre"];
            $_SESSION["rol"] = $encontrado["rol"];
            header("location:../html/index.html");
        } else {
            header("location:../html/login.html");
        }
        exit;
    } catch (Exception $error) {
        http_response_code(500);
        echo "Error del servidor: " . $error->getMessage();
        exit;
    }
} else {
    header("location:../html/login.html");
    exit;
}

?>
