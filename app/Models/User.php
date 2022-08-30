<?php
    
    namespace app\Models;
    
    use Alnazer\Easyapi\Behaviours\RateLimit\UserRateLimitInterface;
    use Alnazer\Easyapi\Database\Model;
    use Alnazer\Easyapi\Database\UserInterface;

    class User extends Model implements UserInterface,UserRateLimitInterface
    {
        public static $user;
        public static  string $tableName = "users";
        public static function findIdentityByAccessToken($token = null)
        {
            // TODO: Implement findIdentityByAccessToken() method.
             //self::$user = ;
            self::$user = self::where(["access_token"=> $token])->one();
            return new static(self::$user);
        }

        public static function findIdentityByUsernamePassword($username, $password)
        {
            self::$user = self::where([self::usernameFiled() => $username,"password" => self::hashPassword($password)])->one();
            return new static(self::$user);
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
            self::$user = self::findIdentityByUsernamePassword($username, $password);
            return new static(self::$user);
        }

        public static function usernameFiled(): string
        {
            return "username";
        }

        public function requestCount(): int
        {
            // TODO: Implement requestCount() method.
            return 10;
        }
    
        public function everySecond(): int
        {
            // TODO: Implement perSecond() method.
            return 10;
        }
    
        public function user()
        {
            return (object) self::$user;
        }
    }