<?php
    
    namespace Alnazer\Easyapi\Database;
    
    use Alnazer\Easyapi\System\Application;
    use mysql_xdevapi\Exception;

    class Schema
    {

        public static function isTableExist($tableName = ""){
            if($tableName){
                try {
                    Application::$app->db->query("SELECT 1 FROM {$tableName} LIMIT 1");
                    return true;
                }catch (\PDOException $e){
                    return false;
                }
            }
            return false;
        }

        /**
         * @throws \Exception
         */
        public static function createTable($tableName,$query)
        {

                try {
                    if(is_string($query)){
                        Application::$app->db->exec($query);
                    }elseif (is_object($query)){
                        $table = new MigrateBuilder();
                        $query($table);
                        return rtrim($table,",");
                    }
                }catch (\PDOException $e){
                    throw new \PDOException($e->getMessage(),$e->getCode());
                }catch (\Exception $e){
                    throw new \Exception($e->getMessage(),$e->getCode());
                }
                
        }

    }