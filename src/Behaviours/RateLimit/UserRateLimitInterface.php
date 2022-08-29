<?php
    
    namespace Alnazer\Easyapi\Behaviours\RateLimit;
    
    interface UserRateLimitInterface
    {
        public function requestCount() :int;
    
        public function everySecond() :int;
    }