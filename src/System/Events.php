<?php
/**
 * @pakages    :easy_api
 * @auther     :https://github.com/alnazer
 * @created_at :28,August 2022 1:33 AM
 * @filename   :Events.php
 **/

/*
|--------------------------------------------------------------------------
| Class title
|--------------------------------------------------------------------------
|
| Description Here
|
*/

namespace Alnazer\Easyapi\system;

abstract class  Events
{
    public $event;
    abstract static public function run($event);
}