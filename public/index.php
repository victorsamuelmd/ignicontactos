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

    $map->get('user', '/user/{username}', function ($request, $response) use ($db) {
        $username = (string) $request->getAttribute('username');
        $user = $db->obtener_usuario($username);
        return new JsonResponse($user);
    });

    $map->post('contacto.nuevo', '/api/contacto/nuevo', function ($req, $res) use ($db) {
        $c = json_decode($req->getBody(), true);
        try {
            $contacto = new Contacto($c['nombres'], $c['apellidos'], $c['id_usuario']);
            return new JsonResponse($contacto);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse(array('error' => $e->getMessage()), 500);
        }
    });

    $map->post('usuario.nuevo', '/api/usuario/nuevo', function ($req, $res) use ($db) {
        $usr = json_decode($req->getBody(), true);
        try {
            $usuario = new Usuario($c['username'], $c['password'], $usr['email']);
            $db->guardar_usuario($usuario);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse(array('error' => $e->getMessage()), 500);
        }
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

main();

