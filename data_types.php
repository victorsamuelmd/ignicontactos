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
     private $email;
     private $username;
     private $password;

     function __construct($username, $password, $email)
     {
         if (usuario_valido($username, $password, $email)){
            $this->email = $email;
            $this->username = $username;
            $this->password = $password;
         } else {
             trigger_error("Existe error en los datos suministrados", E_USER_ERROR);
         }
     }

     private function usuario_valido($username, $password, $email)
     {
         if (filter_var($email, FILTER_VALIDATE_EMAIL) && validar_password($password) && validar_nombre($username)){
             return true;
         }
         return false;
     }
  
 }
 
 /**
  * Contacto
  */
 class Contacto
 {
     public static $cuenta = 0;
     var $nombres;
     var $apellidos;
     var $id;
     
     function __construct($nombres, $apellidos)
     {
         $this->nombres = $nombres;
         $this->apellidos = $apellidos;
         $this->id = self::$cuenta;
         self::$cuenta = self::$cuenta + 1;
     }
 }

 function validar_password($password)
 {
     return preg_match('/^[\w\d.?]{6,40}$/', $password);
 }

 function validar_nombre($nombres)
 {
     return preg_match('/^\w{2,50}/');
 }

 $contactos = array(
    new Contacto('Victor', 'Mosquera'),
    new Contacto('Daniel', 'Mosquera'),
    new Contacto('Manuel', 'Lopez') 
);