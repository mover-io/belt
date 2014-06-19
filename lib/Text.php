<?php namespace Belt;

class Text {
  /*
   * Ensure a string ends with a suffix
   *
   * Will return the original string if it already is terminated by the desired
   * suffix, or will add the suffix if it is not.  This is a case sensitive
   * function.
   *
   * @param string $string the string that should be suffix terminated
   * @param string $suffix the desired suffix that should terminate the string
   *
   * @return string returns the suffix terminated form of the string
   */
  static public function ensureSuffix($string, $suffix) {
      if(Text::endsWith($string, $suffix)) {
          return $string;
      } else {
          return $string . $suffix;
      }
  }

  /*
   * Ensure a string does not start with a prefix
   *
   * Will return the original string if it already is not started by the provided
   * prefix , or will remove the prefix if it is present.  This is a case
   * sensitive function.
   *
   * @param string $string the string that should not be prefix started
   * @param string $prefix the desired prefix that should be removed if present
   *
   * @return string returns the non-prefix started form of the string
   */
  static public function ensureNoSuffix($string, $prefix) {
      if(!Text::endsWith($string, $prefix)) {
          return $string;
      } else {
          return mb_substr($string, 0, -mb_strlen($prefix)) ?: '';
      }
  }

  /*
   * Ensure a string does not start with a prefix
   *
   * Will return the original string if it already is not started by the provided
   * prefix , or will remove the prefix if it is present.  This is a case
   * sensitive function.
   *
   * @param string $string the string that should not be prefix started
   * @param string $prefix the desired prefix that should be removed if present
   *
   * @return string returns the non-prefix started form of the string
   */
  static public function ensureNoPrefix($string, $prefix) {
      if(!Text::startsWith($string, $prefix)) {
          return $string;
      } else {
          return mb_substr($string, mb_strlen($prefix)) ?: '';
      }
  }

  /*
   * Ensure a string starts with a prefix.
   *
   * Will return the original string if it already is started by the desired
   * prefix, or will add the prefix if it is not.  This is a case sensitive
   * function.
   *
   * @param string $string the string that should be prefix terminated
   * @param string $prefix the desired prefix that should start the string
   *
   * @return string returns the prefix started form of the string
   */
  static public function ensurePrefix($string, $prefix) {
      if(Text::startsWith($string, $prefix)) {
          return $string;
      } else {
          return $prefix . $string;
      }
  }

  /*
   * Checks if a string is started by the provided prefix
   *
   * @param string $string the string to check for the provided prefix
   * @param string $prefix the prefix to check against the string
   *
   * @return bool returns true if the string strats with the prefix, false
   * otherwise.
   */
  static public function startsWith($string, $prefix) {
    return ($prefix === "" ) || (mb_strpos($string, $prefix) === 0);
  }

  /*
   * TODO test this!
   *
   * Checks if a string ends with a given suffix.
   *
   * @param string $string the string to check for the provided prefix
   * @param string $suffix the suffix to check against the string
   *
   * @return bool returns true if the string is terminated by the suffix, false otherwise
   */
  static public function endsWith($string, $suffix) {
    return ($suffix === "" ) || (substr($string, -strlen($suffix)) === $suffix);
  }

  /**
   * Generates a random string using [a-Z0-9].
   */
  static public function randomString( $length ) {
      $transactionhars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
      $size = strlen( $transactionhars );
      $str = '';
      for( $i = 0; $i < $length; $i++ )
        $str .= $transactionhars[ rand( 0, $size - 1 ) ];

      return $str;
  }

  /**
   * Wraps string in bash shell color escape sequences for the provided color.
   *
   * @param $color string|integer If a string is provided, we lookup the colors
   * using the $colors table in the function body below. For precise control,
   * an integer will yield a precise color code.
   * @param $string string The string to colorize.
   * @param $conditional A flag parameter which may be passed in to
   * transparently enable/disable colorization.
   *
   * @return The colorized string.
   */
  static public function shellColor($color, $string, $conditional=true) {
    if(!$conditional) {
      return $string;
    }
    $color = self::shellColorCode($color);
    return $color.$string.self::shellColorCode('plain');
  }

  public static function tint($color, $string) {
      $tint_color = self::shellColorCode($color);
      $base_color = self::shellColorCode('plain');
      // return preg_replace($string,"/\x1B\[0m/g", '');
      return $tint_color.str_replace($base_color,$base_color.$tint_color,$string).$base_color;
  }

  public static function shellColorCode($color) {
    // See: http://www.bashguru.com/2010/01/shell-colors-colorizing-shell-scripts.html
    $colors = array(
      'black'=>"\033[30m",
      'red'=>"\033[31m",
      'green'=>"\033[32m",
      'yellow'=>"\033[33m",
      'blue'=>"\033[34m",
      'magenta'=>"\033[35m",
      'cyan'=>"\033[36m",
      'white'=>"\033[37m",
      'gray'=>"\033[90m",
      'plain'=>"\033[0m",

      'bg_black'=>"\033[40m",
      'bg_red'=>"\033[41m",
      'bg_green'=>"\033[42m",
      'bg_yellow'=>"\033[43m",
      'bg_blue'=>"\033[44m",
      'bg_magenta'=>"\033[45m",
      'bg_cyan'=>"\033[46m",
      'bg_gray'=>"\033[100m",
      'white'=>"\033[47m",

      'bold_black'=>"\033[1;30m",
      'bold_red'=>"\033[1;31m",
      'bold_green'=>"\033[1;32m",
      'bold_yellow'=>"\033[1;33m",
      'bold_blue'=>"\033[1;34m",
      'bold_magenta'=>"\033[1;35m",
      'bold_cyan'=>"\033[1;36m",
      'bold_white'=>"\033[1;37m",
      'bold_plain'=>"\033[1;0m",
    );
    if(is_numeric($color)) {
      // How do these look?
      // for code in {0..255}; do echo -e "\e[38;05;${code}m $code: Test"; done
      // http://www.commandlinefu.com/commands/view/5879/show-numerical-values-for-each-of-the-256-colors-in-bash
      $color = "\033[38;05;{$color}m";
    } else {
      $color = $colors[$color];
    }
    return $color;
  }

  /**
   * Strips bash colors codes from a string. Useful if you want to count the
   * actual number of characters or remove formattign for non-colored output
   * such as log files.
   */
  static public function stripColors($string) {
    return preg_replace($string,"/\x1B\[\d+m/g", '');
  }

  /**
   * Converts camel-case to corresponding underscores.
   *
   * @param $str
   *
   * @example helloWorld -> hello_world
   * @return string
   */
  static public function fromCamel($str) {
      $str[0] = strtolower($str[0]);
      return preg_replace_callback('/([A-Z])/', function($char) {return "_" . strtolower($char[1]);}, $str);
  }

  /**
   * Converts underscores to a corresponding camel-case string.
   *
   * @param $str
   * @param $capitalise_first_char
   *
   * @example this_is_a_test -> thisIsATest
   * @return string
   */
  static public function toCamel($str, $capitalise_first_char = false) {
      if($capitalise_first_char) {
        $str[0] = strtoupper($str[0]);
      }
      return preg_replace_callback('/_([a-z])/',function($char) {return strtoupper($char[1]);}, $str);
  }
}
