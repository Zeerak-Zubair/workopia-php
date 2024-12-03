<?php

/**
 * For helper functions to be used throughout the system
 */

/**
 * Get the base path
 * @param string path
 * @return string
 *  */
 function basePath($path = ''){
    return __DIR__. '/' . $path;
 } 


 /**
  * Load a View
  * @param mixed $name
  * @return void
  */
 function loadView($name, $data = []){
   $viewPath = basePath("App/views/{$name}.view.php");

   //inspectAndDie($name);
   //inspect($viewPath);

   if(file_exists($viewPath)){
      extract($data);
      require $viewPath;
   }
   else echo $name .'.view.php View does not exist! <br/>';
 }


 /**
  * Load a Partial view
  * @param mixed $name
  * @return string
  */
 function loadPartial($name){
   $partialPath = basePath("App/views/partials/{$name}.php");
   if(file_exists($partialPath)){
    require $partialPath;
   }
    else echo $name . '.php Partial does not exist! <br/>';
 }

  /**
  * Inspect a value(s)
  * @param mixed $value
  * @return void
  */
  function inspect($value){
    echo '<pre>';
    var_dump($value);
    //var_dump($value);
    echo '</pre>';
   }
  

 /**
  * Inspect a value(s) and die
  * @param mixed $value
  * @return void
  */
 function inspectAndDie($value){
  echo '<pre>';
  //var_dump($value);
  die(var_dump($value));
  echo '</pre>';
 }

 function formatSalary($salary){
  return '$' . number_format(floatval($salary));
 }

?>