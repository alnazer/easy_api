<?php
/**
 * @pakages    :easy_api
 * @auther     :https://github.com/alnazer
 * @created_at :28,August 2022 1:04 AM
 * @filename   :EventsController.php
 **/

/*
|--------------------------------------------------------------------------
| Class title
|--------------------------------------------------------------------------
|
| Description Here
|
*/

namespace app\Controller;


class EventsController extends \Alnazer\Easyapi\System\Controller
{

    public function add()
    {
        $this->callEvent(AFTER_INSERT,["username"=> "ahmed" ,"email"=> "ahmed@gmail.com" ,"password"=> 123443,'access_token' => mt_rand(1,30)]);
    }
    public function call()
    {

    }

}