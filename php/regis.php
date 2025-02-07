<?php
require '../vendor/autoload.php'; // Asegúrate de que la ruta es correcta

use MongoDB\Client;

$uri = 'mongodb+srv://ialfper:ialfper21@alumnos.zoinj.mongodb.net/?retryWrites=true&w=majority&appName=alumnos';
$client = new Client($uri); // Aquí creamos la instancia de MongoDB\Client
$db = $client->selectDatabase("kanva"); 
$collection = $db->selectCollection("usuarios");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $ap1 = trim($_POST['apellido']);
    $password = trim($_POST['password']);

    if (!empty($nombre) && !empty($ap1) && !empty($password)) {
        // Verificar si el usuario ya existe
        $usuarioExistente = $collection->findOne(['nombre' => $nombre, 'ap1' => $ap1]);
        if ($usuarioExistente) {
            echo "<script>alert('El usuario ya existe.'); window.location.href='../html/registrarse.html';</script>";
            exit;
        }

        // Crear usuario con la estructura correcta
        $usuario = [
            'nombre' => $nombre,
            'ap1' => $ap1,
            'rol' => 'user',
            'password' => $password // Sin encriptar (mejor usar password_hash)
        ];
        $collection->insertOne($usuario);

        echo "<script>alert('Registro exitoso. Ahora puedes iniciar sesión.'); window.location.href='../html/login.html';</script>";
    } else {
        echo "<script>alert('Por favor, completa todos los campos.'); window.location.href='../html/registro.html';</script>";
    }
}
?>
