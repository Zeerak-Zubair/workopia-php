<?php

//We have moved the index.php to the public directory
//We aim to change the project root directory
//php -S localhost -t public

//We pasted the css and images folders in the public directory as well

require '../helper.php';

//Necessary to create the Router object
require basePath('Framework/Router.php');

//To create the PDO
require basePath('Framework/Database.php');
// $config = require basePath('config/db.php');
// $db = new Database($config);

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