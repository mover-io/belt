<?php namespace Belt;

class Arrays {

    /**
     * Filters $array by the variadic $keys provided as additional input.
     *
     * @example
     * << $info = array('user' => 'sparks', 'age' => 'infinite', 'make' => 'robocon');
     * << Arrays::pluck('user', 'robocon')
     * >> array('user' => 'sparks', 'make' => 'robocon')
     *
     * @return $array
     */
    public static function pluck($array) {
        $result = array();
        $keys = array_slice(func_get_args(),1);
        foreach($keys as $key) {
            if(isset($array[$key])) {
                $result[$key] = $array[$key];
            }
        }
        return $result;
    }

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

    /**
     * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
     * keys to arrays rather than overwriting the value in the first array with the duplicate
     * value in the second array, as array_merge does. I.e., with array_merge_recursive,
     * this happens (documented behavior):
     *
     * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('org value', 'new value'));
     *
     * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
     * Matching keys' values in the second array overwrite those in the first array, as is the
     * case with array_merge, i.e.:
     *
     * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('new value'));
     *
     * Parameters are passed by reference, though only for performance reasons. They're not
     * altered by this function.
     *
     * @param array $array1
     * @param array $array2
     * @return array
     * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
     * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
     */
    public static function array_merge_recursive_distinct($array1, $array2)
    {
      $merged = $array1;

      foreach($array2 as $key => &$value) {
        if (is_array($value) && isset ( $merged[$key] ) && is_array($merged[$key])) {
          $merged[$key] = static::array_merge_recursive_distinct($merged[$key], $value);
        } else {
          $merged[$key] = $value;
        }
      }
      return $merged;
    }
}
