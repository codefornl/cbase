<?php

//ini_set("error_reporting", E_ALL);
//ini_set("display_errors", 1);

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$config['displayErrorDetails'] = false;
$config['addContentLengthHeader'] = false;
$config['debug'] = false; // FIXME: getenv(DEBUG)

$config['db']['host'] = getenv(DB_HOST);
$config['db']['user'] = getenv(DB_USER);
$config['db']['pass'] = getenv(DB_PASS);
$config['db']['dbname'] = getenv(DB_NAME);

$config['root_pass'] = getenv(ROOT_PASS);

$app = new \Slim\App([
    "settings" => $config
]);

// enable CORS
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Content-type', 'application/hal+json,application/json')
            //->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Origin', $req->getHeader('Origin')) // FIXME ?
            ->withHeader('Access-Control-Allow-Credentials', 'true') // FIXME ?
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

// register containers
$container = $app->getContainer();
$container['db'] = function($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'] . ";charset=utf8",
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};
$container['handler'] = function($c) {
    $handler = new \Cbase\Handler($c['db'], $c['settings']['root_pass']);
    return $handler;
};

/**
 * GET /
 * 
 * Get api home.
 */
$app->get('/', function (Request $request, Response $response) {
    return $response->withJson([
        "service" => "collecties-api: an api for curated sets of use cases of open digital tools in government and civic tech",
        "about" => "https://www.codefor.nl/clarity",
        "codebase" => "https://github.com/codefornl/clarity",
        "application" => "https://collecties.codefor.nl",
        "browser" => "http://haltalk.herokuapp.com/explorer/browser.html#" . $request->getUri()->getBaseUrl(),
        "_links" => [
            "self" => [
                "href" => $request->getUri()->getBaseUrl() // FIXME http(s)?
            ],
            "cbases" => [
                "href" => $request->getUri()->getBaseUrl() . "/cbases"
            ],
            "usecases" => [
                "href" => $request->getUri()->getBaseUrl() . "/usecases"
            ]
        ]
    ]);
});

/**
 * GET /token
 * 
 * Get token pair.
 */
$app->get('/token', function (Request $request, Response $response) {
    return $response->withJson($this->handler->createTokenPair());
});

require('../private/routers/cbases.php');
require('../private/routers/usecases.php');

$app->run();
