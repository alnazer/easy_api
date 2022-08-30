<?php
    
    namespace Alnazer\Easyapi\Database\old;
    
    use Alnazer\Easyapi\Database\Connection;
    use Alnazer\Easyapi\Exceptions\DatabaseQueryErrorException;
    use Alnazer\Easyapi\System\Application;

    class Model extends Connection implements QueryInterface
    {
        protected static string $query = "";
        protected static string $select = "";
        protected static string $where = "";
        protected static string $order = "";
        protected static string $limit = "";
        protected static string $from = "";
        protected static string $group = "";
        public static  string $tableName;
        protected static  array $execute = [];
        protected static $prepare;
        private static array $queryItemList = [
            "select",
            "from",
            'table',
            "where",
            "join",
            "order",
            "group",
            "limit",
        ];
        private static array $operations_marks = [
            "=",
            ">",
            "<",
            ">=",
            "<=",
            "<>",
            "!=",
            "like",
        ];


         /**
          * @return string
          */
         public static function getTableName(): string
         {
             return self::$tableName;
         }
        /**
         * @return string
         */
        /*public function getTableName(): string
        {


            $class = is_object($this) ? get_called_class($this) : $this;
            $name = basename(str_replace('\\', '/', $class));
            $path = explode('\\', $name);
            $name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', array_pop($path)));
            
            if (empty((new self)->tableName)) {
                self::$tableName = Inflector::pluralize($name);
            }
            return (new self)->tableName;
        }*/

         /**
          * @param string $tableName
          */
         public static function setTableName(string $tableName): void
         {
             self::$tableName = $tableName;
         }
        
        public static function primaryKey($id = "id")
        {
            return $id;
        }
        
        /**
         * @param string $query
         * @return $this
         */
        public static function select($query = "")
        {

            if (empty(self::$select)) {
                self::$select .= "SELECT ";
            }
            if (empty($query)) {
                self::$select .= " * ";
            }if (!empty($query) && is_string($query) && strpos(",", $query) !== false) {
                $query = explode(",",$query);
            }elseif(!empty($query) && is_string($query)){
                self::$select .= " $query ";
            }
            if (is_array($query) && count($query) > 0) {
                array_filter($query);
                $query= array_unique($query);
                $query = array_map('trim', $query);
                self::$select .= "`" . join("`,`", $query) . "` ";
            }

            return (new self);
        }
        
        public static function table($table){
            self::$tableName = $table;
            return (new self);
        }
    
        public static function from($table = null)
        {
            if (empty($table)) {
                $table = (new self)->getTableName();
            }
            self::$from = " FROM $table";
            return (new self);
        }
        
        public static function where($firstValue = "", $operation = "=", $sendValue = "")
        {


            if(is_string($firstValue) && !empty($sendValue)){
                (new self)->setWhere($firstValue, $sendValue, $operation);
            }elseif(is_string($firstValue) && empty($sendValue) && !empty($operation) && !in_array(strtolower($operation),self::$operations_marks)){
                (new self)->setWhere($firstValue, $operation, "=");
            }else{
                if (is_array($firstValue) && count($firstValue) > 0) {
                    if(count($firstValue) == 3){
                        (new self)->setWhere($firstValue[0], $firstValue[2], $firstValue[1]);
                    }else{
                        foreach ($firstValue as $column => $condition) {
                            (new self)->setWhere($column, $condition, $operation);
                        }
                    }

                }
            }

            return (new self);
        }
        
        public static function whereIn($column,$value)
        {

            if (count($value) > 0) {
               self::where([$column => $value]);
            }
            return (new self);
        }
        
        public static function like($column, $value, $strat = "",$end = ""){
            (new self)->setWhere($column, $strat.$value.$end,"LIKE");
            return (new self);
        }

        public static function order($column = null, $sort = "ASC")
        {
            if (empty($column)) {
                $column = self::primaryKey();
            }
            self::$order = " ORDER BY $column $sort ";
            return (new self);
        }
    
        public static function groupBy($columns)
        {
            $group = $columns;
            if(is_array($columns)){
                $group = join(" ,", $columns);
            }
            self::$group = " GROUP BY $group";
            return (new self);
        }
        
        public static function limit($limit = 20)
        {
            self::$limit = " LIMIT $limit";
            return (new self);
        }
        
        public static function query()
        {
            try {

                foreach (self::$queryItemList as $item) {
                    if (isset(self::$item)) {
                        if (empty(self::$item)) {
                            self::$item();
                        }
                    }
                }
                (new self)->formatWhere();
                self::$query = self::$select . self::$from . self::$where . self::$order . self::$limit;
                return (new self);

            } catch (\Exception $e) {
                throw  new DatabaseQueryErrorException($e->getMessage(), $e->getCode());
            }
        }

        public static function last()
        {
            try {
                self::limit(1);
                self::order(null, "DESC");
                return self::excute()->prepare->fetch();
            } catch (\Exception $e) {
                throw  new DatabaseQueryErrorException($e->getMessage(), $e->getCode());
            }
        }

        public static function first()
        {
            try {
                self::limit(1);
                return self::excute()->prepare->fetch();
            } catch (\Exception $e) {
                throw  new DatabaseQueryErrorException($e->getMessage(), $e->getCode());
            }
        }

        public static function one()
        {
            try {
                self::limit(1);
                return self::excute()->prepare->fetch();
            } catch (\Exception $e) {
                throw  new DatabaseQueryErrorException($e->getMessage(), $e->getCode());
            }
        }
        

        public static function all()
        {
            try {
                $rows = self::excute()->prepare->fetchAll();
                if ($rows) {
                    return $rows;
                }
                return [];
            } catch (\PDOException  $e) {
                throw new DatabaseQueryErrorException($e->getMessage(), (int)$e->getCode());
            }
        }

        public static function get()
        {
            try {
                $rows = self::excute()->prepare->fetchAll();
                if ($rows) {
                    return $rows;
                }
                return [];
            } catch (\PDOException  $e) {
                throw new DatabaseQueryErrorException($e->getMessage(), (int)$e->getCode());
            }
        }
    
        public static function count()
        {
            try {
                return self::excute()->prepare->rowCount();
            } catch (\Exception $e) {
                throw  new DatabaseQueryErrorException($e->getMessage(), $e->getCode());
            }
        }
        
        public static function exist($id=null)
        {
            if ($id) {
                self::$where = "";
                self::where([self::primaryKey() => $id]);
               // return self::count() > 0;
            }

            if(!empty(self::$where)){
                return self::count() > 0;
            }
            return false;
        }

        public static function insert($data)
        {
            
            try {
            
                self::$execute = [];
                $array_key = array_keys($data);
                $coulmes = join("`,`", $array_key);
                $values = join(",:", $array_key);
                foreach ($data as $key => $value) {
                    self::$execute[$key] = $value;
                }
                
                $query = "INSERT INTO `{self::getTableName()}` (`$coulmes`) VALUES (:$values)";
                self::excute($query);
                return true;
            } catch (\PDOException  $e) {
                throw new DatabaseQueryErrorException($e->getMessage(), (int)$e->getCode());
            }

        }

        public static function update($data = [], $conditions = [])
        {
            try {
                $_set = [];
                $SET = " SET ";
                foreach ($data as $key => $item) {
                    $_set[]="`$key`=:$key";
                }
                $SET.=join(", ",$_set);
                if($conditions){
                    self::where($conditions);
                }
                $sql = "UPDATE `{self::getTableName()}` $SET ".self::$where;
                self::$execute = array_merge($data,$conditions);
                return self::excute($sql);
            } catch (\PDOException  $e) {
                throw new DatabaseQueryErrorException($e->getMessage(), (int)$e->getCode());
            }

        }

        public static function delete($conditions = [])
        {
            try {

                self::$execute = [];
                if(count($conditions) > 0){
                    self::where($conditions);
                }
                (new self)->formatWhere();
                $query = "DELETE FROM `{self::getTableName()}` ".self::$where;
                self::excute($query);
                return true;
            } catch (\PDOException  $e) {
                throw new DatabaseQueryErrorException($e->getMessage(), (int)$e->getCode());
            }

        }

        public static function findBy($column, $value){
           return self::select()->from()->where($column,$value)->get();
        }

        private static function excute($query = null)
        {

            $query = (!$query) ? (new self)->query()::$query : $query;
            pd($query);
            self::$prepare = Application::$app->db->prepare($query);
            self::$prepare->execute(self::$execute);
            return (new self);
        }
    
         private function setConditionArray($column, $condition ,$symbol = "="){
             if(is_array($condition) && count($condition) > 0){
                 $condition = array_map("trim",$condition);
                 $column  = str_repeat('?,', count($condition) - 1) . '?';
                 $symbol = "IN";
             }
             return ["column"=> $column, "symbol"=> $symbol, "condition" => $condition];
         }
         
         private function setWhere($column, $condition,$symbol)
         {
             self::$execute[trim($column)] = (new self)->setConditionArray(trim($column), $condition,$symbol);
         }
         
         protected function formatWhere(){
             if(count(self::$execute) > 0){
                 self::$where .= " WHERE ";
             }
        
             foreach (self::$execute as $column => $item) {
                 if(trim($item['symbol']) === "IN"){
                     self::$where .= "`$column`".$item['symbol']." (".join(",",$item["condition"]).") AND ";
                     unset(self::$execute[$column]);
                 }else{
                     self::$where .= "`$column`".$item['symbol'].":".$item['column']." AND ";
                     self::$execute[$column] = $item["condition"];
                 }
             }
             // pd((new self)->execute);
             self::$where = rtrim(self::$where, "AND ");
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
               $name = strtolower(str_replace("findBy","",$name));
               if($name){
                   $arguments = array_merge([$name],$arguments);
                   return call_user_func_array([new static, "findBy"],$arguments);
               }
           }
        }
    
        
    }