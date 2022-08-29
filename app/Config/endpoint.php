<?php
    use Alnazer\Easyapi\System\EndPoint;
    EndPoint::any("/register","SiteController@register");
    EndPoint::any("/asd/","SiteController@index");
    EndPoint::any(["user","index"],"UserController@index");
    EndPoint::any(["user","update"],"UserController@update");
    EndPoint::any(["user","update"],["UserController","update"]);
    EndPoint::any(["user","login"],["UserController","login"]);

    EndPoint::any(["events","add"],["EventsController","add"]);
    EndPoint::any(["events","call"],["EventsController","call"]);
    EndPoint::any(["events","remove"],["EventsController","remove"]);

