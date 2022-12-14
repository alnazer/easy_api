<?php
/**
 * @pakages    :easy_api
 * @auther     :https://github.com/alnazer
 * @created_at :26,August 2022 2:45 AM
 * @filename   :BasicAuth.php
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


class BasicAuth extends AuthBehaviour
{

    public function execute()
    {
        if(parent::execute() === false){
            $token = $this->getToken();
            if($token){
                $user = Application::$app->config["auth"]['class']::findIdentityByUsernamePassword($token[0],$token[1]);
                if($user){
                    Application::$app->auth = (object) $user;
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

    private function getToken(): array
    {
        $username = request()->getHeader("PHP_AUTH_USER");
        $password = request()->getHeader("PHP_AUTH_PW");

        if (!empty($username) && !empty($password)) {
             return [
                 "username" => $username,
                 "password" => $password,
             ];
        }

        $auth_token = request()->getHeader('Authorization');
        if ($auth_token !== null && strncasecmp($auth_token, 'basic', 5) === 0) {
            $array_map = [];
            foreach (explode(':', base64_decode(mb_substr($auth_token, 6)), 2) as $key => $value) {
                $array_map[$key] = strlen($value) === 0 ? null : $value;
            }
            $parts = $array_map;
            if (count($parts) < 2) {
                return [$parts[0], null];
            }

            return $parts;
        }

        return [null, null];

    }
}