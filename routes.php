<?php 

// return [
//     '/' => 'controllers/home.php',
//     '/listings' => 'controllers/listings/index.php',
//     '/listings/create' => 'controllers/listings/create.php',
//     //'404' => 'controllers/error/404.php' // removed since we configured this path in the Controller Class
// ];

//Populating the routes array in the $router object 

$router->get('/','controllers/home.php');
$router->get('/listings','controllers/listings/index.php');
$router->get('/listings/create','controllers/listings/create.php');

?>