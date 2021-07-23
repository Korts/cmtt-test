<?php

require 'vendor/autoload.php';

use App\Controllers\AdsController;
use App\Services\DatabaseConnector;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new DotEnv();
$dotenv->load(__DIR__ . '/.env');
$dbConnection = (new DatabaseConnector())->getConnection();

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/ads/relevant', 'get_ads');
    $r->addRoute('POST', '/ads/{id:\d+}', 'patch_ads');
    $r->addRoute('POST', '/ads', 'create_ads');
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        header("HTTP/1.1 404 Not Found");
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        header("HTTP/1.1 405 Not Found");
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $id = $routeInfo[2]['id'] ?? null;
        $adsController = new AdsController($dbConnection, $httpMethod, $id);
        $adsController->processRequest();
        break;
}
