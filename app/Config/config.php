<?php
require_once "endpoint.php";
require_once "events.php";
$config = [
    "namespace" => "app",
    "security_key" => "qqeqweqwe@#!#$!$%\$Q4",
    "auth"  =>[
        "class" => \app\Models\User::class,
    ],
    /*'db'  =>[
        "host" => "127.0.0.1",
        "name" => "api",
        "username" => "root",
        "password" => "",
        "port" => "3306",
        "encode" => "utf8",
    ]*/
    'db'  =>[
        "host" => "127.0.0.1",
        "name" => "api",
        "username" => "root",
        "password" => "root",
        "port" => "8889",
        "encode" => "utf8",
    ]
];
return $config = array_merge($config,["events"=>include "events.php"]);