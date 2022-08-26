<?php
    
    namespace Alnazer\Easyapi\System;
    
    use Alnazer\Easyapi\database\Connection;
    use Alnazer\Easyapi\exceptions\BadRequestException;
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

        private function beforeRun()
        {

        }
    }