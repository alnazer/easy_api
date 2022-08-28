<?php
/**
 * @pakages    :easy_api
 * @auther     :https://github.com/alnazer
 * @created_at :28,August 2022 2:36 AM
 * @filename   :AfterInsert.php
 **/

/*
|--------------------------------------------------------------------------
| Class title
|--------------------------------------------------------------------------
|
| Description Here
|
*/

namespace app\Event;

use Alnazer\Easyapi\system\Events;
use app\Models\User;

class AfterInsert extends Events
{

    public static function run($event)
    {
        User::insert($event);
        // TODO: Implement run() method.
    }
}