<?php
    
    namespace Alnazer\Easyapi\Exceptions;
    
    use Throwable;

    class DatabaseQueryErrorException extends \PDOException
    {
    
        public function __construct($message = "", $code = 403, Throwable $previous = null)
        {
            parent::__construct($message, (int) $code, $previous);
        }
    }