<?php

//We have moved the index.php to the public directory
//We aim to change the project root directory
//php -S localhost -t public

//We pasted the css and images folders in the public directory as well
require '../views/home.view.php';
require basePath('views/home.view.php');
require '../helper.php';

?>