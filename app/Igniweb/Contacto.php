<?php namespace Igniweb;

use InvalidArgumentException;
/**
 * Class Contacto
 * @author victorsamuelmd
 */
class Contacto
{
    public $nombres;
    public $apellidos;
    public $telefono;
    public $email;
    public $categoria;
    public $fecha_nacimiento;
    public $pais;
    public $departamento;
    public $ciudad;
    public $direccion;
    public $coordenadas;
    public $notas;
    private $id_usuario;

    /**
     * Contrustructor de la clase, espera tres parámetros de tipo @string y
     * valida el tercer parámetro, este debe ser un nombre de usuario valido
     * que es una combinacion de letras y numeros de entre 2 y 50 caracteres.
     *
     * @return Contacto
     */
    public function __construct($nombres, $apellidos, $id_usuario)
    {
        if (preg_match('/^[\w\d]{2,50}/', $id_usuario)) {
            $this->nombres = $nombres;
            $this->apellidos = $apellidos;
            $this->id_usuario = $id_usuario;
        } else {
            throw new InvalidArgumentException("El nombre de usuario suministrado ($id_usuario) no es valido");
        }
    }
    
}
