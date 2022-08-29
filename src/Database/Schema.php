<?php
    
    namespace Alnazer\Easyapi\Database;
    
    use mysql_xdevapi\Exception;

    class Schema
    {
        public $db;
        
        public function isTableExist($tableName = ""){
            if($tableName){
                try {
                    $this->db->query("SELECT 1 FROM {$tableName} LIMIT 1");
                    return true;
                }catch (\PDOException $e){
                    return false;
                }
            }
            return false;
        }
    
        public function createTable($tableName, $query)
        {
                try {
                    if(is_string($query)){
                        $this->db->exec($query);
                    }
                }catch (\PDOException $e){
                    throw new \PDOException($e->getMessage(),$e->getCode());
                }catch (\Exception $e){
                    throw new \Exception($e->getMessage(),$e->getCode());
                }
                
        }
    }