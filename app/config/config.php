<?php
require_once "endpoint.php";
return $config = [
    "namespace" => "app",
    "auth"  =>[
        "class" => \app\Models\User::class,
    ],
    'db'  =>[
        "host" => "127.0.0.1",
        "name" => "api",
        "username" => "root",
        "password" => "root",
        "port" => "8889",
        "encode" => "utf8",
    ]
];