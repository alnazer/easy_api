<?php
    
    namespace Alnazer\Easyapi\System;

    use Alnazer\Easyapi\Exceptions\MethodNotAllowedException;
    

    class Request
    {
        public $request_method;
        /**
         * @var array|mixed
         */
        private $requestData;
        public Security $security;
        public array $allowAccept = ["application/json","application/xm","application/x-www-form-urlencoded"];
        public function __construct()
        {
            $this->request_method = $_SERVER["REQUEST_METHOD"];
            $this->security = new Security();
            $this->requestData = $this->setRequestData();
        }
    
        public function isGet(){
            if($this->request_method === "GET"){
                return true;
            }else{
                throw new MethodNotAllowedException();
            }
        }
        public function isPut(){
            if($this->request_method === "PUT"){
                return true;
            }else{
                throw new MethodNotAllowedException();
            }
        }
        public function isDelete(){
            if($this->request_method === "DELETE"){
                return true;
            }else{
                throw new MethodNotAllowedException();
            }
        }
        public function isOption(){
            if($this->request_method === "OPTION"){
                return true;
            }else{
                throw new MethodNotAllowedException();
            }
        }
        /**
         * @throws MethodNotAllowedException
         */
        public function isPost(){
            if($this->request_method === "POST"){
                return true;
            }else{
                throw new MethodNotAllowedException();
            }
            
        }
    
        /**
         * @param $name
         * @param $default
         * @return array|mixed|string|void|null
         */
        public function get($name = null, $default =null)
        {
            if(empty($name)){
                return $this->requestData;
            }
            if(isset($name) && isset($this->requestData[$name])){
                return $this->requestData[$name];
            }elseif(isset($name) && !isset($this->requestData[$name]) && !empty($default)){
                return $default;
            }elseif(isset($name) && !isset($this->requestData[$name]) && empty($default)){
                return $default;
            }
        }
        private function setRequestData(){
            $headerAcceptType = $this->getHeader("Content-Type");
            
            switch (strtolower($headerAcceptType)){
                case "application/json":
                case "application/xml":
                    return $this->security->cleanInput(json_decode( file_get_contents('php://input'), true));
                case "application/x-www-form-urlencoded":
                default:{
                    return  $this->security->cleanInput($_REQUEST);
                }
            }
            
        }
        public function getHeaders()
        {
            return apache_request_headers();
        }
        public function getHeader($name)
        {
            $headers = $this->getHeaders();
            if(in_array($name, array_keys($this->getHeaders()))){
                return $headers[$name];
            }
            return null;
        }
    
 
    }