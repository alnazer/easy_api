<?php
    
    namespace Alnazer\Easyapi\exceptions;
    
    class BadRequestException extends \Exception
    {
        /**
         * Constructor.
         * @param string|null $message error message
         * @param int $code error code
         * @param \Throwable|null $previous The previous exception used for the exception chaining.
         */
        public function __construct($message = "unknown content type request", $code = 400, $previous = null)
        {
            parent::__construct($message, $code, $previous);
        
        }
    }