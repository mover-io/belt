<?php namespace Belt;

/**
 *
 * Implementation Notes:
 * __call and __callStatic are used to correctly dispatch static / nonstatic
 * calls to otherwise identical methods. _callStatic simply creates an instance
 * of a Trace object behind the scenes.
 *
 * This allows for invocation such as:
 *
 *    Trace::banner()->width(100)->color()->debug("Hello");
 *
 * You can transpose the initial static method call with any of the normal
 * methods. E.g.:
 *
 *    Trace::debug("World");
 *
 */
class Trace {

  public static $defaults = array(
      'tint' => false,
      'banner' => array(
        'width' => 77,
        'enabled' => false
      ),
      'inspect' => array(
        'color' => true,
        'depth' => 5,
      ),
      'trace' => array(
        'enabled' => true,
        'depth' => 1,
        'offset' => 0
      ),
      'separator' => ' « ',
      'timestamp' => array(
        'enabled' => true
      ),
      'printer' => 'error_log'
  );

  public function __construct($options=array()) {
    $this->options = array_merge($options, static::$defaults);
  }

  public static function __callStatic($name, $arguments) {
    $trace = new Trace();
    if(!method_exists($trace,"_".$name)) {
      $trace->_debug("Ooops - can't do `$name`");
      return $trace;
    }
    return call_user_func_array(array($trace,"_".$name), $arguments);
  }

  public function __call($name, $arguments) {
    if(method_exists($this,"_".$name)) {
      return call_user_func_array(array($this,"_".$name), $arguments);
    } else {
      $this->_debug("Ooops - can't do `$name`");
      return $this;
    }
  }

  // Utility Methods
  //################

  public function _stack($depth=5,$start=1,$formatter=null) {
    if($formatter == null) {
      $formatter = function($item, $index) {
        if(isset($item['file'])) {
          $file = basename($item['file']);
          return "{$file}:{$item['line']}";
        } else {
          return "{$item['function']}";
        }
      };
    }
    $bt = array_slice(debug_backtrace(),$start,$depth);
    $result = array();
    foreach($bt as $index=>$item) {
      $result[] = $formatter($item,$index);
    }
    return array_filter($result);
  }

  public function _stackString($depth=5,$start=3,$joinchar=" « ") {
    return implode($joinchar,$this->_stack($depth,$start));
  }

  // Output Methods
  //###############

  protected function writeLn($string)
  {
      $printer = $this->options['printer'];
      if ($printer === 'error_log') {
          error_log($string);
      } elseif (is_string($printer) && Text::startsWith($printer, 'file://')) {
          file_put_contents(Text::ensureNoPrefix($printer, 'file://'), $string."\n", FILE_APPEND);
      } elseif (is_callable($this->options['printer'])) {
          call_user_func($this->options['printer'], $string);
      }
  }

  public function _stackDump() {
      $this->writeLn($this->_generateLine("", 5));
  }

  public function _debug($value, $depth=1) {
    $result = $this->_generateLine(
      Dump::pretty(
        $value,
        $this->options['inspect']['depth'],
        array(),
        array(),
        1,
        $this->options['inspect']['color']
      )
    );
    if($this->options['tint']) {
      $result = Text::tint($this->options['tint'], $result);
    }

    $this->writeLn($result);
  }


  // String Generation
  //##################

  /**
   * Common method for wrapping a string in the configured banner.
   */
  public function _generateBanner($string) {
    $banner = str_repeat("#",$this->options['banner']['width']);
    return $banner."\n".$string."\n".$banner;
  }

  /**
   * Common method for generating the main logging line.
   */
  public function _generateLine($string, $start=6) {
    if($this->options['trace']['enabled']) {
      $trace = $this->_stackString($this->options['trace']['depth'], $start+$this->options['trace']['offset'], $this->options['separator']);
    } else {
      $trace = "";
    }

    $result = "";

    if($this->options['timestamp']['enabled']) {
      $result .= date('c').' ';
    }

    $result .= implode($this->options['separator'],array_filter(array($trace,$string)));

    if($this->options['banner']['enabled']) {
      $result = $this->_generateBanner($result);
    }

    return $result;
  }

  // Fluent Configuration Methods
  //############################

  /**
   * Enable colorization. Not implemented.
   *
   * @fluent
   */
  public function _color() {
    $this->options['inspect']['color'] = true;
    return $this;
  }

  /**
   * Use file based output. Useful to work around nginx's log swallowing
   * annoyingness.
   *
   * @fluent
   */
  public function _toFile($file = null) {
    $file = $file ?: sys_get_temp_dir() . '/belt_trace.log';
    $this->options['printer'] = 'file://'.$file;
    return $this;
  }

  /**
   * Depth of included stack traces.
   *
   * @fluent
   */
  public function _traceDepth($depth) {
    $this->options['trace']['depth'] = $depth;
    return $this;
  }

  /**
   * Offset of included stack trace e.g. if you're logging in a
   * common function (such as commandDebug) and are more interested in
   * the name of a method up the stack.
   *
   * commandDebug - offset 0
   * getRelativeDir - offset 1
   *
   * @fluent
   */
  public function _traceOffset($offset) {
    $this->options['trace']['offset'] = $offset;
    return $this;
  }


  /**
   * Maximum depth of object dumps.
   *
   * @fluent
   */
  public function _depth($depth) {
    $this->options['inspect']['depth'] = $depth;
    return $this;
  }

  /**
   * Width of the banner.
   *
   * @fluent
   */
  public function _width($width) {
    $this->options['banner']['width'] = $width;
    return $this;
  }

  /**
   * Whether banners are enabled.
   *
   * @fluent
   */
  public function _banner() {
    $this->options['banner']['enabled'] = true;
    return $this;
  }

  /**
   * Whether timestamps are enabled.
   *
   * @fluent
   */
  public function _timestamp() {
    $this->options['timestamp']['enabled'] = true;
    return $this;
  }

  /**
   * Tints the plain color to use $color.
   *
   * @fluent
   */
  public function _tint($color) {
    $this->options['tint'] = $color;
    return $this;
  }
}
