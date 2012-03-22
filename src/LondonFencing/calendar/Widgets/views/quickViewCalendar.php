<?php
require_once(dirname(dirname(__DIR__)))."/calendar.php";
use LondonFencing\calendar\Widgets AS cWid;
if (!isset($cal)){
    $cal = new cWid\calendarWidgets($db);
    $cal->url = '/events';
}
$cal->quick_view_dialog();
$cal->quick_view_calendar();
?>