<?php
/**
 * @pakages    :easy_api
 * @auther     :https://github.com/alnazer
 * @created_at :26,August 2022 4:29 AM
 * @filename   :LoginUsernamePasswordException.php
 **/

/*
|--------------------------------------------------------------------------
| Class title
|--------------------------------------------------------------------------
|
| Description Here
|
*/

namespace Alnazer\Easyapi\exceptions;

use Throwable;

class LoginUsernamePasswordException extends AuthenticationException
{
    public function __construct($message = "Login failed error username or password", $code = 401, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}