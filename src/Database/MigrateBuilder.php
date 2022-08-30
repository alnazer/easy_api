<?php
/**
 * @pakages    :easy_api
 * @auther     :https://github.com/alnazer
 * @created_at :30,August 2022 1:55 AM
 * @filename   :MigrateBulider.php
 **/

/*
|--------------------------------------------------------------------------
| Class title
|--------------------------------------------------------------------------
|
| Description Here
|
*/

namespace Alnazer\Easyapi\Database;

class MigrateBuilder
{
    public string $builder = "";
    public array $command;
    public int $countCall = 0;
    public  $finalQuery;
    /**
     * @param $name
     * @param int $length
     * @return $this
     */
    public function string($name, int $length =255)
    {
        return $this->addCommand("$name VARCHAR($length)",0);
    }

    public function int($name, int $length =255)
    {
        return $this->addCommand("$name INT($length)",0);
    }

    public function decimal($name, int $start =15, $end =2)
    {
        return $this->addCommand("$name DECIMAL($start,$end)",0);
    }

    public function tinyInt($name, int $length =15)
    {
        return $this->addCommand("$name TINYINT($length)",0);
    }
    public function date($name, int $length =15)
    {
        return $this->addCommand("$name DATE",0);
    }

    public function null()
    {
        return $this->addCommand("NULL",1);
    }
    public function notNull()
    {
        return $this->addCommand("NOT NULL",1);
    }
    public function default($value = "NULL"){
        return $this->addCommand("DEFAULT $value",2);
    }
    public function auto_increment()
    {
        return $this->addCommand("AUTO_INCREMENT",10);
    }
    public function primary($keys)
    {
        $keys = join(", ",$keys);
        return $this->addCommand("CONSTRAINT contacts_pk  PRIMARY KEY ($keys)",0);
    }
    private function addCommand($string,$index = 0){
        if($index == 0){
            $this->countCall++;
        }
        $this->command[$this->countCall][$index] = $string;
        return $this->createCommand();
    }
    private function createCommand(){
        ksort($this->command);
        return $this;
    }
    public function __toString()
    {
        $result = "";
        if(is_array($this->command) && count($this->command) > 0){
            foreach ($this->command as $item) {
                $result.=join(" " ,$item).",";
            }
        }
        return $result;
    }
}