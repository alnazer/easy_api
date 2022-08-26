<?php
/**
 * @pakages    :easy_api
 * @auther     :https://github.com/alnazer
 * @created_at :26,August 2022 4:02 AM
 * @filename   :Authentications.php
 **/

/*
|--------------------------------------------------------------------------
| Class title
|--------------------------------------------------------------------------
|
| Description Here
|
*/

namespace Alnazer\Easyapi\behaviours\Auth;

use Alnazer\Easyapi\System\Application;

class Authentications
{
    public array $methods = [BearerAuth::class];
    public array $execute = ["login", "register","forget_password"];
    
    public function execute()
    {
        if(in_array(Application::$app->action,$this->execute)){
            return true;
        }
        return false;
    }
}