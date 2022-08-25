<?php
    
    namespace Alnazer\Easyapi\exceptions;
    
    class BadClassCallException extends \Exception
    {
        /**
         * Constructor.
         * @param string|null $message error message
         * @param int $code error code
         * @param \Throwable|null $previous The previous exception used for the exception chaining.
         */
        public function __construct($message = "Class Not found", $code = 403, $previous = null)
        {
            parent::__construct( $message, $code, $previous);
        }
    }