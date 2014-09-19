<?php namespace PHPUsable;

require __DIR__ . '/../vendor/autoload.php';

use Belt\Arrays;

class ArraysTest extends PHPUsableTest
{
    public function tests()
    {
        PHPUsableTest::$current_test = $this;

        describe('Arrays', function ($test) {
            describe('Arrays', function ($test) {
                it('should not convert scalars to arrays', function ($test) {
                    $a = array(
                        'level 1' => array(
                            'level 2' => 2
                        )
                    );
                    $b = array(
                        'level 1' => array(
                            'level 2' => 5
                        )
                    );
                    $test->expect(Arrays::array_merge_recursive_distinct($a, $b))->to->eql(
                        array(
                            'level 1' => array(
                                'level 2' => 5
                            )
                        )
                    );
                });

                it('should unionize arrays', function ($test) {
                    $a = array(
                        'level 1' => array(
                            'level 2' => array(2,3)
                        )
                    );
                    $b = array(
                        'level 1' => array(
                            'level 2' => array(5)
                        )
                    );
                    $test->expect(Arrays::array_merge_recursive_distinct($a, $b))->to->eql(
                        array(
                            'level 1' => array(
                                'level 2' => array(5, 3)
                            )
                        )
                    );
                });
            });
        });
    }
}
