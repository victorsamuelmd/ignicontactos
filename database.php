<?php
header('Content-Type:text/html;charset=utf-8');

try {
    $db = new PDO('mysql:host=localhost;dbname=test','root');
} catch (PDOException $e) {
    print "No se conecta a la base de datos: $e";
}

include_once('data_types.php');

$db->exec("create table if not exists usuarios (
    username varchar(50),
    password varchar(256),
    email varchar(200)
);");


$db->exec("create table if not exists contactos (
    id int not null,
    nombres varchar(200) not null,
    apellidos varchar(200) not null,
    telefono varchar(20),
    email varchar(200),
    categoria varchar(40),
    fecha_nacimiento date,
    pais varchar(40),
    departamento varchar(40),
    ciudad varchar(40),
    direccion varchar(200),
    coordenadas varchar(30),
    notas varchar(700),
    id_usuario varchar(50) not null
);");

function guardar_usuario(Usuario $user, PDO $database){
    $stmt = $database->prepare("insert into usuarios (username,password,email) values (?,?,?)");
    $stmt->execute(array($user->username, $user->password, $user->email));
}

function guardar_contacto(Usuario $user, Contacto $contact, PDO $database){
    $stmt = $database->prepare("insert into contactos (id,nombre,apellidos,telefono,email,categoria,
        fecha_nacimiento,pais,departamento,ciudad,direccion,coordenadas,notas,id_usuario)
        values (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute();
}

guardar_usuario(new Usuario("victorsamuelmd", "algunapw", "email@gmail.com"), $db);