<?php
    
    namespace Alnazer\Easyapi\exceptions;
    
    use Throwable;

    class DatabaseQueryErrorException extends \PDOException
    {
    
        public function __construct($message = "", $code = 403, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }
    }