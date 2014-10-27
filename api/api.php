<?php

header('Content-Type: application/json');

mb_internal_encoding('utf-8');

require_once 'DB.php';
require_once 'Router.php';
require_once 'Api.php';

try
{
    DB::connect('127.0.0.1', 'root', '', 'calendar');

    $router = new Router();
    $api = new Api();
    $api->run($router->getAction());
}
catch (PDOException $e)
{
    echo json_encode(['pdo_exception' => $e->getMessage()]);
}