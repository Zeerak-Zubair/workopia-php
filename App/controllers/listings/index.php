<?php

$config = require basePath('config/db.php');
$db = new Database($config);

$listings = $db->query('SELECT * FROM workopia.listings LIMIT 6')->fetchAll();

//inspect($listings); 

//The `extract` func will create a variable $listings using the key and it will be equal to the value assigned to it.
//This is how we pass data to it.

loadView('listings/index', [
    'listings' => $listings
]);

?>