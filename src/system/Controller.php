<?php
    
    namespace Alnazer\Easyapi\System;
    
    use Alnazer\Easyapi\exceptions\BadActionCallException;
    use Alnazer\Easyapi\exceptions\BadClassCallException;

    class Controller extends  Application
    {
        public function beforeCall($action = []){
            return $action;
        }
        public function afterCall($action = []){
            return $action;
        }
        public function callAction($namespace,$controller,$action){
            $ex = explode("\\",$controller);
            $_controller = end($ex);
            
            if(class_exists($controller)){
                $controller = new $controller();
                if(method_exists($controller,$action) ){
                    if(is_callable([$controller,$action])){
                        $controller->beforeCall(['action'=>$action,"controller"=>$_controller,'namespace'=>$namespace,'route'=> (new self)->mainRoute]);
                        $action = $controller->$action();
                        $controller->afterCall(['action'=>$action,"controller"=>$_controller,'namespace'=>$namespace,'route'=> (new self)->mainRoute]);
                        echo $this->response->returnResponse($action);
                        exit();
                    }else{
                        throw new BadActionCallException("Action $action not found in class ".$namespace."/Controller/".$_controller." must be public");
                    }
            
                }else{
                    throw new BadActionCallException("Action $action not found in class ".$namespace."/Controller/".$_controller);
                }
            }else{
                throw new BadClassCallException("Class not found ".$namespace."/Controller/".$_controller);
            }
        }
    }