<?php
/**
 * @pakages    :easy_api
 * @auther     :https://github.com/alnazer
 * @created_at :26,August 2022 2:18 AM
 * @filename   :AuthenticationException.php
 **/


namespace Alnazer\Easyapi\Exceptions;

use RuntimeException;
use Throwable;

class AuthenticationException extends RuntimeException
{
    public function __construct($message = "Authentication failed", $code = 401, Throwable $previous = null)
    {
        parent::__construct($message,(int) $code, $previous);
    }
}