<?php
/**
 * @pakages    :easy_api
 * @auther     :https://github.com/alnazer
 * @created_at :26,August 2022 2:01 AM
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

use Alnazer\Easyapi\exceptions\AuthenticationException;
use Alnazer\Easyapi\System\Application;

class Authentications extends Application
{
    public array $methods = [BearerAuth::class];
    public array $execute = ["login", "register","forget_password"];
    /**
     * @return bool
     */
    public function execute()
    {
        if(in_array(Application::$app->action,$this->execute)){
            return true;
        }
        $allValidClassCount = count($this->methods);
        $errorCount = 0;
        foreach ($this->methods as $class){
            try {
                $class  = new $class();
                $class->execute = $this->execute;
                $class->execute();
            }catch (\Exception $e){
                $errorCount++;
            }
        }
        if($allValidClassCount === $errorCount){
            throw new AuthenticationException();
        }
        return true;
    }
}