<?php
    namespace Alnazer\Easyapi\Exceptions;
 

    class MethodNotAllowedException extends \Exception
    {
        /**
         * Constructor.
         * @param string|null $message error message
         * @param int $code error code
         * @param \Throwable|null $previous The previous exception used for the exception chaining.
         */
        public function __construct($message = "Method Not Allowed", $code = 405, $previous = null)
        {
            parent::__construct( $message, (int) $code, $previous);
        }
    }