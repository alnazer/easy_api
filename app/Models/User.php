<?php
    
    namespace app\Models;
    
    use Alnazer\Easyapi\Database\Model;
    use Alnazer\Easyapi\Database\UserInterface;

    class User extends Model implements UserInterface
    {
        public static $user;
        public static function findIdentityByAccessToken($token = null)
        {
            // TODO: Implement findIdentityByAccessToken() method.
              return self::$user = self::where(["access_token"=> $token])->one();
        }

        public static function findIdentityByUsernamePassword($username, $password)
        {
            return self::where([self::usernameFiled() => $username,"password" => self::hashPassword($password)])->one();
        }

        public static function hashPassword($password): string
        {
            // TODO: Implement hashPassword() method.
            return password_hash($password, PASSWORD_BCRYPT);
        }
        public static function verifyPassword($enter_password, $currency_password)
        {
            return password_verify($enter_password, $currency_password);
        }
        public static function login($username, $password)
        {
            return self::$user = self::findIdentityByUsernamePassword($username, $password);
        }

        public static function usernameFiled(): string
        {
            return "username";
        }

    }