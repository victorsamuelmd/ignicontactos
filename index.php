<?php
session_start();

if (isset($_SESSION['username'])){
    header('Location: /contactos/contactos.php');
} else {
    header('Location: /contactos/autenticar.php');
}