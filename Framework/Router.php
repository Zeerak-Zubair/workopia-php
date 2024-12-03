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

    class Router{
        protected $routes = [];

        private function registerRoute($method, $uri, $controller){
            $this->routes[] = [
                'method' => $method,
                'uri' => $uri,
                'controller' => $controller
            ];
        }

        /**
         * Add a GET Route
         * 
         * @param string $uri
         * @param string $controller
         * @return void
         */
        public function get($uri, $controller){
            $this->registerRoute('GET',$uri,$controller);
        }

        /**
         * Add a POST Route
         * @param mixed $uri
         * @param mixed $controller
         * @return void
         */
        public function post($uri, $controller){
            $this->registerRoute('POST',$uri,$controller);
        }
        
        /**
         * Add a PUT Route
         * @param mixed $uri
         * @param mixed $controller
         * @return void
         */
        public function put($uri, $controller){
            $this->registerRoute('PUT',$uri,$controller);
        }

        /**
         * Add a DELETE Route
         * @param mixed $uri
         * @param mixed $controller
         * @return void
         */
        public function delete($uri, $controller){
            $this->registerRoute('DELETE',$uri,$controller);
        }

        /**
         * Route a request
         * @param mixed $uri
         * @param mixed $method
         * @return void
         */
        public function route($uri, $method){
            foreach($this->routes as $route){
                if($route['uri'] === $uri && $route['method'] === $method){
                    require basePath('App/' .$route['controller']);
                    return;
                }
            }
            //Otherwise return 404
            $this->error();
        }

        /**
         * Load Error Page
         * @param mixed $httpCode
         * @return void
         */
        public function error($httpCode = 404){
            http_response_code($httpCode);
            loadView('error/'.$httpCode);
            exit;
        }

    }

?>