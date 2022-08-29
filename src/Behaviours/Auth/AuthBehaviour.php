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

use Alnazer\Easyapi\Behaviours\BehaviourInterface;
use Alnazer\Easyapi\System\Application;

class Behaviour implements  BehaviourInterface
{
    public array $methods = [BearerAuth::class];
    public array $execute = ["login", "register","forget_password"];

    public function execute()
    {
        return in_array(Application::$app->action,$this->execute);
    }
}