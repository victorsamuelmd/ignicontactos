<?php

require_once __DIR__ . '/../vendor/autoload.php';
use Aura\Router\RouterContainer as RouterContainer;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Server;
use Igniweb\Usuario;
use Igniweb\Contacto;
use Igniweb\DB;

Twig_Autoloader::register();

function main()
{
    //Conexion a base de datos
    try {
        $db = new DB();
    } catch (PDOException $e) {
        print "No se conecta a la base de datos: $e";
    }

    $routerContainer = new RouterContainer();

    $map = $routerContainer->getMap();

    $map->get('get.usuario', '/usuario/{username}', function ($request, $response) use ($db) {
        $username = (string) $request->getAttribute('username');
        $user = $db->obtener_usuario($username);
        return new JsonResponse($user);
    });

    $map->post('post.usuario', '/usuario/nuevo', function ($req, $res) use ($db) {
        $usr = json_decode($req->getBody(), true);
        try {
            $usuario = new Usuario($usr['username'], $usr['password'], $usr['email']);
            return new JsonResponse($db->guardar_usuario($usuario));
        } catch (InvalidArgumentException $e) {
            return new JsonResponse(array('error' => $e->getMessage(), 'data' => $usr), 500);
        }
    });

    /*
     * CRUD para la tabla de contactos
     * */
    $map->get('get.contacto.todos', '/{username}/contacto/todos', function ($req, $res) use ($db) {
        $username = (string) $req->getAttribute('username');
        return new JsonResponse($db->obtener_contacto_todos($username));
    });

    $map->get('get.contacto', '/{username}/contacto/{id}', function ($req, $res) use ($db) {
        $username = (string) $req->getAttribute('username');
        $id = (int) $req->getAttribute('id');
        $contacto = $db->obtener_contacto($username, $id);
        if ($contacto){
            return new JsonResponse($contacto);
        } else {
            return new JsonResponse(array("error" => "El contacto con id $id no existe"), 404);
        }
    });

    $map->post('post.contacto', '/{username}/contacto/nuevo', function ($req, $res) use ($db) {
        $username = (string) $req->getAttribute('username');
        $c = json_decode($req->getBody(), true);
        try {
            $contacto = new Contacto($c['nombres'], $c['apellidos'], $username);
            $contacto->telefono = isset($c['telefono']) ? $c['telefono'] : null;
            $contacto->email = isset($c['email']) ? $c['email'] : null;
            $contacto->categoria = isset($c['categoria']) ? $c['categoria'] : null;
            $contacto->fecha_nacimiento = isset($c['fecha_nacimiento']) ? $c['fecha_nacimiento'] : null;
            $contacto->pais = isset($c['pais']) ? $c['pais'] : null;
            $contacto->departamento = isset($c['departamento']) ? $c['departamento'] : null;
            $contacto->ciudad = isset($c['ciudad']) ? $c['ciudad'] : null;
            $contacto->direccion = isset($c['direccion']) ? $c['direccion'] : null;
            $contacto->coordenadas = isset($c['coordenadas']) ? $c['coordenadas'] : null;
            $contacto->notas = isset($c['notas']) ? $c['notas'] : null;

            return new JsonResponse($db->guardar_contacto($contacto));
        } catch (InvalidArgumentException $e) {
            return new JsonResponse(array('error' => $e->getMessage()), 500);
        }
    });

    $map->put('put.contacto', '/{username}/contacto/{id}', function ($req, $res) use ($db) {
        $username = (string) $req->getAttribute('username');
        $id = (int) $req->getAttribute('id');
        $contacto = json_decode($req->getBody(), true);
        $stat = $db->actualizar_contacto($username, $id, $contacto);
        return new JsonResponse($stat);
    });

    $map->delete('delete.contacto', '/{username}/contacto/{id}', function ($req, $res) use ($db) {
        $username = (string) $req->getAttribute('username');
        $id = (int) $req->getAttribute('id');

        return new JsonResponse($db->borrar_contacto($username, $id));
    });

    $matcher = $routerContainer->getMatcher();

    $request = ServerRequestFactory::fromGlobals(
        $_SERVER,
        $_GET,
        $_POST,
        $_COOKIE,
        $_FILES
    );

    $route = $matcher->match($request);

    if (! $route) {
        // get the first of the best-available non-matched routes
        $failedRoute = $matcher->getFailedRoute();

        // which matching rule failed?
        switch ($failedRoute->failedRule) {
        case 'Aura\Router\Rule\Allows':
            // 405 METHOD NOT ALLOWED
            // Send the $failedRoute->allows as 'Allow:'
            break;
        case 'Aura\Router\Rule\Accepts':
            // 406 NOT ACCEPTABLE
            break;
        default:
            // 404 NOT FOUND
            echo "Not found";
            break;
        }

    } else {
        foreach ($route->attributes as $key => $val) {
            $request = $request->withAttribute($key, $val);
        }

        $server = Server::createServerFromRequest($route->handler, $request);
        $server->listen();
    }
}

session_start();
if (isset($_SESSION['username']) && $_SESSION['username'] != ''){
    main();
} else {
    echo json_encode(['error' => 'No autorizado']);
}

