--TEST--
Import of iCalendar events
--FILE--
<?php

class Driver {
    function getCalendar()
    {
        return 'foo';
    }
}
class Prefs {
    function getValue()
    {
        return 0;
    }
}
$prefs = new Prefs;

require 'Date/Calc.php';
require 'Horde/Date.php';
require 'Horde/Date/Recurrence.php';
require 'Horde/Util.php';
require 'Horde/iCalendar.php';

$iCal = new Horde_iCalendar();
$iCal->parsevCalendar(file_get_contents(dirname(__FILE__) . '/fromicalendar.ics'));
$components = $iCal->getComponents();
$iCal2 = new Horde_iCalendar();

define('KRONOLITH_BASE', dirname(__FILE__) . '/../..');
require KRONOLITH_BASE . '/lib/Kronolith.php';
require KRONOLITH_BASE . '/lib/Driver.php';
foreach ($components as $content) {
    if (is_a($content, 'Horde_iCalendar_vevent')) {
        $event = new Kronolith_Event(new Driver);
        $event->fromiCalendar($content);
        echo $event->start->rfc3339DateTime() . "\n";
        echo $event->end->rfc3339DateTime() . "\n";
        var_export($event->isAllDay());
        echo "\n";
        if ($event->recurs()) {
            echo $event->recurrence->toRrule20($iCal2) . "\n";
            var_dump($event->recurrence->exceptions);
        }
        echo "\n";
    }
}

?>
--EXPECT--
2010-11-01T10:00:00
2010-11-01T11:00:00
false
FREQ=WEEKLY;INTERVAL=1;BYDAY=MO;UNTIL=20101129T230000Z
array(2) {
  [0]=>
  string(8) "20101108"
  [1]=>
  string(8) "20101122"
}
