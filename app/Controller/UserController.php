<?php
    namespace app\Controller;
    use Alnazer\Easyapi\Behaviours\Auth\MultiAuth;
    use Alnazer\Easyapi\Behaviours\Auth\BasicAuth;
    use Alnazer\Easyapi\Behaviours\Auth\BearerAuth;
    use Alnazer\Easyapi\Exceptions\AuthenticationException;
    use Alnazer\Easyapi\Exceptions\LoginUsernamePasswordException;
    use Alnazer\Easyapi\System\Application;
    use Alnazer\Easyapi\System\Controller;
    use app\Models\User;

    class UserController extends Controller
    {

        public function behaviour($behaviours = [])
        {
            $behaviours["auth"] = [
                "class"=> MultiAuth::class,
                'methods' => [
                    BasicAuth::class,
                    BearerAuth::class,
                ],
                "execute" => ["login","register"]
            ];
            return parent::behaviour($behaviours); // TODO: Change the autogenerated stub
        }
        public function index(){
            pd(Application::$app->auth->user()->username);
        }
        public function login()
        {
            $username = request()->get("username");
            $password = request()->get("password");
            $isLogin = User::login($username, $password);
            if(!$isLogin){
                return User::$user;
            }else{
                throw new LoginUsernamePasswordException();
            }
        }
        public function update()
        {
            return 1;
        }
        public function beforeCall($action = [])
        {
            return parent::beforeCall($action); // TODO: Change the autogenerated stub
        }
    }