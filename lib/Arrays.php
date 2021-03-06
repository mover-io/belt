<?php namespace Belt;

class Arrays {

    /**
     * Gets a value from an array if it exists, otherwise returns $default;
     *
     * Supports dot delimited keys to safely traverse deep arrays.
     *
     * @return mixed
     */
    public static function get($array, $key, $default = null) {
        $keys = explode(".", $key);
        $level = &$array;
        $length = count($keys);
        foreach($keys as $index => $key) {
            if (!is_array($level)) {
              return null;
            }

            if(array_key_exists($key, $level)) {
                if($index == $length-1) {
                    return $level[$key];
                } else {
                    $level = &$level[$key];
                }
            } else {
                return $default;
            }
        }
        return $default;
    }

    public static function subarray($array, $key)
    {
        return static::get($array, $key, array());
    }

    /**
     * Returns an $array using $mapping to convert it's original keys.
     *
     * @example
     * >>> Arrays::map(array('hello'=>'world','when'=>'now'),array('hello'=>'goodbye'));
     * array('goodbye'=>'world');
     */
    public static function map($array, $mapping) {
        $result = array();
        foreach($mapping as $src=>$dest) {
            if(array_key_exists($src, $array)) {
                $result[$dest] = $array[$src];
            }
        }
        return $result;
    }

}
