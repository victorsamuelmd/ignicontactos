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
        http_status_code(401);
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
                <input type="text" placeholder="Nombre Usuario" name="username">
                <input type="email" placeholder="Email" name="email">
                <input type="password" placeholder="Password" name="password">
                <input type="submit" value="Registrarse" class="pure-button pure-button-primary">
            </form>
        <?php if (isset($_GET['error'])) { ?>
        <p class="error"><?php echo $_GET['error']; ?></p>
        <?php } ?>
        </div>
        <div class="pure-u-1-3">
        </div>
    </body>
</html>
