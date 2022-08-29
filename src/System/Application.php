<?php
    
    namespace Alnazer\Easyapi\System;
    
    use Alnazer\Easyapi\Database\Connection;
    use Alnazer\Easyapi\Exceptions\BadRequestException;
    use http\Exception\RuntimeException;

    class Application extends Configuration
    {

        public  $controller;
        public  $action;
        public $mainRoute = "";
        public $config;
        public Request $request;
        public Response $response;
        public Security $security;
        public  $db;
        public $user;
        protected array $eventListeners = [];
        public static Application $app;
        public function __construct(){
            
            $this->mainRoute = str_replace($_SERVER["SCRIPT_NAME"]."/","",$_SERVER['PHP_SELF']);

            if($this->mainRoute === $_SERVER["SCRIPT_NAME"]){
                $this->mainRoute = "/";
            }
            $this->request = new Request();
            $this->response = new Response();
            $this->security = new Security();

            $this->user = "";

        }

        public function init(array $config)
        {
            $this->config = $config;
            self::$app = new Application();
            self::$app->config = $config;
            self::$app->db = new Connection($config['db'] ?? []);
            self::$app->db = self::$app->db->connect();
            $this->handelConfig();
        }

        public function handelConfig()
        {
            if($this->config){
                foreach ($this->config as $var => $value){
                    $this->$var = $value;
                }
            }
        }

        public function run(array $config)
        {
            try{
                $this->init($config);
                $endpoint = new EndPoint();
                $endpoint->setConfig($this->config);
                $this->beforeRun();
                $endpoint->callEndPoint();
            }catch(\Exception|\PDOException|\RuntimeException|\ParseError|\ErrorException|\TypeError $e){
                $this->response->setCode($e->getCode());
                echo response()->returnResponse([
                    "code" => $e->getCode(),
                    "message" => $e->getMessage(),
                ]);
                exit();
            }

        }

        public function callEvent($eventName,...$event)
        {

            $callbacks = $this->eventListeners[$eventName] ?? [];

            foreach ($callbacks as $callback) {
                $arguments = $event ?? $callback['arguments'];
                call_user_func_array($callback['callback'],$arguments);
            }
        }
        public function  registerAllEvent(){

            $events = (isset(Application::$app->config['events'])) ? Application::$app->config['events'] : [];
            if(is_array(Application::$app->config['events']) && count(Application::$app->config['events']) > 0){
                foreach ($events as $eventName => $event) {
                    $this->runEventListener($eventName, [$event["class"],"run"], $event["event"]);
                }
            }
        }
        public function runEventListener($eventName, $callback, ...$arguments)
        {
            define($eventName,"$eventName");
            $this->eventListeners[$eventName][] = ['callback'=> $callback,'arguments' => $arguments];
        }

        private function beforeRun()
        {

        }
    }