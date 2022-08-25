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
    
        public function connect()
        {
            try {
                $servername = $this->config["host"] ?? "";
                $username = $this->config["username"] ?? "";
                $password = $this->config["password"] ?? "";
                $db_name = $this->config["name"] ?? "";
                $this->db = new \PDO("mysql:host=$servername;dbname=$db_name", $username, $password);
                // set the PDO error mode to exception
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                $this->db->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS ,true);
                $this->db->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
                return $this->db;
            } catch(\PDOException $e) {
                throw new DatabaseConnectionFailException($e->getMessage(), $e->getCode());
            }
        }
        
    }