<?php

include_once( __DIR__. '/../../vendor/autoload.php');
include_once( __DIR__. '/application.php');

function dispatch( Application $app ){

    $dispatcher = FastRoute\simpleDispatcher(
        function(FastRoute\RouteCollector $r) use ( $app ){
            $app->route( $r );
        }
    );

    $httpMethod = $_SERVER['REQUEST_METHOD'];
    $uri = preg_replace("/^\/api/", "",
    $_SERVER['REQUEST_URI']);

    if (false !== $pos = strpos($uri, '?')) {
        $uri = substr($uri, 0, $pos);
    }
    $uri = rawurldecode($uri);

    $routeInfo = $dispatcher->dispatch( $httpMethod, $uri );

    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            die('... 404 Not Found');
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];
            die('... 405 Method Not Allowed');
        case FastRoute\Dispatcher::FOUND:
            call_user_func_array(
                $routeInfo[1], array_values(
                $routeInfo[2])
            );
            break;
    }
}

?>
