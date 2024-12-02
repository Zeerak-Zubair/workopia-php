<?php

//We have moved the index.php to the public directory
//We aim to change the project root directory
//php -S localhost -t public

//We pasted the css and images folders in the public directory as well

require '../helper.php';

//Necessary to create the Router object
require basePath('Router.php');

//Instantiating the Router class
$router = new Router();

//Initializing the routes configured to specific URIs
$routes = require basePath('routes.php');

//obtaining the URI
$uri = $_SERVER['REQUEST_URI'];
//obtaining the REQUEST METHOD
$method = $_SERVER['REQUEST_METHOD'];

//Routing the requested URI and Method
$router->route($uri,$method);

?>