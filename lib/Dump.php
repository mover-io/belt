<?php namespace Belt;

class Dump {
  public static function color($subject, $depth = 3)
  {
      return static::pretty($subject, $depth, array(), array(), 1, true);
  }

  /**
   * Depth limited print_r. Returns string instead of echoing by default.
   *
   * @return string
   */
  public static function pretty($subject, $depth = 3, $ignore = array(), $refChain = array(), $current = 1, $colors=false)
  {
      $str = '';
      if ( $depth > 0 && is_object($subject) )
      {
          $str .= Text::shellColor("blue", get_class($subject). " Object (",$colors)."\n";
          $subject = (array) $subject;
          foreach ( $subject as $key => $val )
          {
              if ( is_array($ignore) && ! in_array($key, $ignore, 1) )
              {
                  $str .= str_repeat(" ", $current * 4) . '[';
                  if ( $key{0} == "\0" )
                  {
                      $keyParts = explode("\0", $key);
                      $str .= $keyParts[2] . (($keyParts[1] == '*') ? ':protected' : ':private');
                  }
                  else
                  {
                      $str .= $key;
                  }
                  $str .= '] => ';
                  $str .= self::pretty($val, $depth - 1, $ignore, $refChain, $current + 1, $colors);
              }
          }
          $str .= str_repeat(" ", ($current - 1) * 4) . Text::shellColor("blue",")",$colors). "\n";
          array_pop($refChain);
      }
      else
      {
          if ( $depth > 0 && is_array($subject) )
          {
              $str .= Text::shellColor('yellow','Array (',$colors) . "\n";
              foreach ( $subject as $key => $val )
              {
                  if ( is_array($ignore) && ! in_array($key, $ignore, 1) )
                  {
                      $str .= str_repeat(" ", $current * 4) . '[' . $key . ']' . ' => ';
                      $str .= self::pretty($val, $depth - 1, $ignore, $refChain, $current + 1, $colors);
                  }
              }
              $str .= str_repeat(" ", ($current - 1) * 4) . Text::shellColor('yellow',")",$colors) . "\n";
          }
          else
          {
              if ( is_array($subject) || is_object($subject) )
              {
                  $str .= "Nested Element\n";
              }
              else
              {
                  if($subject === true)
                  {
                      $subject = Text::shellColor('green','true',$colors);
                  }
                  else if ($subject ===  false)
                  {
                      $subject = Text::shellColor('red','false',$colors);
                  }
                  else if ($subject === null) {
                      $subject = Text::shellColor('magenta','null',$colors);
                  }
                  else if (is_float($subject)) {
                      if ($subject > 0) {
                        $formated = number_format($subject);
                      } else {
                        $formated = sprintf("%g", $subject);
                      }
                      $subject = Text::shellColor('cyan', $formatted . ' => :0x'. bin2hex(pack('d', $subject)), $colors);
                  }
                  else if (is_numeric($subject)) {
                      $subject = Text::shellColor('blue',$subject,$colors);
                  }
                  else if (is_string($subject)) {
                    if($colors) {
                      $matches = array();
                      preg_match('/^(\s*)?(.*?)(\s*)?$/s',$subject,$matches);
                      $subject = Text::shellColor('bg_red',$matches[1],$colors) .
                        $matches[2] .
                        Text::shellColor('bg_red',$matches[3],$colors);
                    }
                  }
                  $str .= $subject . "\n";
              }
          }
      }

      // Trim trailing newlines.
      if($current == 1) { $str = rtrim($str,"\n"); }

      return $str;
  }
}
