<?php
    
    namespace Alnazer\Easyapi\database;
    
    use Alnazer\Easyapi\exceptions\DatabaseConnectionFailException;
    use Alnazer\Easyapi\System\Configuration;
    use PDO;

    class Connection extends Configuration
    {
        private $config;
        private $db;
    
        /**
         * @throws DatabaseConnectionFailException
         */
        public function __construct($config =[])
        {

            $this->config = $config;
        }

        /**
         * @throws DatabaseConnectionFailException
         */
        public function connect()
        {
            try {
                $this->validDatabaseDefineInfo();
                $servername = $this->config["host"] ?? "";
                $username = $this->config["username"] ?? "";
                $password = $this->config["password"] ?? "";
                $db_name = $this->config["name"] ?? "";
                $db_encode = $this->config["encode"] ?? "";
                $port = $this->config["port"] ?? "";
                $port = ($port)? "port=$port;" : "";
                try {
                    $this->db = new \PDO("mysql:host=$servername;dbname=$db_name;$port", $username, $password);
                }catch (\PDOException $e){
                    throw new DatabaseConnectionFailException($e->getMessage(), $e->getCode());
                }

                // set the PDO error mode to exception
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                $this->db->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS ,true);
                $this->db->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );

                if(!empty($db_encode)){
                    $this->db->setAttribute( PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES $db_encode" );
                }
                return $this->db;
            } catch(\PDOException $e) {
                throw new DatabaseConnectionFailException($e->getMessage(), $e->getCode());
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage(), $e->getCode());
            }
        }

        private function validDatabaseDefineInfo()
        {
            if(!$this->config){
                throw new \Exception("You must define db connection in config file", 2002);
            }
        }

    }