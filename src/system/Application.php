<?php
    
    namespace Alnazer\Easyapi\System;
    
    use Alnazer\Easyapi\database\Connection;
    use Alnazer\Easyapi\exceptions\BadRequestException;

    class Application extends Configuration
    {
       
      
        public $mainRoute = "";
        public $config;
        public Request $request;
        public Response $response;
        public Security $security;
        public  $db;
        public static Application $app;
        public function __construct(){
            
            $this->mainRoute = str_replace($_SERVER["SCRIPT_NAME"]."/","",$_SERVER['PHP_SELF']);
            if($this->mainRoute === $_SERVER["SCRIPT_NAME"]){
                $this->mainRoute = "/";
            }
         
            $this->request = new Request();
            $this->response = new Response();
            $this->security = new Security();
        }
        public function init(array $config)
        {
            self::$app = new Application();
            $this->config = $config;
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
                $endpoint->callEndPoint();
            }catch(\Exception $e){
                $this->response->setCode($e->getCode());
                echo $this->response->returnResponse([
                    "code" => $e->getCode(),
                    "message" => $e->getMessage(),
                ]);
            }
        }
    
       
        
    
    
    }