<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Aura\Router\RouterContainer as RouterContainer;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Server;
use Igniweb\Usuario;

$routerContainer = new RouterContainer();

$map = $routerContainer->getMap();

$map->get('blog.read', '/blog/{id}', function ($request, $response) {
    $response = new Response();
    $id = (int) $request->getAttribute('id');
    $response->getBody()->write("You asked for blog entry {$id}.");
    return $response;
});

$map->get('home', '/home', function ($request, $response) {
    try {
        $usuario = new Usuario("victorsamuel", "$%&.,abcd1123", "victorsamuel@mail.com");
        $response = new JsonResponse($usuario);
    } catch (InvalidArgumentException $e) {
        $response = new JsonResponse($e->getMessage());
    }
    return $response;
});

$matcher = $routerContainer->getMatcher();

$request = Zend\Diactoros\ServerRequestFactory::fromGlobals(
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
