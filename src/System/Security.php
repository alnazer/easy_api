<?php
    
    namespace Alnazer\Easyapi\System;
    
    use Exception;

    class Security
    {
        public string $encrypt_algo = "aes-256-cbc-hmac-sha1";
        private string $tag = "easy_api";
        /**
         * @param $input
         * @return void
         * @throws Exception
         */
        public function validSecurityKey()
        {
            if(!Application::$app->security_key){
                throw new Exception('Config parameter (security_key) must be define',30);
            }
        }

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
         * @throws Exception
         */
        public function getRandomKey($length = 0): string
        {
            if(is_string($length)){
                throw new Exception('First parameter ($length) must be an integer',30);
            }
            if($length <= 0 ){
                throw new Exception('First parameter ($length) must be greater than 0',30);
                
            }
            return bin2hex(random_bytes($length));
        }
        function getRandomToken($length = 32){
            if(!isset($length)  || !intval($length)){
                $length = 32;
            }
            if (function_exists('random_bytes')) {
                return bin2hex(random_bytes($length));
            }
            if (function_exists('mcrypt_create_iv')) {
                return bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
            }
            if (function_exists('openssl_random_pseudo_bytes')) {
                return bin2hex(openssl_random_pseudo_bytes($length));
            }
        }
        public function generateRandomString($length = 10 ,$schema = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ") {
            return substr(str_shuffle(str_repeat($x=$schema, ceil($length/strlen($x)) )),1,$length);
        }
        /**
         * @param $text
         * @return string
         * @throws Exception
         */
        public function encrypt($text): string
        {
            $this->validSecurityKey();
            if (in_array($this->encrypt_algo, openssl_get_cipher_methods())) {
                $iv_length = openssl_cipher_iv_length($cipher= $this->encrypt_algo);
                $iv = openssl_random_pseudo_bytes($iv_length);
                $ciphertext_raw = openssl_encrypt($text, $cipher, Application::$app->security_key, $options=OPENSSL_RAW_DATA, $iv);
                $hmac = hash_hmac('sha256', $ciphertext_raw, Application::$app->security_key, $as_binary=true);
                return base64_encode( $iv.$hmac.$ciphertext_raw );
            }else{
                throw new Exception("cipher methods not found");
            }
        }

        /**
         * @param $text
         * @return string|void
         * @throws Exception
         */
        public function decrypt($text){
            $this->validSecurityKey();
            if (in_array($this->encrypt_algo, openssl_get_cipher_methods())) {
                $c = base64_decode($text);
                $iv_length = openssl_cipher_iv_length($cipher= $this->encrypt_algo);
                $iv = substr($c, 0, $iv_length);
                $hmac = substr($c, $iv_length, $sha2len=32);
                $ciphertext_raw = substr($c, $iv_length+$sha2len);
                $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, Application::$app->security_key, $options=OPENSSL_RAW_DATA, $iv);
                $calcium = hash_hmac('sha256', $ciphertext_raw, Application::$app->security_key, $as_binary=true);
                if (hash_equals($hmac, $calcium))// timing attack safe comparison
                {
                    return $original_plaintext;
                }

            }else{
                throw new Exception("cipher methods not found");
            }

        }
    }