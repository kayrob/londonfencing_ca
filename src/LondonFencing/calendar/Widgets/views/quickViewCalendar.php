<?php
if (!isset($cal)){
	$cal = new calendarWidgets($db);
	$cal->url = '/events';
}
$cal->quick_view_dialog();
$cal->quick_view_calendar();
?>