<?php namespace Belt;

/**
 * An alternative to objects/arrays for times when you want a more explicit schema
 * defined but don't want the boilerplate.
 *
 * Simply subclass SchemaObject and provide your own `$attribute_spec`
 *
 * @warning Reads, writes, and construction will throw Exceptions if invalid attributes
 * or values are used. See the magic getter and setter for details.
 */
class SchemaObject
{
    protected static $attribute_spec = array();

    protected $attribute_data = array();

    public function __construct()
    {
        foreach (func_get_args() as $arg) {
            if ($arg instanceof SchemaObject) {
                $this->fromSchemaObject($arg);
            } else {
                $this->fromArray($arg);
            }
        }
    }

    protected function isValidAttribute($attribute)
    {
        return isset(static::$attribute_spec[$attribute]);
    }

    protected function getSpec($attribute)
    {
        return isset(static::$attribute_spec[$attribute])
            ? static::$attribute_spec[$attribute] : null;
    }

    protected function isValidValue($attribute, $value)
    {
        $spec = $this->getSpec($attribute);

        if ($spec === null) {
            return false;
        }

        if (!isset($spec['type'])) {
            return true;
        }

        // Nulls are allowed for all types unless explicitly forbidden with
        // 'allow_null' => false.
        if (is_null($value)) {
            return !(
                array_key_exists('allow_null', $spec)
                && $spec['allow_null'] === false
            );
        }

        $value_type = gettype($value);

        switch($spec['type']) {
            case 'boolean':
                return is_bool($value);
            case 'string':
                return is_string($value);
            case 'integer':
                return is_integer($value);
            case 'float':
                return is_float($value);
            case 'array':
                return is_array($value);
            default:
                if ($value_type !== 'object') {
                    return false;
                } else {
                    return is_a($value, $spec['type']);
                }
        }
    }

    public function __get($attribute)
    {
        if (isset($this->attribute_data[$attribute])) {
            return $this->attribute_data[$attribute];
        } elseif (isset(static::$attribute_spec[$attribute])
          && isset(static::$attribute_spec[$attribute]['default'])) {
          return static::$attribute_spec[$attribute]['default'];
        } else {
            return null;
        }
    }

    /**
     * @throws InvalidArgumentException When an invalid value is written.
     * @throws UnexpectedValueException When an unspecified attribute is
     * read or written.
     */
    public function __set($attribute, $value)
    {
        if (!$this->isValidAttribute($attribute)) {
            throw new \UnexpectedValueException("Unknown attribute: $attribute");
        }

        if (!$this->isValidValue($attribute, $value)) {
            throw new \InvalidArgumentException("Invalid value for $attribute provided ".Dump::pretty($value, 1));
        }

        $this->attribute_data[$attribute] = $value;
    }

    public function toArray()
    {
        return $this->attribute_data;
    }

    public function fromArray($values)
    {
        foreach ($values as $attribute => $value) {
            $this->$attribute = $value;
        }
    }

    public function fromSchemaObject(SchemaObject $schema_object)
    {
        $this->fromArray($schema_object->toArray());
    }
}
