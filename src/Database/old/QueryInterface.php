<?php
    
    namespace Alnazer\Easyapi\Database\old;
    
    interface QueryInterface
    {
        public static function primaryKey($id = "id");
        
        public static function select($query = "");
    
        public static function table($table);
        
        public static function from($table = null);
        
        public static function where($firstValue = "", $operation = "=", $sendValue = "");
        
        public static function  whereIn($column,$value);
        
        public static function like($column, $value, $strat = "",$end = "");
        
        public static function order($column = null, $sort = "ASC");
    
        public static function groupBy($columns);
        
        public static function limit($limit = 20);
        
        public static function query();
        
        public static function last();
    
        public static function first();
    
        public static function one();
    
        public static function count();
    
        public static function all();
        
        public static function get();
    
        public static function exist($id=null);
        
        public static function insert($data);
        
        public static function update($data = [], $conditions = []);
        
        public static function delete($conditions = []);
        
        public static function findBy($column, $value);
        
    }