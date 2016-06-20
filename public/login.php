<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
use Igniweb\DB;
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $db = new DB();
    $usuario_autentico = $db->validar_usuario($_POST['username'], $_POST['password']);
    if ($usuario_autentico){
        $_SESSION['username'] = $_POST['username'];
        setcookie('username', $_POST['username']);
        header('location: home.php');
    } else {
        $error = "No se pudo autenticar";
    }
}

?><!doctype html>
<?php if (true){ ?>
<html>
<head>
<title>Ignicontactos</title>
</head>
<body>
    <h1>Autenticar</h1>
<form action="login.php" method="post">
<input type="text" placeholder="username" name="username">
<input type="password" placeholder="Password" name="password">
<input type="submit" value="Autenticar">
</form>
<p><?php
echo $error;
?>
</p>
<p>No tiene cuenta: <a href="registro.php">Registrarse</a></p>
</body>
</html>
<?php } ?>
