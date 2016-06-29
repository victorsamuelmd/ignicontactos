<?php namespace Igniweb;

/*
 * Define la estructura de datos de un Usuario, tiene básicamente 3 campos en
 * para el email, un nombre de usuario (username) y una contraseña (password).
 * El constructor revisa al momento de intanciar la classe que los datos
 * suministrados sean válidos. En caso de no ser válidos se produce una
 * Excepción de tipo InvalidArgumentException
 * Implementa la Clase JsonSerializable para customizar la forma en que se pasa
 * a Json una instancia de la clase, así se puede esconder la contraseña.
 */
class Usuario implements \JsonSerializable
{
    private $email;
    private $username;
    private $password;

    public function __construct($username, $password, $email)
    {
        if ($this->usuario_valido($username, $password, $email)){
            $this->email = $email;
            $this->username = $username;
            $this->password = hash('sha256', $password);
        } else {
            throw new \InvalidArgumentException("Existen errores en los datos suministrados");
        }
    }

    private function usuario_valido($username, $password, $email)
    {
        $email_valido = filter_var($email, FILTER_VALIDATE_EMAIL);
        $password_valido = $this->validar_password($password);
        $username_valido = $this->validar_nombre($username);

        if ($email_valido && $password_valido && $username_valido) {
            return true;
        } else {
            return false;
        }
    }

    private function validar_password($password)
    {
        return preg_match('/^[\w\d.?$%&#*,]{6,40}$/', $password);
    }

    private function validar_nombre($nombres)
    {
        return preg_match('/^\w{2,50}/', $nombres);
    }

    public function jsonSerialize()
    {
        return array(
            "username" => $this->username,
            "email" => $this->email
        );
    }

    /**
     * Retorna el usuario en forma de array
     *
     * @return Array
     */
    public function asArray()
    {
        return array(
            "username" => $this->username,
            "password" => $this->password,
            "email" => $this->email
        );
    }

    /**
     * undocumented function
     *
     * @return string
     */
    public function get_username()
    {
        return $this->username;
    }
    
}
