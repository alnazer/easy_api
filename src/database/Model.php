<?php
    
    namespace Alnazer\Easyapi\database;
    
    use Alnazer\Easyapi\exceptions\DatabaseQueryErrorException;
    use Alnazer\Easyapi\helpers\Inflector;
    use Alnazer\Easyapi\System\Application;
    
    abstract class Model extends Connection
    {
        public string $query = "";
        public string $select = "";
        public string $where = "";
        public string $order = "";
        public string $limit = "";
        public string $from = "";
        protected string $tableName;
        public array $execute = [];
        public $prepare;
        private array $queryItemList = [
            "select",
            "from",
            "where",
            "join",
            "order",
            "groupby",
            "limit",
        ];
        
        public function __construct()
        {
            $this->from = "FROM " . $this->getTableName() . " ";
            $this->query = "SELECT * FROM `" . $this->getTableName() . "` LIMIT 10";
        }
        
        /**
         * @return string
         */
        public function getTableName(): string
        {
            $class = is_object($this) ? get_class($this) : $this;
            $name = basename(str_replace('\\', '/', $class));
            $path = explode('\\', $name);
            $name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', array_pop($path)));
            
            if (empty($this->tablename)) {
                $this->tablename = Inflector::pluralize($name);
            }
            return $this->tablename;
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
                $this->select .= " SELECT ";
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
        
        public function _where($condations = [])
        {
            
            if (count($condations) > 0) {

                foreach ($condations as $column => $condation) {
                    $this->setWhere($column, $condation);
                }
            }
            
            return $this;
        }
        
        private function setWhere($column, $condition)
        {
            $this->execute[trim($column)] = $condition;
        }
        protected function formatWhere(){
            if(count($this->execute) > 0){
                $this->where .= " WHERE ";
            }
            foreach ($this->execute as $column => $item) {
                $this->where .= "`$column` = :$column AND ";
            }
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
                $table = $this->getTableName();
            }
            $this->order = " FROM $table";
            return $this;
        }
        
        public function _limit($limit = 10)
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
        
        private function excute($query = null)
        {
            $query = (!$query) ? $this->query()->query : $query;
            $this->prepare = Application::$app->db->prepare($query);
            $this->prepare->execute($this->execute);
            return $this;
        }
        
        public function _last()
        {
            try {
                $this->order(null, "DESC");
                
                return $this->excute()->prepare->fetch();
            } catch (\Exception $e) {
                throw  new DatabaseQueryErrorException($e->getMessage(), $e->getCode());
            }
        }
        
        public function _first()
        {
            try {
                return $this->excute()->prepare->fetch();
            } catch (\Exception $e) {
                throw  new DatabaseQueryErrorException($e->getMessage(), $e->getCode());
            }
        }
        
        public function _one()
        {
            try {
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
                $query = "INSERT INTO `$this->tablename` (`$coulmes`) VALUES (:$values)";
                $this->excute($query);
                return true;
            } catch (\PDOException  $e) {
                throw new DatabaseQueryErrorException($e->getMessage(), (int)$e->getCode());
            }
            
        }
        public function _update($data, $conditions = [])
        {
            try {
                $set = "";
                $this->execute = [];
                foreach ($data as $key => $value) {
                    $set.= " `$key` = :$key , ";
                    $this->execute[$key] = $value;
                }
                $set = rtrim($set, ", ");
                if(count($conditions) > 0){
                    $this->_where($conditions);
                }
                $query = "UPDATE `$this->tablename` SET $set ".$this->where;
         
                $this->excute($query);
                return true;
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
                $query = "DELETE FROM `$this->tablename` ".$this->where;
                $this->excute($query);
                return true;
            } catch (\PDOException  $e) {
                throw new DatabaseQueryErrorException($e->getMessage(), (int)$e->getCode());
            }
        
        }
        public function __call($name, $arguments)
        {
            // TODO: Implement __call() method.
            $name = "_" . $name;
            return $this->$name(...$arguments);
        }
        
        public static function __callStatic($name, $arguments)
        {
            // TODO: Implement __callStatic() method.
            $name = "_" . $name;
            return (new static)->$name(...$arguments);
            
        }
    }