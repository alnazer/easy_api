<?php
/**
 * @pakages    :easy_api
 * @auther     :https://github.com/alnazer
 * @created_at :26,August 2022 2:13 AM
 * @filename   :BearerAuth.php
 **/

/*
|--------------------------------------------------------------------------
| Class title
|--------------------------------------------------------------------------
|
| Description Here
|
*/

namespace Alnazer\Easyapi\Behaviours\Auth;


use Alnazer\Easyapi\Exceptions\AuthenticationException;
use Alnazer\Easyapi\System\Application;

class BearerAuth extends AuthBehaviour
{

    public function execute(): bool
    {
        if(parent::execute() === false){
            if($this->getToken()){
                $user = Application::$app->config["auth"]['class']::findIdentityByAccessToken($this->getToken());
                if($user){
                    Application::$app->auth = $user;
                }else{
                    throw  new AuthenticationException();
                }
                return true;
            }else{
                throw  new AuthenticationException();
            }
        }
        return parent::execute(); // TODO: Change the autogenerated stub
    }

    private function getToken(){

        $header = request()->getHeader("Authorization");
        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}