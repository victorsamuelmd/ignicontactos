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
    }
}
?><!doctype html>
<html>
<head>
<title>Ignicontactos | Registro</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<form action="registro.php" method="post">
<input type="text" placeholder="Nombre Usuario" name="username">
<input type="email" placeholder="Email" name="email">
<input type="password" placeholder="Password" name="password">
<input type="submit" value="Registrarse">
</form>
<?php if (isset($_GET['error'])) { ?>
    <p class="error"><?php echo $_GET['error']; ?></p>
<?php } ?>
</body>
</html>
