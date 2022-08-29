<?php
/**
 * @pakages    :easy_api
 * @auther     :https://github.com/alnazer
 * @created_at :28,August 2022 2:31 AM
 * @filename   :events.php
 **/
return [
    "AFTER_INSERT" => [
        "class" => app\Events\AfterInsert::class,
        "event" => ["username"=> "sayes" ,"email"=> "sayed@gmail.com" ,"password"=> 123443,'access_token' => mt_rand(1,30)]
    ]
];