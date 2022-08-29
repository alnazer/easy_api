<?php
    
    namespace Alnazer\Easyapi\Exceptions;
    
    use Throwable;

    class DatabaseConnectionFailException extends \PDOException
    {
        public function __construct($message = "", $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, (int) $code, $previous);
        }
    }