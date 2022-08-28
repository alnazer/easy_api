<?php
    
    namespace Alnazer\Easyapi\helpers;
    
    use Alnazer\Easyapi\database\Query;
    use Alnazer\Easyapi\database\Schema;
    use Alnazer\Easyapi\System\Application;

    class Cache
    {
        
        public $tableName = "cache";
        public $query;
        public Schema $schema;
        public function __construct()
        {
            $this->schema = new Schema();
            $this->schema->db =Application::$app->db;
            $tableExists = $this->schema->isTableExist($this->tableName);
            if($tableExists === false){
                $query = "CREATE TABLE IF NOT EXISTS $this->tableName(
                    id char(128)  PRIMARY KEY,
                    data BLOB NOT NULL,
                    expire int(11) NOT NULL,
                    parent_id int( 11 )  NULL);";
                $this->schema->createTable($this->tableName,$query);
            }
            $this->query = new Query();
            $this->query->tableName = $this->tableName;
        }
    
        /**
         * @throws \Exception
         */
        public function _add($data=[], $expire = 10, $parent_id = null){
            if($expire){
                pd($this->query->insert([
                    'id' => security()->getRandonKey(30),
                    "data" => serialize($data),
                    "expire" => time()+$expire,
                    "parent_id" => $parent_id
                ]));
            }else{
                throw new \Exception("You must insert expire time",403);
            }
            
        }
        public function _get($key,$parent = null){
            if($key){
                $get = $this->query->where(['id' => $key])->all();
                pd($get);
            }else{
                throw new \Exception("You must insert key ",403);
            }
        
        }
        public function __call($name, $arguments)
        {
            // TODO: Implement __call() method.
            $name = "_".$name;
            return call_user_func_array([$this,$name],$arguments);
        }
    
        public static function __callStatic($name, $arguments)
        {
            // TODO: Implement __callStatic() method.
            $name = "_" . $name;
            return call_user_func_array([new static,$name],$arguments);
        }
    }