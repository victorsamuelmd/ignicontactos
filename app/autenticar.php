<?php
session_start();
if(isset($_POST['username'])){
    $_SESSION['username'] = $_POST['username'];
    header('Location: /contactos/contactos.php');
}
if(isset($_SESSION['username'])){
    header('Location: /contactos/contactos.php');
}

?>
<!doctype html>
<html>
<head>
    <title>Contactos</title>
    <meta charset="utf-8"/>
</head>
<body>
<form method="post" action="/contactos/autenticar.php">
    <input type="text" placeholder="Nombre de Usuario" name="username">
    <input type="password" placeholder="Contraseña" name="password">
    <input type="submit" value="Autenticar">
</form>
</body>
</html>