<?php
session_start();

$data=array("usuario_nombre"=>$_SESSION["usuario_nombre"],"usuario_rol"=> $_SESSION["rol"]);

echo json_encode($data);




?>
