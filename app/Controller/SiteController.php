<?php
    
    namespace app\Controller;

    use Alnazer\Easyapi\behaviours\Auth\MultiAuth;
    use Alnazer\Easyapi\behaviours\Auth\BasicAuth;
    use Alnazer\Easyapi\behaviours\Auth\BearerAuth;
    use Alnazer\Easyapi\System\Controller;
    use app\Models\User;

    class SiteController extends Controller
    {

        public function behaviour($behaviours = [])
        {
            $behaviours["auth"] = [
                "class"=> MultiAuth::class,
                'methods' => [
                    BasicAuth::class,
                    BearerAuth::class,
                ],
                "execute" => ["index"]
            ];
            return parent::behaviour($behaviours); // TODO: Change the autogenerated stub
        }

        public function index(){
            //User::insert(["username"=>"admin","password"=>sha1(rand(1,20)),"access_token"=>sha1("aafafsfa")]);
           /*return [
               "query"=>User::select("*")->where(["username"=> "admin","email"=> "hassanaliksa@gmail.com"])->all(),
               "queryCount"=>User::select("*")->where(["username"=> "admin","email"=> "hassanaliksa@gmail.com"])->count(),
               "all"=>User::all(),
               "one"=>User::one(),
               "last"=>User::last(),
               "count"=>User::count(),
               "first"=>User::first(),
               "inputs" => $this->request->get(),
               "name" => ["first"=> "hassan", "last" => "Attia"],
               "email" => "hassan@gmail.com",
               "mobile" => "90033807",
               "nation" => [
                   "country" => [
                       "name" => "egypt",
                       "code" => "EG",
                       "curancy" => [
                           "title" => "Egypt bound",
                           "ex" => 3.0,
                       ]
                   ]
               ],
           ];*/
        }
        
        public function beforeCall($action = [])
        {
            return parent::beforeCall($action); // TODO: Change the autogenerated stub
        }
    }