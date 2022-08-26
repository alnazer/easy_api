<?php
    
    namespace Alnazer\Easyapi\exceptions;
    
    use Throwable;

    class DatabaseConnectionFailException extends \PDOException
    {
     
        public function __construct($message = "database connection fail", $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, (int) $code, $previous);
        }
    }