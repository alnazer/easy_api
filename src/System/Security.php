<?php
    
    namespace Alnazer\Easyapi\System;
    
    class Security
    {
        /**
         * @param $input
         * @return array
         */
        public function cleanInput($input){
            $output = null;
            if (is_array($input)) {
                foreach($input as $var=>$val) {
                    $output[$var] = $this->clean($val);
                }
            }
            else {
                $output  = $this->clean($input);
            }
            return $output;
        }
    
        /**
         * @param $input
         * @return string
         */
        private function clean($input) {
            $input = strip_tags($input);
            $search = array(
                '@<script[^>]*?>.*?</script>@si',   // Javascript tag
                '@<[\/\!]*?[^<>]*?>@si',            // HTML tags
                '@<style[^>]*?>.*?</style>@siU',    // Style tags
                '@<![\s\S]*?--[ \t\n\r]*>@'         // Multi-line
            );
            
            $output = preg_replace($search, '', $input);
            $output = htmlentities($output, ENT_QUOTES, 'UTF-8');
            $output = str_replace('/', '&#x2F;', $output);
            return stripslashes($output);
        }
    
        /**
         * @throws \Exception
         */
        public function getRandonKey($length = 0)
        {
            if(is_string($length)){
                throw new \Exception('First parameter ($length) must be an integer',30);
            }
            if($length <= 0 ){
                throw new \Exception('First parameter ($length) must be greater than 0',30);
                
            }
            return bin2hex(random_bytes($length));
        }
    }