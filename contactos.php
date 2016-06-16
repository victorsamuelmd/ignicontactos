<?php
session_start();

if (isset($_SESSION['username'])){
} else {
    header('Location: /contactos/autenticar.php');
}


?>
<!doctype html>
<html>
<head>
    <title>Contactos</title>
</head>
<body>
<p>Bienvenido <?php echo $_SESSION['username'] ?></p>
    <h1>Contactos</title>
</body>
</html>