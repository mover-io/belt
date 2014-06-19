<?php namespace Belt;

use Belt\Trace;
use \DateTime;

class Profile {
    public static function run($fn) {
        $memory_before = memory_get_usage();
        $time_before = microtime(true);

        $elapsed = function() use ($time_before) {
            $elapsed = microtime(true) - $time_before;
            $ms = $elapsed - floor($elapsed);
            $seconds = sprintf("%d.%d",$elapsed,$ms*10000);
            $formatted = "$seconds seconds";
            return $formatted;
        };

        $marks = array();
        $markFunc = function($label = "mark") use (&$marks, $elapsed) {
            $memory = Profile::humanizeMemory();
            $formatted = $elapsed();
            $marks[] = "$label: $formatted - $memory";
        };

        Trace::traceOffset(1)->debug('Staring profile.');

        // The callback provided to the profile closure will trigger
        // a measurement.
        $fn($markFunc);

        $memory_after = memory_get_usage();

        $memory_peak = memory_get_peak_usage(true);

        Trace::traceOffset(1)->debug(array(
            'elapsed' => $elapsed(),
            'memory before' => self::humanizeMemory($memory_before),
            'memory after' => self::humanizeMemory($memory_after),
            'memory peak' => self::humanizeMemory($memory_peak),
            'marks' => $marks
        ));
    }

    public static function humanizeMemory($bytes=null) {
        if($bytes === null) {
            $bytes = memory_get_usage();
        }
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($bytes/pow(1024,($i=floor(log($bytes,1024)))),2).' '.$unit[$i];
    }
}
