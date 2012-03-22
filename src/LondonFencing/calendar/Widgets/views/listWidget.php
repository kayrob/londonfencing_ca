<?php
require_once(dirname(dirname(__DIR__)))."/calendar.php";
use LondonFencing\calendar\Widgets AS cWid;
if (!isset($cal)){
    $cal = new cWid\calendarWidgets($db);
    $cal->url = '/events';
}
$eventList = $cal->upcoming_events_widget(10);
?>
<div id="listViewWidget">
<div class="eventWidget">
<h3>Events</h3>
<?php
if (is_array($eventList)){
	foreach($eventList as $item){
		echo "<div class=\"eventWidgetItem\">
		<div class=\"dateBox\">
		<span class=\"month\">" . strtoupper(date('M', $item["start"])) . "</span>
		<span class=\"date\">" . date('d', $item["start"]) . "</span>
		</div>
		<a href=".$item["link"].">".trim($item["eventTitle"])."</a>
		</div>";
	}
	echo "<br /><a class=\"prettyButtonBrown\" href=\"". $this->url ."\">More Events</a></div>";
}
?>
</div>