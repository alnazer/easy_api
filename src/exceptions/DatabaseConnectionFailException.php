<?php
    
    namespace Alnazer\Easyapi\exceptions;
    
    use Throwable;

    class DatabaseConnectionFailException extends \Exception
    {
     
        public function __construct($message = "", $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }
    }