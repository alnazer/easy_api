<?php
    
    namespace Alnazer\Easyapi\System;
    
    use Alnazer\Easyapi\Exceptions\BadRequestException;
    use Alnazer\Easyapi\Exceptions\MethodNotAllowedException;

    /**
     * @property $namespace
     */
    class EndPoint extends Application
    {
        public static $routes;
        public Request $request;
        public  $controller;
        public  $action;
        public function __construct()
        {
            parent::__construct();
            $this->request = new Request();
            
        }
    
        public function setConfig($config)
        {
            $this->init($config);
            
        }
        public function getRoutes()
        {
           return self::$routes;
        }
        
        public static function  get($path, $callback)
        {
            if((new self())->prepare_path($path) !== false){
                self::$routes["get"][(new self())->prepare_path($path)] = (new self)->prepare_callback($callback);
            }
        }
        public static function  post($path, $callback)
        {
            if((new self())->prepare_path($path) !== false){
                self::$routes["post"][(new self())->prepare_path($path)] =  (new self)->prepare_callback($callback);
            }
        }
        public static function  put($path, $callback)
        {
            if((new self())->prepare_path($path) !== false){
                self::$routes["put"][(new self())->prepare_path($path)] =  (new self)->prepare_callback($callback);
            }
        }
        public static function  option($path, $callback)
        {
            if((new self())->prepare_path($path) !== false){
                self::$routes["option"][(new self())->prepare_path($path)] =  (new self)->prepare_callback($callback);
            }
        }
        public static function  delete($path, $callback)
        {
            if((new self())->prepare_path($path) !== false){
                self::$routes["delete"][(new self())->prepare_path($path)] =  (new self)->prepare_callback($callback);
            }
        }
        public static function  any($path, $callback)
        {
            if((new self())->prepare_path($path) !== false){
                self::$routes["any"][(new self())->prepare_path($path)] = (new self)->prepare_callback($callback);
            }
            
        }
        public function callEndPoint(){
            $headerAcceptType = $this->request->getHeader("Content-Type");
            if(!in_array($headerAcceptType, $this->request->allowAccept)){
                throw  new BadRequestException();
            }
            $this->getRoutes();
            $currency_route = $this->get_is_endpoint_available();
            if($currency_route !== false){
                switch ($currency_route['method']){
                    case "post":{
                        $this->request->isPost();
                    }
                        break;
                    case "get":{
                        $this->request->isGet();
                    }
                        break;
                    case "put":{
                        $this->request->isPut();
                    }
                        break;
                    case "option":{
                        $this->request->isOption();
                    }
                        break;
                    case "delete":{
                        $this->request->isDelete();
                    }
                        break;
                }
                $this->handelController($currency_route);
            
            }else{
                throw new MethodNotAllowedException("route not found",403);
            }
        }
    
        public function handelController($currency_route)
        {

            $classController = new Controller();
            $_controller = $currency_route["callback"][0];
            $classController->action = $currency_route["callback"][1] ?? "index";
            $classController->controller = $_controller;
            $this->action = $currency_route["callback"][1] ?? "index";
            $this->controller = $_controller;
            $this->setRouteDataToApplication();
            $controller = "$this->namespace\\Controller\\$_controller";
            $classController->callAction($this->namespace,$controller,$this->action, $currency_route['arguments']);

        }
        private function format_route($item){

            $_item = explode("/", $item);

            $_item = array_filter($_item);

            if(count($_item) == 1){
                return join("/",$_item)."/index";
            }
            return join("/",$_item);
        }
        private function getRouteAsOffset($route, $offset = 2){
            $route = $this->format_route($route);
            $explode_route = explode("/" ,$route);
            if(count($explode_route) >= $offset){
                return $explode_route[0]."/".$explode_route[1];
            }
            return $route;
        }
        private function formatArguments($replaceWith){
            $arguments = str_replace($replaceWith,"",$this->mainRoute);
            $arguments = $this->security->cleanInput($arguments);
            $arguments = str_replace("&#x2F;", "/" ,$arguments);
            $arguments = explode("/", $arguments);
            $arguments = array_filter($arguments);
            return array_map(function ($argument){
                return  $this->security->cleanInput($argument);
            },$arguments);
        }
        public function get_is_endpoint_available(){
            $arguments = [];
            if(self::$routes){
                foreach (self::$routes as $key => $value){
                    $actions = array_map(function ($item){ return trim($this->format_route($item), "/"); }, array_keys($value));

                    $RouteAsOffset = $this->getRouteAsOffset($this->mainRoute);
                    if(in_array($RouteAsOffset,$actions)){
                        $callback = $value[$this->getRouteAsOffset($this->mainRoute)];
                        $arguments = $this->formatArguments($RouteAsOffset);
                        $callback = $this->prepare_callback($callback);
                        if(is_string($callback)  && !strpos($callback, '@')){
                            $callback = [$callback,"index"];
                        }
                        if((is_array($callback) && count($callback) <= 1)){
                            $callback = array_merge($callback,["index"]);
                        }
                        if((is_array($callback) && count($callback) >= 2 && empty($callback[1]))){
                            $callback[1] = "index";
                        }
                        return ["method"=>$key,"route"=>$this->mainRoute,"callback" =>$callback,'arguments' => $arguments] ;
                    }
                }
                return false;
            }
        }
        private function prepare_path($path){

            if(is_array($path) && count($path) > 0){
                return join("/",$path);
            }elseif (is_string($path)){
                return trim($this->format_route($path),"/");
            }
            return false;
        }
        private function prepare_callback($callback){
            
            if(!is_array($callback) && strpos($callback, '@') !== false){
                return explode("@",$callback);
            }
 
            return $callback;
        }
        private function formatControllerName($name): string
        {
            return strtolower(str_replace("Controller","",$name));
        }

        private function setRouteDataToApplication()
        {
            Application::$app->controller = $this->formatControllerName($this->controller);
            Application::$app->action = strtolower($this->action);
            Application::$app->request->controller = Application::$app->controller;
            Application::$app->request->action = Application::$app->action;
        }
        public static function __callStatic($method, $arguments)
        {
            // TODO: Implement __callStatic() method.
           return (new self)->$method(...$arguments);
          
        }
    }