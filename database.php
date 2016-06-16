<?php
header('Content-Type:text/html;charset=utf-8');
try {
$db = new PDO('mysql:host=localhost;dbname=test','root');
echo "Connection success";
} catch (PDOException $e) {
    print "No se conecta a la base de datos: $e";
}

$db->exec("create table if not exists usuarios (
    username varchar(50),
    password varchar(256),
    email varchar(200)
)");