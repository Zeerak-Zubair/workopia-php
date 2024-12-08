<?php

    // $routes = require basePath('routes.php');

    // if(array_key_exists($uri, $routes)){
    //     require basePath($routes[$uri]);
    // }else{
    //     //manipulating the http status code
    //     //since we are getting 200 

    //     http_response_code(404);
    //     require basePath($routes['404']);
    // }

namespace Framework;
use App\Controllers\ErrorController;
use Framework\middleware\Authorise;

class Router{
    protected $routes = [];

    // private function registerRoute($method, $uri, $controller){
        // $this->routes[] = [
        //     'method' => $method,
        //     'uri' => $uri,
        //     'controller' => $controller
        // ];
    // }

    /**
     * Register a Route
     * @param mixed $method
     * @param mixed $uri
     * @param mixed $action
     * @param array $middleware
     * @return void
     */
    public function registerRoute($method, $uri, $action, $middleware = []){
        list($controller, $controllerMethod) = explode('@',$action);
        //inspectAndDie($arr);
        
        //inspect($controller);
        //inspectAndDie($controllerMethod);

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'controllerMethod' => $controllerMethod,
            'middleware' => $middleware
        ];
        
    }

    /**
     * Add a GET Route
     * 
     * @param string $uri
     * @param array $middleware
     * @param string $controller
     * @return void
     */
    public function get($uri, $controller, $middleware = []){
        $this->registerRoute('GET',$uri,$controller, $middleware);
    }

    /**
     * Add a POST Route
     * @param mixed $uri
     * @param mixed $controller
     * @param array $middleware
     * @return void
     */
    public function post($uri, $controller, $middleware = []){
        $this->registerRoute('POST',$uri,$controller,$middleware);
    }
    
    /**
     * Add a PUT Route
     * @param mixed $uri
     * @param mixed $controller
     * @return void
     */
    public function put($uri, $controller, $middleware = []){
        $this->registerRoute('PUT',$uri,$controller,$middleware);
    }

    /**
     * Add a DELETE Route
     * @param mixed $uri
     * @param mixed $controller
     * @return void
     */
    public function delete($uri, $controller,$middleware = []){
        $this->registerRoute('DELETE',$uri,$controller,$middleware);
    }

    /**
     * Route a request
     * @param mixed $uri
     * @param mixed $method
     * @return void
     */
    public function route($uri){

        $requestMethod = $_SERVER['REQUEST_METHOD'];

        //Check for hidden request method

        if($requestMethod === 'POST' && isset($_POST['_method'])){
            //Override the request method
            
            $requestMethod = strtoupper($_POST['_method']);
            //inspectAndDie($requestMethod);
        }

        foreach($this->routes as $route){

            //Split the URI into segments
            $uriSegments = explode('/',trim($uri, '/'));
        

            //Split the route URI into segments
            $routeSegments = explode('/', trim($route['uri'],'/'));
        
            //Check if the number of segments matches
            if(count($uriSegments) === count($routeSegments) && strtoupper($route['method'] === $requestMethod)){
                $params = [];

                $match = true;

                for($i=0; $i < count($uriSegments); $i++){
                    // If the URI's do not match and there is no param
                    if($routeSegments[$i] !== $uriSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i])){
                        $match = false;
                        break;
                    }
                    //Check for the param and add to the params array
                    if(preg_match('/\{(.+?)\}/', $routeSegments[$i] ,$matches)){
                        $params[$matches[1]] = $uriSegments[$i];
                        //inspectAndDie($params);
                    }
                }

                if($match){
                    //inspect($route);
                    foreach($route['middleware'] as $middleware){
                        (new Authorise())->handle($middleware);
                    }

                    //Extract the controller and its method
                    $controller = 'App\\Controllers\\' . $route['controller'];
                    $controllerMethod = $route['controllerMethod'];

                    //inspect($controller);
                    //inspect($controllerMethod);

                    //Instantiate the controller and the method
                    $controllerInstance = new $controller();
                    $controllerInstance->$controllerMethod($params); 
                    return;
                }
            }
        }

            // if($route['uri'] === $uri && $route['method'] === $method){

            //     //Extract the controller and its method
            //     $controller = 'App\\Controllers\\' . $route['controller'];
            //     $controllerMethod = $route['controllerMethod'];

            //     //inspect($controller);
            //     //inspect($controllerMethod);

            //     //Instantiate the controller and the method
            //     $controllerInstance = new $controller();
            //     $controllerInstance->$controllerMethod(); 
            //     return;
            // }
            //}
        //Otherwise return 404
        //$this->error();
        ErrorController::notFound();
    }

    /**
     * Load Error Page
     * @param mixed $httpCode
     * @return void
     */
    // public function error($httpCode = 404){
    //     http_response_code($httpCode);
    //     loadView('error/'.$httpCode);
    //     exit;
    // }        

}

?>