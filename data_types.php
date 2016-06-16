<?php
/* Este archivo define los diferentes tipos de datos necesarios para el programa
 * usuarios
 * contactos
 */

 /**
  * Usuario
  */
class Usuario
 {
     private var $email;
     private var $username;
     private var $password;

     function __construct($username, $password, $email)
     {
         this->email = $email;
         this->username = $username;
         this->password = $password;
     }

  
 }
 
 /**
  * Contacto
  */
 class Contacto
 {
     
     function __construct($nombres, $apellidos)
     {
         this->nombres = $nombres;
         this->apellidos = $apellidos;
     }
 }