<?php
namespace Belt;

class Stack
{
    /**
     * Generates a stack trace while skipping internal and self calls.
     *
     * @return array
     */
    public static function filtered($classes = array(), $files = array(), &$stack = null)
    {
        $skip_self_trace = 0;
        if ($stack === null) {
            $stack = debug_backtrace();
        }

        $classes = array_merge((array) $classes, array(__CLASS__));
        // Note: in a php backtrace, the class refers to the target of
        // invocation, whereas the file and line indicate where the invocation
        // happened.
        // E.g.
        //     [file] => /mover/backend/test/Belt/Support/TracePrinter.php
        //     [line] => 18
        //     [function] => debug
        //     [class] => Belt\Trace
        // Here, one might expect class to be 'Belt\Trace'
        $files = array_merge((array) $files, array(__FILE__));

        $filtered_stack = array();
        foreach ($stack as $index => $item) {
            $file = Arrays::get($item, 'file', null);
            // So given the long comment above, we cheat by looking
            // at our caller's class.
            $class = Arrays::get($stack, ($index).'.class', null);
            if (
                (in_array($file, $files))
                // ||
                // ($index != 0 && in_array($class, $classes))
            ) {
                $skip_self_trace++;
                continue;
            } else {
                $filtered_stack[] = $item;
            }
        }
        return $filtered_stack;
    }

    public static function simplified($depth = 5)
    {
        return self::simplify(array_slice(debug_backtrace(), 1, $depth));
    }

    public static function simplify(&$stack)
    {
        $result = array();
        foreach ($stack as $index => $item) {
            $result[] = static::formatter($item, $index);
        }

        return array_filter($result);
    }

    protected static function formatter($item, $index)
    {
        if (isset($item['file'])) {
            $file = basename($item['file']);
            error_log(Dump::pretty($item,2));
            return "{$file}:{$item['line']}:{$item['function']}";
        } else {
            $file = basename($item['class']);
            return "{$item['function']}:$file";
        }
    }
}
