<?php
session_start();
include_once('data_types.php');


if(isset($_GET['id'])){
    echo $contactos[$_GET['id']]->nombres;
}