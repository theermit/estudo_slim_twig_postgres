<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

/*
    autor: benhur (benhur.azevedo@hotmail.com)
    utilidade: ponto de inicialização do app web
*/

// instantiate the App object

$config = \lib\config\Config::getConfig();

$appConfiguration = [
    "settings" => [
        "displayErrorDetails" => $config['ambiente_app'] == 'DESENVOLVIMENTO',
    ],
];
$c = new \Slim\Container($appConfiguration);
$app = new \Slim\App($c);

\lib\container\Container::setContainer($app);

\lib\routes\Route::setRoutes($app);

// $app->options('/{routes:.+}', function (Request $request, Response $response, array $args) {
//     return $response;
// });

// $app->add(function (Request $req, Response $res, callable $next) {
//     $config = \lib\config\Config::getConfig();
//     $response = $next($req, $res);
//     return $response
//             ->withHeader('Access-Control-Allow-Origin', $config['base_path'])
//             ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
//             ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
// });

// Catch-all route to serve a 404 Not Found page if none of the routes match
// NOTE: make sure this route is defined last
// $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function(Request $req, Response $res) {
//     $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
//     return $handler($req, $res);
// });

// Run application
$app->run();