<?php
    use Alnazer\Easyapi\System\EndPoint;
    EndPoint::any("/asd/","SiteController@index");
    EndPoint::any(["user","index"],"UserController@index");
    EndPoint::any(["user","update"],"UserController@update");
    EndPoint::any(["user","update"],["UserController","update"]);
    EndPoint::any(["user","login"],["UserController","login"]);