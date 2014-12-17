<?php namespace PHPUsable;

require __DIR__ . '/../vendor/autoload.php';

use Belt\Text as Text;

class TextTest extends PHPUsableTest {
  public function tests() {
    PHPUsableTest::$current_test = $this;

    describe('functions', function($test) {
      describe('startsWith', function($test) {
        it('should return true in the positive case',function($test){
          $test->expect(Text::startsWith('helloWorld','hello'))->to->eql(true);
        });

        it('should return false in the negative case',function($test){
          $test->expect(Text::startsWith('helloWorld','dfsf'))->to->eql(false);
        });
      });

      describe('endsWith', function($test) {
        it('should return true in the positive case',function($test){
          $test->expect(Text::endsWith('helloWorld','World'))->to->eql(true);
        });

        it('should return false in the negative case',function($test){
          $test->expect(Text::endsWith('helloWorld','DWorld'))->to->eql(false);
        });
      });

      describe('ensureNoSuffix', function($test) {
        it('removes a suffix if present',function($test){
          $test->expect(Text::ensureNoSuffix('batman.jpg','.jpg'))->to->eql('batman');
        });

        it('does nothing if suffix not present',function($test){
          $test->expect(Text::ensureNoSuffix('batman.jpg','.gif'))->to->eql('batman.jpg');
        });

        it('works in the degenerate case', function($test){
          $test->expect(Text::ensureNoSuffix('b','b'))->to->eql('');
        });
      });

      describe('ensureNoPrefix', function($test) {
        it('removes a prefix if present',function($test){
          $test->expect(Text::ensureNoPrefix('batman.jpg','batman'))->to->eql('.jpg');
        });

        it('does nothing if prefix not present',function($test){
          $test->expect(Text::ensureNoPrefix('batman.jpg','spiderman'))->to->eql('batman.jpg');
        });

        it('works in the degenerate case', function($test){
          $test->expect(Text::ensureNoPrefix('b','b'))->to->eql('');
        });
      });

      describe('fromCamel', function($test) {
        it('to convert to lower_case format', function($test) {
          $test->expect(Text::fromCamel("TestController"))->to->be("test_controller");
        });
      });

      describe('toCamel', function($test) {
        it('should convert to camelCase', function($test) {
          $test->expect(Text::toCamel("test_controller"))->to->be("testController");
        });
        it('should convert to CamelCase if capitilization is specified', function($test) {
          $test->expect(Text::toCamel("test_controller", true))->to->be("TestController");
        });
      });
    });
  }
}
