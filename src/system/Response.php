<?php
    
    namespace Alnazer\Easyapi\System;
    
    use SimpleXMLElement;

    class Response
    {
        private Request $request;
    
        public function __construct()
        {
         
            $this->request = new Request();
            $this->setHeader("Content-Type", $this->request->getHeader("Accept"));
        }
    
        public function getHeaders()
        {
            return apache_response_headers();
        }
        
        public function getHeader($name)
        {
            $headers = $this->getHeaders();
            if(in_array($name, array_keys($this->getHeaders()))){
                return $headers[$name];
            }
            return null;
        }
        public function setHeader($name, $value)
        {
            header("$name: $value");
        }
        
        public function returnResponse($data)
        {
           
            $data = (!is_array($data)) ? [$data] : $data;
            
            switch ($this->request->getHeader("Accept")){
                case "application/xml":{
                    $xml = new SimpleXMLElement('<root/>');
                    if(is_array($data) && count($data) > 0){
                        $this->convertArrayToXml($data, $xml);
                    }
                    return $xml->asXML();
                }
                default:{
                    return json_encode($data);
                }
            }
        }
        public function setCode(int $int)
        {
            http_response_code($int);
        }
        private function convertArrayToXml($array, &$xml)
        {
            foreach ($array as $key => $value) {
                if(!is_array($value)){
                    $xml->addChild($key,$value);
                }else{
                    $_xml = $xml->addChild($key);
                    $this->convertArrayToXml($value,$_xml);
                }
            }
        }

    }