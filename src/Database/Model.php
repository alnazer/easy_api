<?php
    
    namespace Alnazer\Easyapi\Database;
    use \Illuminate\Database\Eloquent\Model as EloquentModel;
    class Model extends EloquentModel
    {
        public static function findBy($column, $value){
            return self::select()->from()->where($column,$value)->get();
        }
        public function __call($name, $arguments)
        {
            // TODO: Implement __call() method.
            if(strpos($name, "findBy") !== false){
                $name = strtolower(str_replace("findBy","",$name));
            
                if($name){
                    $arguments = array_merge([$name],$arguments);
                    return call_user_func_array([$this, "findBy"],$arguments);
                }
            }
        }
    
        public static function __callStatic($name, $arguments)
        {
            // TODO: Implement __callStatic() method.
            if(strpos($name, "findBy") !== false){
                pd();
                $name = strtolower(str_replace("findBy","",$name));
                if($name){
                    $arguments = array_merge([$name],$arguments);
                    return call_user_func_array([new static, "findBy"],$arguments);
                }
            }
        }
    }