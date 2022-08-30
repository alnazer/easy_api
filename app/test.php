<?php
namespace app;
use app\Models\User;

class Query {
    public static $query;
    public static function select()
    {
        self::$query.=" select ";
        return (new self);
    }
    public static function from()
    {
        self::$query.=" from ";
         return (new self);
    }
    public static function where()
    {
        self::$query.=" where ";
         return (new self);
    }
    public static function get()
    {
        return self::$query;
    }
}
echo Query::select()->from()->where()->get()."\n";
