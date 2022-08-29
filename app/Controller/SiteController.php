<?php
    
    namespace app\Controller;

    use Alnazer\Easyapi\Behaviours\Auth\BearerAuth;
    use Alnazer\Easyapi\Behaviours\Auth\MultiAuth;
    use Alnazer\Easyapi\Behaviours\RateLimit\RateLimit;
    use Alnazer\Easyapi\Database\Query;
    use Alnazer\Easyapi\System\Controller;
    use app\Models\User;

    class SiteController extends Controller
    {

        public function behaviour($behaviours = [])
        {
            $behaviours["auth"] = [
                "class"=> MultiAuth::class,
                'methods' => [
                    //BasicAuth::class,
                    BearerAuth::class,
                ],
                "execute" => ["register"]
            ];
            $behaviours["rate_limit"] = [
                "class"=> RateLimit::class,
                "everySecond" => 3,
                'requestCount' => 3,
                'allowDisplayInHeader' => true
            ];
            return parent::behaviour($behaviours); // TODO: Change the autogenerated stub
        }
        public function register(){
            return  User::insert(["username"=> "admin".rand(1,20),"email"=> "hassan".rand(1,20)."@gmail.com","password"=> security()->getRandonKey(50),"access_token"=> security()->getRandonKey(50)]);
        }
        public function index($id = null,$name = null,$mobile = null){
            //Cache::add(range(1,20),13000);
           return [
               'id'=> $id,
               "name" => $name,
               'mobile' => $mobile,
               //'get' => Cache::get("bc2a2917b4640a146f2fdcb9546b3d27c69b11df1e6c4e3c5aad3d46e5ce"),
               //'findBy' => User::findById(3),
               /*"all"=>User::select(["id ","username ","id "])->all(),
               "all_selec"=>User::select("*")->whereIn("id",[1,2,3])->all(),
               "insert" => User::insert(["username"=> "admin".rand(1,20),"email"=> "hassan".rand(1,20)."@gmail.com","password"=> sha1(time()),"access_token"=>sha1(time()*time())]),
               "query"=>User::select("*")->where(["username"=> "admin","email"=> "hassan@gmail.com"])->all(),
               "queryCount"=>User::select("*")->where(["username"=> "admin","email"=> "hassan@gmail.com"])->count(),
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
               ],*/
           ];
        }

        public function beforeCall($action = [])
        {
            return parent::beforeCall($action); // TODO: Change the autogenerated stub
        }
    }