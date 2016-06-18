<?php namespace Igniweb;

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
        $this->db = new \PDO('mysql:host=mysql;dbname=test',
            getenv('DB_USERNAME'), getenv('DB_PASSWORD'));

        $this->db->exec("create table if not exists usuarios (
            username varchar(50),
            password varchar(256),
            email varchar(200)
        );");

        $this->db->exec("create table if not exists contactos (
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
    }
    
    public function guardar_usuario(Usuario $user){
        if ( ! $this->obtener_usuario($user->asArray()['username'])) {
            $stmt = $this->db->prepare("insert into usuarios (username,password,email) 
                values (:username, :password, :email)");
            $stmt->execute($user->asArray());
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
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    

    public function guardar_contacto(Contacto $contact){
        $stmt = $this->db->prepare("insert into contactos (id,nombre,apellidos,
            telefono,email,categoria, fecha_nacimiento,pais,departamento,ciudad,
            direccion,coordenadas,notas,id_usuario)
            values (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute();
    }
}

