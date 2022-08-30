<?php
    
    namespace Alnazer\Easyapi\Database;
    
    use Alnazer\Easyapi\Exceptions\DatabaseConnectionFailException;
    use Alnazer\Easyapi\System\Configuration;
    use Illuminate\Database\Capsule\Manager as Capsule;
    use Illuminate\Events\Dispatcher;
    use Illuminate\Container\Container;
    
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
                try {
                    $capsule = new Capsule;
                    $capsule->addConnection([
                        'driver' => 'mysql',
                        'host' => $servername,
                        'database' => $db_name,
                        'username' => $username,
                        'password' => $password,
                        'charset' => $db_encode,
                        'collation' => 'utf8_unicode_ci',
                        'prefix' => '',
                    ]);
                    $capsule->setEventDispatcher(new Dispatcher(new Container));

                    // Make this Capsule instance available globally via static methods... (optional)
                    $capsule->setAsGlobal();

                    // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
                    $capsule->bootEloquent();
                    return $this->db = $capsule;
                }catch (\Exception $e){
                    throw new DatabaseConnectionFailException($e->getMessage(), $e->getCode());
                }
                return $this->db;
            } catch(\PDOException $e) {
                throw new DatabaseConnectionFailException($e->getMessage(), $e->getCode());
                return false;
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage(), $e->getCode());
                return false;
            }
        }

        private function validDatabaseDefineInfo()
        {
            if(!$this->config){
                throw new \Exception("You must define db connection in Config file", 2002);
            }
        }

    }