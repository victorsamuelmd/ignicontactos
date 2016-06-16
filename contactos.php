<?php
include_once('data_types.php');

session_start();

if (isset($_SESSION['username'])){
} else {
    header('Location: /contactos/autenticar.php');
}

$contactos = array(
    new Contacto('Victor', 'Mosquera'),
    new Contacto('Daniel', 'Mosquera'),
    new Contacto('Manuel', 'Lopez') 
    );

?>
<!doctype html>
<html>
<head>
    <title>Contactos</title>
</head>
<body>
<p>Bienvenido <?php echo $_SESSION['username'] ?></p>
    <h1>Contactos</h1>
    <ul>
    <?php foreach ($contactos as $contacto) {
        echo "<li>$contacto->nombres $contacto->apellidos ".
             "<a href=\"/contactos/contacto.php?id=$contacto->id\">Editar</a></li>";
    }
    ?>
    <ul>
</body>
</html>