<?php
/* Este archivo define los diferentes tipos de datos necesarios para el programa
 * usuarios
 * contactos
 */


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


