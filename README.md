# belt
---

A PHP developer toolbelt full of rainbows and awesome output!

Created with love by [@jacobstr](https://github.com/jacobstr) and tried and trusted at [@mover](https://github.com/mover-io).

### Classes

#### Trace

Provides Error, Logging, and CLI debugging tools through intelligent and
colorized stack, source, and result formatted outputs.  Better than straight up
error_log because:
  1. You always know the line number where the log statement took place, so it's
     easier to remove later.
  2. With a larger traceDepth, you can identity the call sites of your code.
  3. The tinting options help during console-debugging sessions that escalate
     to the stage that you're dumping a lot of data and need a way to visually
     scan for particular messages.

Example:

```php
Belt\Trace::debug($your_result);
// with Stack Trace
Belt\Trace::traceDepth(7)->debug($your_result);
```

#### Text

Provides string format helpers for frequent string helpers.

Example:

```php
$route = Belt\Text::ensureNoPrefix($route, "/v2/");
$route = Belt\Text::ensureNoSuffix($route, "/");
```

#### Arrays

Provides array helpers for common actions.

Example:

```php
// Safe getter that returns null if no value for key
$value = Belt\Array::get($array, 'key', null)
```

#### Profile

Provides run time profiling for memory and execution time on function calls.

#### SchemaObject

Facebook's React has a similar concept, known as a _shape_. `SchemaObject` is useful when you want
to wrap a plain old array with a type. This in turn, is useful when you want to type hint a function. Instead of declaring a `$user_info` parameter, you can specify `UserInfo $user_info`. Cross-referencing `UserInfo` immediately tells you the key/value pairs that your object should have. Further, the SchemaObject may assist in validation so that the values corresponding to a given key have the correct type.

```php
<?php namespace Mover\Connectors\Sharing;

use Belt\SchemaObject;
use Belt\Arrays;

class UserInfo extends SchemaObject
{
    protected static $attribute_spec = array(
        'first_name'  => array('type' => 'string'),
        'last_name'   => array('type' => 'string'),
        'email'       => array('type' => 'string'),
        // E.g. this could be an id from the migration source system.
        'external_id' => array('type' => 'string'),
        // The users id' in the active connector.
        'id'          => array('type' => 'string'),
        // The raw data from the third party if available. This is optional.
        // Code should never rely on a raw field. We may use it for debugging
        // and introspection.
        'raw'         => array('type' => 'array')
    );

    public function fullname()
    {
        return implode(
            ' ',
            array_filter(
                array($this->first_name, $this->last_name)
            )
        );
    }

    /**
     * Splits a full name (first + last name) into an array of two parts, the
     * first and last name. Note that this assumes only a single space separates
     * the first and last name.
     */
    public static function splitName($fullname)
    {
          $name_parts = explode(" ", $fullname, 2);
          $first_name = Arrays::get($name_parts, 0, "");
          $last_name  = Arrays::get($name_parts, 1, "");
          return array($first_name, $last_name);
    }
}
```