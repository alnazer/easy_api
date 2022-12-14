<?php
    
    namespace Alnazer\Easyapi\System;
    
    use Alnazer\Easyapi\Exceptions\BadActionCallException;
    use Alnazer\Easyapi\Exceptions\BadClassCallException;

    class Controller extends  Application
    {
        public  $controller;
        public  $action;
        public function behaviour($behaviours = []){
            return $this->executeBehaviours($behaviours);
        }
        public function beforeCall($action = []){
            return $action;
        }
        public function afterCall($action = []){
            return $action;
        }
        public function callAction($namespace, $controller, $action, $arguments = []){

            $ex = explode("\\",$controller);

            $_controller = end($ex);

            if(class_exists($controller)){

                $controller = new $controller;
                if(method_exists($controller,$action) ){

                    if(is_callable([$controller,$action])){
                        $controller->controller = $_controller;
                        $controller->action = $action;
                        $controller->behaviour();
                        $controller->beforeCall(['action'=>request()->action,"controller"=>request()->controller,'namespace'=>$namespace,'route'=> (new self)->mainRoute]);
                        $controller->registerAllEvent();
                        $action = $controller->$action(...$arguments);//call_user_func_array([$controller,$action],$arguments);

                        $controller->afterCall(['action'=>request()->action,"controller"=>request()->controller,'namespace'=>$namespace,'route'=> (new self)->mainRoute]);
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

        private function executeBehaviours($behaviours)
        {
            if(is_array($behaviours)){
                foreach ($behaviours as $key => $behaviour) {
                    try {
                        $className = "classClass_".$key;
                        $className = new $behaviour["class"];
                        unset($behaviour["class"]);
                        foreach ($behaviour as $attr_name => $attr_value) {
                            $className->$attr_name = $attr_value;
                        }
                        $className->execute();
                    }catch (\Exception $e){
                        throw new \Exception($e->getMessage(), $e->getCode());
                    }
                }
            }
            return $behaviours;
        }

    }