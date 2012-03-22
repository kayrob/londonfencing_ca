<?php
require_once(dirname(dirname(__DIR__)))."/calendar.php";
use LondonFencing\calendar\Widgets AS cWid;
if (!isset($cal)){
        $cal = new cWid\calendarWidgets($db);
        $cal->url = "/eventsr";
}
$eventRequested = $cal->get_event_details_byID($_GET);
$dlgTitle = (isset($eventRequested[6]))?$eventRequested[6]:"title";
echo '<style type="text/css">';
$cal->buildCalendarCSS();
echo '</style>';
?>
<div id="calendarWrap">
	<!-- view event details -->
	<div id="dlgEventDetails" title="<?php echo($dlgTitle);?>" style="display:none">
	<?php (!isset($eventRequested))?$cal->display_dlg_events_details(true):$cal->display_dlg_events_details(true,$eventRequested[0],$eventRequested[1],$eventRequested[2],$eventRequested[3],$eventRequested[4],$eventRequested[5],$_GET['event']);?>
	</div>
	<!--end section of dialog boxes -->
	<div id='leftCol'>
		<p>Current Calendars:</p>
		<?php (isset($_GET['view']))?$cal->get_calendars_view($_GET['view']):$cal->get_calendars_view(); ?>
	</div>
	<div id='centreCol'>
		<div id='calendarTitle'><h2 class='fc-header-title'></h2></div>
		<!--calendar div should remain empty for fullCalendar.js to fill-->
		<div id='calendar'></div>
		<div id='eventsWidget'><?php (isset($_GET['view']))?$cal->fullCalendar_upcoming_events(10,array($_GET['view'])):$cal->fullCalendar_upcoming_events(10); ?></div>
	</div>
<div class="clearfix"></div> 
</div>
