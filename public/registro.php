<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
use Igniweb\DB;
use Igniweb\Usuario;

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $db = new DB();
    try {
        $usuario = new Usuario($_POST['username'], $_POST['password'], $_POST['email']);
        $exito = $db->guardar_usuario($usuario);
        if ($exito){
            $_SESSION['username'] = $usuario->get_username();
            setcookie('username', $_POST['username']);
            header('Location: home.php');
        }
    } catch (InvalidArgumentException $e){
        $_GET['error'] = $e->getMessage();
        http_response_code(401);
    }
    echo error_get_last();
}
?><!doctype html>
<html>
    <head>
        <title>Ignicontactos | Registro</title>
        <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
        <link rel="stylesheet" href="styles.css">
    </head>
    <body class="pure-g">
        <div class="pure-u-1-3">
        </div>
        <div class="pure-u-1-3">
            <h1>Registrarse</h1>
            <form action="registro.php" method="post" class="pure-form pure-form-stacked">
                <input type="text" placeholder="Nombre Usuario" name="username" pattern="[a-zA-Z0-9]{6,50}" id="username">
                <input type="email" placeholder="Email" name="email" id="email">

                <input type="password" 
                       id="password"
                       placeholder="Password"
                       name="password"
                       pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$">

                <input type="submit" value="Registrarse" class="pure-button pure-button-primary">
            </form>
        <?php if (isset($_GET['error'])) { ?>
            <p class="error"><?php echo $_GET['error']; ?></p>
        <?php } ?>
        </div>
        <div class="pure-u-1-3">
        </div>
    <script>
'use strict';

var usernameInput = document.getElementById('username');
usernameInput.oninvalid = function (event) {
    event.target.setCustomValidity('El nombre de usuario debe tener solo caracteres alfanuméricos, mínimo 6 caracteres');
}
var passwordInput = document.getElementById('password');
passwordInput.oninvalid = function (event) {
    event.target.setCustomValidity('La contraseña debe contener al menos 8 caracteres, con al menos una mayúscula, una letra minúscula, al menos un numero o un caracter especial');
}
</script>
    </body>
</html>
