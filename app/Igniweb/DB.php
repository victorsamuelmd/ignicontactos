<?php namespace Igniweb;

use PDO;
use InvalidArgumentException;
use function Functional\reduce_left;

/**
 * Class DB
 * @author victorsamuelmd
 */
class DB
{
    private $db;

    /**
     * Inicializa la base de datos estableciendo una conexion
     *
     * @return DB
     */
    public function __construct()
    {
        $this->db = new PDO('mysql:host=localhost;dbname=test', 'root');
        ////$this->db = new PDO('mysql:host=mariadb;dbname=homestead', 'homestead', 'secret');

    }

    /**
     * Crea las tablas necesarias para la aplicación, es suficiente con que se
     * llame una única vez.
     *
     * @return void
     */
    public function crear_tablas()
    {
        $this->db->exec("create table if not exists usuarios (
            username varchar(50) primary key,
            password varchar(256),
            email varchar(200) not null unique
        );");

        $this->db->exec("CREATE TABLE IF NOT EXISTS contactos (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nombres VARCHAR(200) NOT NULL,
            apellidos VARCHAR(200) NOT NULL,
            telefono VARCHAR(20),
            email VARCHAR(200),
            categoria VARCHAR(40),
            fecha_nacimiento DATE,
            pais VARCHAR(40),
            departamento VARCHAR(40),
            ciudad VARCHAR(40),
            direccion VARCHAR(200),
            coordenadas VARCHAR(30),
            notas VARCHAR(700),
            imagen VARCHAR(38) DEFAULT '',
            id_usuario VARCHAR(50) NOT NULL
        );");
    }
    
    
    /**
     * Crear un nuevo usuario que se pasa como primer parámetro a la función.
     * En caso de que halla un usuario con el mismo nombre de usuario genera un
     * error de tipo InvalidArgumentException.
     *
     * @param user Usuario
     * @return bool
     */
    public function guardar_usuario(Usuario $user){
        if ( ! $this->obtener_usuario($user->get_username())) {
            $stmt = $this->db->prepare("insert into usuarios (username,password,email) 
                values (:username, :password, :email)");
            return $stmt->execute($user->asArray());
        } else {
            throw new InvalidArgumentException("El usuario ya existe");
        }
    }

    /**
     * Devuelve un usuario si este existe en la base de datos, requiere el nombre
     * de usuario como primer parámetro.
     *
     * @return PDO query object
     */
    public function obtener_usuario($username)
    {
        $stmt = $this->db->prepare("select `username`, `email` 
                                    from usuarios
                                    where `username` = :username");
        $stmt->execute(array('username' => $username));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Devuelve si se encuentra un usuario con la combinacion correcta de
     * username y contraseña.
     * @param username string
     * @param password string
     * @return bool
     */
    public function validar_usuario($username, $password)
    {
        $stmt = $this->db->prepare("select `username` from usuarios
                                    where `username` = :username
                                    and `password` = :password");
        $stmt->execute(array(
            'username' => $username, 'password' => hash('sha256', $password)));
        return $stmt->fetch();
    }

    /**
     * Devuelve todos los contactos de un mismo usuario
     *
     * @return array<Contacto>
     */
    public function obtener_contacto_todos($username)
    {
        $stmt = $this->db->prepare("select 
            `nombres`, `apellidos`, `telefono`, `email`, `categoria`,
            `fecha_nacimiento`, `pais`, `departamento`, `ciudad`, `direccion`,
            `coordenadas`, `notas`, `id`, `imagen`
            FROM contactos WHERE `id_usuario` = :username");
        $stmt->execute(array('username' => $username));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Guarda el contacto pasado como primer argunmento, en la base de datos.
     * Devuelve el id del ultimo contacto creado
     *
     * @param contacto Contacto
     * @return Int
     */
    public function guardar_contacto(Contacto $contact){
        $keys = $this->envolver_elementos($contact->asArray(), '`', '`,');
        $values = $this->envolver_elementos($contact->asArray(), ':', ',');

        $sql = "insert into contactos ($keys) values ($values)";
        $stmt = $this->db->prepare($sql);
        if ($stmt->execute($contact->asArray())) {
            $last_id = $this->db->lastInsertId();
            return $last_id;
        }
        return null;
    }

    /**
     * Devuelve el usuario especificado con nombre de usuario y id, en caso
     * de que no exista, devuelve un error de tipo TODO
     *
     * @return Contacto
     */
    public function obtener_contacto($username, $id)
    {
        $stmt = $this->db->prepare("select * from contactos
            where `id` = :id and `id_usuario` = :id_usuario");
        $stmt->execute(array('id' => $id, 'id_usuario' => $username));
        $contacto = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($contacto) {
            return $contacto;
        } else {
            return null;
        }
    }
    
    /**
     * Borra el contacto especificado con nombre de usuario y id.
     *
     * @return void
     */
    public function borrar_contacto($username, $id)
    {
        $stmt = $this->db->prepare("delete from contactos
            where `id` = :id and `id_usuario` = :id_usuario");
        $stmt->execute(array('id' => $id, 'id_usuario' => $username));
    }

    /**
     * Actualiza los datos que se pasen en un array como tercer parametro
     * TODO: Esta funcion no es segura y permite injeccion de SQL
     *
     * @return void
     */
    public function actualizar_contacto($username, $id, Array $datos)
    {
        $str = trim(reduce_left($datos, function($value, $index, $collection, $reduction) {
            return $reduction . "`$index` = :$index,";
        }), ",");
        $datos['id'] = $id;
        $sql = "update contactos set $str where id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($datos);
    }
    
    
    private function envolver_elementos($list, $pre = '', $pos = '') {
        return trim(reduce_left($list, function($value, $index, $collection, $reduction)
            use ($pre, $pos) {
            return $reduction . $pre . $index . $pos;
        }), ",");
    }

}

