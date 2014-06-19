<?php namespace PHPUsable;

require __DIR__ . '/../vendor/autoload.php';

use Belt\SchemaObject;

class NaturalNumber
{
    public $value;
    public function __construct($value)
    {
        $this->value = $value;
    }
}

class PersonConfig extends SchemaObject
{
    protected static $attribute_spec = array(
        'first_name' => array('type' => 'string'),
        'age' => array('type' => 'PHPUsable\NaturalNumber'),
        'gender' => array('default' => 'M')
    );
}

class SchemaObjectTest extends PHPUsableTest
{
    public function tests()
    {
        PHPUsableTest::$current_test = $this;

        describe('SchemaObject', function ($test) {
            it('should allow defined keys', function ($test) {
                $person_config = new PersonConfig();
                $person_config->first_name = 'Sparks';
                $person_config->age = new NaturalNumber(5);
                $test->expect($person_config->first_name)->to->eql('Sparks');
                $test->expect($person_config->age)->to->eql(new NaturalNumber(5));
            });

            it('should use defaults', function ($test) {
                $person_config = new PersonConfig();
                $test->expect($person_config->gender)->to->eql('M');
            });

            it('should throw when providing unexpected key/value pairs', function ($test) {
                $person_config = new PersonConfig();
                $test->expect(function () use ($person_config) {
                    $person_config->weight = 150;
                })->to->throw('\UnexpectedValueException');
            });

            it('should enforce types if specified', function ($test) {
                $person_config = new PersonConfig();
                $test->expect(function () use ($person_config) {
                    $person_config->age = 5;
                })->to->throw('\InvalidArgumentException');
            });

            it('should allows nulls when a class type is used', function ($test) {
                $person_config = new PersonConfig();
                $test->expect(function () use ($person_config) {
                    $person_config->age = null;
                })->to->not->throw('\Exception');
            });

            it('should ignore types if not specified', function ($test) {
                $person_config = new PersonConfig();
                $test->expect(function () use ($person_config) {
                    $person_config->gender = 5;
                    $person_config->gender = 'M';
                    $person_config->gender = array();
                })->to->not->throw('\Exception');
            });
        });
    }
}
