<?php

namespace YourNameSpace;
use SimpleXMLElement;
use Exception;

class xmlHelper {
    public function xmlToArray($data)
    {
        $converted = simplexml_load_string($data);
        if(!$converted){
            throw new Exception('Bad format xml');
        }
        $body = json_decode(json_encode($converted), true);
        return [$converted->getName()=>$body];
    }

    function arrayToXml(array $data, ?string $header = '<?xml version="1.0" encoding="utf-8"?>', ?SimpleXMLElement $xml_data = null):?string {
        if(!isset($xml_data)){
            $key = array_key_first($data);
            $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?>");
            $xml_data = new SimpleXMLElement($header.'<'.$key.'></'.$key.'>');
            SELF::arrayToXml($data[$key], null, $xml_data);
            return $xml_data->asXML();
        }
        foreach( $data as $key => $value ) {
            if($key === '@attributes'){
                foreach ($value as $attributes => $attrValue) {
                    $xml_data->addAttribute("$attributes",htmlspecialchars("$attrValue"));
                }
                continue;
            }
    
            if(!is_array($value) ) {
                $xml_data->addChild("$key",htmlspecialchars("$value"));
                continue;
            }
    
            if( SELF::isAssoc($value) ){
                $subnode = $xml_data->addChild($key);
                SELF::arrayToXml($value, null, $subnode);
                continue;
            }
            
            foreach ($value as $value2){
                $subnode2 = $xml_data->addChild($key);
                SELF::arrayToXml($value2, null, $subnode2);
            }
         }
         return null;
    }

    public static function isAssoc(array $arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
