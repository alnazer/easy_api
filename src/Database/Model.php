<?php
    
    namespace Alnazer\Easyapi\Database;
    
    use Alnazer\Easyapi\Exceptions\DatabaseQueryErrorException;
    use Alnazer\Easyapi\Helpers\Inflector;
    use Alnazer\Easyapi\System\Application;
    use mysql_xdevapi\Exception;

    abstract class Model extends Connection
    {
        public string $query = "";
        public string $select = "";
        public string $where = "";
        public string $order = "";
        public string $limit = "";
        public string $from = "";
        public string $tableName;
        public array $execute = [];
        public $prepare;
        private array $queryItemList = [
            "select",
            "from",
            "where",
            "join",
            "order",
            "group",
            "limit",
        ];
        private array $condition_marks = [
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
        public function gettableName(): string
        {
            $class = is_object($this) ? get_class($this) : $this;
            $name = basename(str_replace('\\', '/', $class));
            $path = explode('\\', $name);
            $name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', array_pop($path)));
            
            if (empty($this->tableName)) {
                $this->tableName = Inflector::pluralize($name);
            }
            return $this->tableName;
        }
        
        
        public function primaryKey($id = "id")
        {
            return $id;
        }

        /**
         * @param string $query
         * @return $this
         */
        public function _select($query = "")
        {

            if (empty($this->select)) {
                $this->select .= "SELECT ";
            }
            if (empty($query)) {
                $this->select .= " * ";
            }if (!empty($query) && is_string($query) && strpos(",", $query) !== false) {
                $query = explode(",",$query);
            }elseif(!empty($query) && is_string($query)){
                $this->select .= " $query ";
            }
            if (is_array($query) && count($query) > 0) {
                array_filter($query);
                $query= array_unique($query);
                $query = array_map('trim', $query);
                $this->select .= "`" . join("`,`", $query) . "` ";
            }

            return $this;
        }
        
        public function _where($firstValue = "", $mark = "=", $sendValue = "")
        {


            if(is_string($firstValue) && !empty($sendValue)){
                $this->setWhere($firstValue, $sendValue, $mark);
            }elseif(is_string($firstValue) && empty($sendValue) && !empty($mark) && !in_array(strtolower($mark),$this->condition_marks)){
                $this->setWhere($firstValue, $mark, "=");
            }else{
                if (is_array($firstValue) && count($firstValue) > 0) {
                    if(count($firstValue) == 3){
                        $this->setWhere($firstValue[0], $firstValue[2], $firstValue[1]);
                    }else{
                        foreach ($firstValue as $column => $condition) {
                            $this->setWhere($column, $condition, $mark);
                        }
                    }

                }
            }

            return $this;
        }
        public function _whereIn($column,$value)
        {

            if (count($value) > 0) {
               $this->_where([$column => $value]);
            }
            return $this;
        }
        public function _like($column, $value, $strat = "",$end = ""){
            $this->setWhere($column, $strat.$value.$end,"LIKE");
            return $this;
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
            $this->execute[trim($column)] = $this->setConditionArray(trim($column), $condition,$symbol);
        }
        protected function formatWhere(){
            if(count($this->execute) > 0){
                $this->where .= " WHERE ";
            }

            foreach ($this->execute as $column => $item) {
                if(trim($item['symbol']) === "IN"){
                    $this->where .= "`$column`".$item['symbol']." (".join(",",$item["condition"]).") AND ";
                    unset($this->execute[$column]);
                }else{
                    $this->where .= "`$column`".$item['symbol'].":".$item['column']." AND ";
                    $this->execute[$column] = $item["condition"];
                }
            }
           // pd($this->execute);
            $this->where = rtrim($this->where, "AND ");
        }
        public function _order($column = null, $sort = "ASC")
        {
            if (empty($column)) {
                $column = $this->primaryKey();
            }
            $this->order = " ORDER BY $column $sort ";
            return $this;
        }
        
        public function _from($table = null)
        {
            if (empty($table)) {
                $table = $this->gettableName();
            }
            $this->from = " FROM $table";
            return $this;
        }
        
        public function _limit($limit = 20)
        {
            $this->limit = " LIMIT $limit";
            return $this;
        }
        
        public function _query()
        {
            try {

                foreach ($this->queryItemList as $item) {
                    if (isset($this->$item)) {
                        if (empty($this->$item)) {
                            $this->$item();
                        }
                    }
                }
                $this->formatWhere();
                $this->query = $this->select . $this->from . $this->where . $this->order . $this->limit;
                return $this;

            } catch (\Exception $e) {
                throw  new DatabaseQueryErrorException($e->getMessage(), $e->getCode());
            }
        }

        public function _last()
        {
            try {
                $this->limit(1);
                $this->order(null, "DESC");
                return $this->excute()->prepare->fetch();
            } catch (\Exception $e) {
                throw  new DatabaseQueryErrorException($e->getMessage(), $e->getCode());
            }
        }

        public function _first()
        {
            try {
                $this->limit(1);
                return $this->excute()->prepare->fetch();
            } catch (\Exception $e) {
                throw  new DatabaseQueryErrorException($e->getMessage(), $e->getCode());
            }
        }

        public function _one()
        {
            try {
                $this->limit(1);
                return $this->excute()->prepare->fetch();
            } catch (\Exception $e) {
                throw  new DatabaseQueryErrorException($e->getMessage(), $e->getCode());
            }
        }

        public function _count()
        {
            try {
                return $this->excute()->prepare->rowCount();
            } catch (\Exception $e) {
                throw  new DatabaseQueryErrorException($e->getMessage(), $e->getCode());
            }
        }

        public function _all()
        {
            try {
                $rows = $this->excute()->prepare->fetchAll();
                if ($rows) {
                    return $rows;
                }
                return [];
            } catch (\PDOException  $e) {
                throw new DatabaseQueryErrorException($e->getMessage(), (int)$e->getCode());
            }
        }

        public function _get()
        {
            try {
                $rows = $this->excute()->prepare->fetchAll();
                if ($rows) {
                    return $rows;
                }
                return [];
            } catch (\PDOException  $e) {
                throw new DatabaseQueryErrorException($e->getMessage(), (int)$e->getCode());
            }
        }

        public function _exist($id=null)
        {
            if ($id) {
                $this->where = "";
                $this->where([$this->primaryKey() => $id]);
               // return $this->_count() > 0;
            }

            if(!empty($this->where)){
                return $this->_count() > 0;
            }
            return false;
        }

        public function _insert($data)
        {
            try {
                $this->execute = [];
                $array_key = array_keys($data);
                $coulmes = join("`,`", $array_key);
                $values = join(",:", $array_key);
                foreach ($data as $key => $value) {
                    $this->execute[$key] = $value;
                }
                $query = "INSERT INTO `$this->tableName` (`$coulmes`) VALUES (:$values)";
                $this->excute($query);
                return true;
            } catch (\PDOException  $e) {
                throw new DatabaseQueryErrorException($e->getMessage(), (int)$e->getCode());
            }

        }

        public function _update($data = [], $conditions = [])
        {
            try {
                $_set = [];
                $SET = " SET ";
                foreach ($data as $key => $item) {
                    $_set[]="`$key`=:$key";
                }
                $SET.=join(", ",$_set);
                if($conditions){
                    $this->where($conditions);
                }
                $sql = "UPDATE `$this->tableName` $SET ".$this->where;
                $this->execute = array_merge($data,$conditions);
                return $this->excute($sql);
            } catch (\PDOException  $e) {
                throw new DatabaseQueryErrorException($e->getMessage(), (int)$e->getCode());
            }

        }

        public function _delete($conditions = [])
        {
            try {

                $this->execute = [];
                if(count($conditions) > 0){
                    $this->_where($conditions);
                }
                $this->formatWhere();
                $query = "DELETE FROM `$this->tableName` ".$this->where;
                $this->excute($query);
                return true;
            } catch (\PDOException  $e) {
                throw new DatabaseQueryErrorException($e->getMessage(), (int)$e->getCode());
            }

        }

        public function _findBy($column, $value){
           return $this->where($column,$value)->get();
        }

        private function excute($query = null)
        {

            $query = (!$query) ? $this->query()->query : $query;
            $this->prepare = Application::$app->db->prepare($query);

            $this->prepare->execute($this->execute);
            return $this;
        }

        public function __call($name, $arguments)
        {

            // TODO: Implement __call() method.
            if(strpos($name, "findBy") !== false){
                $name = strtolower(str_replace("findBy","",$name));

                if($name){
                    $arguments = array_merge([$name],$arguments);
                    return call_user_func_array([$this, "_findBy"],$arguments);
                }
            }
            $name = "_".$name;
            return call_user_func_array([$this,$name],$arguments);
        }

        public static function __callStatic($name, $arguments)
        {
            // TODO: Implement __callStatic() method.
           if(strpos($name, "findBy") !== false){
               $name = strtolower(str_replace("findBy","",$name));
               if($name){
                   $arguments = array_merge([$name],$arguments);
                   return call_user_func_array([new static, "_findBy"],$arguments);
               }
           }

            $name = "_" . $name;
            return call_user_func_array([new static,$name],$arguments);
        }

    }