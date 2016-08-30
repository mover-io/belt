<?php namespace Belt;

use Belt\Trace;
use Belt\Arrays;
use DateTime;

class Profile {
    public static function run($fn, $options = array()) {
        $memory_before = memory_get_usage();
        $time_before = microtime(true);

        $elapsed = function() use ($time_before) {
            return microtime(true) - $time_before;
        };

        $marks = array();
        $markFunc = function($label = "mark") use (&$marks, $elapsed) {
            $memory = Profile::humanizeMemory();
            $formatted_time_delta = $elapsed();
            $marks[] = "$label: $formatted_time_delta - $memory";
        };

        if (Arrays::get($options, 'debug', false)) {
            Trace::traceOffset(1)->debug('Staring profile.');
        }

        // The callback provided to the profile closure will trigger
        // a measurement.
        $fn($markFunc);

        $memory_after = memory_get_usage();
        $memory_peak  = memory_get_peak_usage(true);

        $data = array(
            'elapsed' => $elapsed(),
            'memory_before' => $memory_before,
            'memory_after' => $memory_after,
            'memory_peak' => $memory_peak,
            'marks' => $marks
        );

        if (Arrays::get($options, 'debug', false)) {
            Trace::traceDepth(2)->traceOffset(1)->debug($data);
        }

        return $data;
    }
}
