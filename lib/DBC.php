<?php namespace Belt;

/**
 * Debugging aid to make subtle errors blow up nicely.
 */
class DBC
{
    static public $disabled = false;

    public static function precondition()
    {
        if( self::$disabled ) { return; }

        // The last argument is a callback that receives the first n-1
        // args provided, Why? Because php.
        $args = func_get_args();
        $func = array_pop($args);
        call_user_func_array($func, $args);
    }

    public static function requireInstance($value, $class)
    {
        if( self::$disabled ) { return; }

        if (! is_a($value, $class)) {
            throw new \Exception("Contract failed - our value was not a $class.");
        }
    }

    public static function assert($statement)
    {
        if( self::$disabled ) { return; }

        if ($statement === false) {
            throw new \Exception("Assertion failed");
        }
    }
}

