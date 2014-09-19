<?php namespace PHPUsable;

require __DIR__ . '/../vendor/autoload.php';

use Belt\Profile;

class ProfileTest extends PHPUsableTest
{
    public function tests()
    {
        PHPUsableTest::$current_test = $this;

        describe('Profile', function ($test) {
            describe('run', function ($test) {
                it('should record memory usage', function ($test) {
                    $data = Profile::run(function() {
                    });
                    $test->assertArrayHasKey('memory before', $data);
                    $test->assertArrayHasKey('memory after', $data);
                    $test->assertArrayHasKey('memory peak', $data);
                });

                it('should record elapsed times', function ($test) {
                    $data = Profile::run(function() {
                    });
                    $test->assertArrayHasKey('elapsed', $data);
                });

                it('should record granular marks', function($test) {
                  $data = Profile::run(function ($markFunc) {
                    $markFunc();
                    $markFunc();
                    $markFunc();
                  });

                  $test->assertArrayHasKey('marks', $data);
                  $test->assertEquals(3, count($data['marks']));
                });
            });
        });
    }
}
