<?php 

// return [
//     '/' => 'controllers/home.php',
//     '/listings' => 'controllers/listings/index.php',
//     '/listings/create' => 'controllers/listings/create.php',
//     //'404' => 'controllers/error/404.php' // removed since we configured this path in the Controller Class
// ];


//We want to change the router so it can handle this:
$router->get('/','HomeController@index');
$router->get('/listings','ListingController@index');
$router->get('/listings/create','ListingController@create');
$router->get('/listing','ListingController@show');

//Populating the routes array in the $router object 
// $router->get('/','controllers/home.php');
// $router->get('/listings','controllers/listings/index.php');
// $router->get('/listings/create','controllers/listings/create.php');
// $router->get('/listing','controllers/listings/show.php');

?>