<?php

/** 
* Get the base path

* @param string $path
* @return string

*/

function basePath($path = '')
{
    return __DIR__ . '/' . $path;
}

/**
 * Load a view
 * 
 * @param string $name
 * @return void
 */

 function loadView($name, $data = []) {
    $viewPath = basePath('App/views/' . $name . '.view.php');

    if(file_exists($viewPath)) {
        extract($data);
        require $viewPath;
    } else {
        echo 'View ' . $viewPath . ' not found!';
    }
    
}

/**
 * Load a partial
 * 
 * @param string $name
 * @return void
 */

 function loadPartial($name, $data = []) {
    $partialPath =  basePath('App/views/partials/' . $name . '.php');
    
    if(file_exists($partialPath)) {
        extract($data);
        require $partialPath;
    }else {
        echo 'View ' . $partialPath . ' not found!';
    }
}


/**
 * Inspect a value(s)
 * 
 * @param mixed $value
 * @return void
 */


 function inspect($value) {
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
 }
/**
 * Inspect a value(s) and die
 * 
 * @param mixed $value
 * @return void
 */


 function inspectAndDie($value) {
    echo '<pre>';
    echo var_dump($value);
    echo '</pre>';
    die();
 }

/**
 * Format Salary
 * 
 * @param string $salary
 * @return string Formatted Salary
 */

 function formatSalary($salary) {
    return '$' . number_format(floatval($salary));
 }

 /**
  * Sanitize Data
  * @param string $dirty
  * @return string 
  */

function sanitize($dirty){
    return htmlspecialchars(trim($dirty));
}

/**
 * Redirect to a given url
 * 
 * @param string $url
 * @return void
 */

 function redirect($url) {
    header("Location: " . $url);
    exit;
}

 