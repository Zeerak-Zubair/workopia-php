<?php

//Similar to npm
//where packages are stored in node_modules

//In composer for php 
//The modules are stored in `vendor`
require __DIR__.'/../vendor/autoload.php';
require '../helper.php';


use Framework\Router;

//custom autoloader
// spl_autoload_register(function($class){
//     $path = basePath('Framework/' . $class . '.php');
//     if(file_exists($path)){
//         require $path;
//     }
// });

//Instantiating the Router class
$router = new Router();

//Initializing the routes configured to specific URIs
$routes = require basePath('routes.php');

//obtaining the URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
//inspectAndDie($uri);// `http:localhost:8000/listing?id=2` -> string(8) "/listing"

//obtaining the REQUEST METHOD
$method = $_SERVER['REQUEST_METHOD'];

//Routing the requested URI and Method
$router->route($uri,$method);

?>