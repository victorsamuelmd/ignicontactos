<?php

session_set_cookie_params(3600 * 3);
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
use Igniweb\DB;


$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $db = new DB();
    $usuario_autentico = $db->validar_usuario($_POST['username'], $_POST['password']);

    if ($usuario_autentico){
        $_SESSION['username'] = $_POST['username'];
        if ($_POST['mantener']){
            setcookie('username', $_POST['username'], time() + 3600 * 3);
        } else {
            setcookie('username', $_POST['username']);
        }
        header('location: home.php');
    } else {
        $error = "No se pudo autenticar";
    }
}

?><!doctype html>
<html>
    <head>
        <title>Ignicontactos</title>
        <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <body class="pure-g">
            <div class="pure-u-1-3">
            </div>
            <div class="pure-u-1-3">
                <h1>Ingresar</h1>
                <form action="login.php" method="post" class="pure-form pure-form-stacked">
                    <input type="text" placeholder="username" name="username">
                    <input type="password" placeholder="Password" name="password">
                    <input type="submit" value="Autenticar" class="pure-button pure-button-primary">
                    <label for="mantener" class="pure-checkbox">
                        Mantener sesion:<input type="checkbox" name="mantener" value="1"/>
                    </label>
                </form>
                <p>
                    <?php echo $error; ?>
                </p>
                <p>No tiene cuenta: <a href="registro.php">Registrarse</a></p>
            </div>
            <div class="pure-u-1-3">
            </div>
        </body>
</html>
