<?php
/**
 * @pakages    :easy_api
 * @auther     :https://github.com/alnazer
 * @created_at :26,August 2022 2:01 AM
 * @filename   :MultiAuth.php
 **/

/*
|--------------------------------------------------------------------------
| Class title
|--------------------------------------------------------------------------
|
| Description Here
|
*/

namespace Alnazer\Easyapi\behaviours\Auth;

use Alnazer\Easyapi\exceptions\AuthenticationException;

class MultiAuth extends Auth
{

    public function execute(): bool
    {
        if(parent::execute() === false){
            $allValidClassCount = count($this->methods);
            $errorCount = 0;
            foreach ($this->methods as $class){
                try {
                    $class  = new $class();
                    $class->execute = $this->execute;
                    $class->execute();
                }catch (\Exception $e){
                    $errorCount++;
                }
            }
            if($allValidClassCount === $errorCount){
                throw new AuthenticationException();
            }
            return true;
        }
        return parent::execute();
    }

}