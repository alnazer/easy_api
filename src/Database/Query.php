<?php
    
    namespace Alnazer\Easyapi\Database;
    
    class Query extends Model
    {
    
        public function update(array $array, array $array1)
        {
            parent::_update($array,$array1);
        }
    
        public function insert(array $array)
        {
            parent::_insert($array);
        }


    }