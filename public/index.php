<?php

require __DIR__ . '/../vendor/autoload.php';

use Framework\Router;
use Framework\Session;

Session::start();

require '../helpers.php';


//Instantiating the router
 $router = new Router();

 //Get Routes
 $routes = require basePath('routes.php');
 
 //Get current URI and HTTP method
 $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
//  $method = $_SERVER['REQUEST_METHOD'];

 //Route the request
 $router->route($uri);

 
 
 
 