<?php
    
    namespace app\Models;
    
    use Alnazer\Easyapi\Behaviours\RateLimit\UserRateLimitInterface;
    use Alnazer\Easyapi\Database\UserInterface;
    use Illuminate\Database\Eloquent\Model;


    class User extends Model implements UserInterface,UserRateLimitInterface
    {
        public static $user;
        public  $table = "users";
        public static function findIdentityByAccessToken($token = null)
        {
            return User::where(["access_token"=> $token])->get();
        }

      
        public static function findIdentityByUsername($username)
        {
            return User::where([self::usernameFiled() => $username])->first();
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
            $user =  User::findIdentityByUsername($username);
            return ($user && self::verifyPassword($password, $user->password)) ? $user : false;
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
    

    }