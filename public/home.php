<?php
session_start();

if(!isset($_SESSION['username']) || !isset($_COOKIE['username'])){
    header('Location: login.php');
}

include_once(__DIR__ . '/../app/app.html');
