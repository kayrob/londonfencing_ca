<?php
$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require_once $root ."/inc/init.php";
require_once dirname(dirname(__DIR__)) ."/Calendar.php";
require_once dirname(__DIR__)."/adminCalendar.php";

$quipp->css[] = "/src/LondonFencing/calendar/vendors/fullcalendar/fullcalendar.css";
$quipp->css[] = "/src/LondonFencing/calendar/assets/css/admin.css";
$quipp->js['footer'][] = "/src/LondonFencing/calendar/vendors/fullcalendar/fullcalendar.min.js";
$quipp->js['footer'][] = "/src/LondonFencing/calendar/vendors/jscolor/jscolor.js";
$quipp->js['footer'][] = "/src/LondonFencing/calendar/assets/js/calendar.js";
$quipp->js['footer'][] = "/src/LondonFencing/calendar/assets/js/adminCalendar.js";
include $root. "/admin/templates/header.php";

$months = array("01"=>"Jan","02"=>"Feb","03"=>"Mar","04"=>"Apr","05"=>"May","06"=>"Jun","07"=>"Jul","08"=>"Aug","09"=>"Sep","10"=>"Oct","11"=>"Nov","12"=>"Dec");
$year = Date("Y");
$hours = array("01"=>"1","02"=>"2","03"=>"3","04"=>"4","05"=>"5","06"=>"6","07"=>"7","08"=>"8","09"=>"9","10"=>"10","11"=>"11","00"=>"12");
$cal = new adminCalendar($db);
echo '<style type="text/css">';
$cal->buildCalendarCSS();
echo '</style>';
?>

<div id="adminCalendarWrap">
<!--start section of dialog boxes -->
<!--create a new calendar-->
<div id="dlgNewCalendar" title="Create a New Calendar" style="display:none">
	<img src="/src/LondonFencing/calendar/assets/img/ajax-loader.gif" alt="Please Wait" style="display:none" id="imgNewCalLoad" />
	<p class="message"></p>
	<form name="frmNewCalendar" id="frmNewCalendar" action="" method="post">
		Calendar Name: <input type="text" name="calendarName" id="calendarName" size="20" /><br />
		Highlight Colour: <input type="text" id="backgroundColorNew" name="backgroundColorNew" size="10" value="#cccccc" class="color" />
		<div id="colorPickerNew"></div>
		<input type="submit" value="Save" name="btnSaveNewCalendar" id="btnSaveNewCalendar" />&nbsp;<input type="button" value="Cancel" name="btnCancelNewCalendar" id="btnCancelNewCalendar" />
	</form>
</div>
<!--edit calendar -->
<div id="dlgEditCalendar" title="Edit Calendar" style="display:none">
	<img src="/src/LondonFencing/calendar/assets/img/ajax-loader.gif" alt="Please Wait" style="display:none" id="imgEditCalLoad" />
	<p class="message"></p>
	<form name="frmEditCalendar" id="frmEditCalendar" action="" method="post">
		Calendar Name: <span id="currentCalendarName"></span>
		Highlight Colour: <span id="currentCalendarColor"></span><br />
		<input type = "button" name="btnEditCalendar" id="btnEditCalendar" value="Edit" />&nbsp;<input type = "button" name="btnDeleteCalendar" id="btnDeleteCalendar" value="Delete" />
		<div id="calEditElements" style="display:none">
			<hr />
			New Calendar Name: <input type="text" name="calendarName" id="calendarNameEdit" size="20" value="" /><br />
			New Highlight Colour: <input type="text" id="backgroundColorEdit" name="backgroundColorEdit" size="10" value="" class="color" />
			<input type="hidden" value="" name="editAction" id="calEditAction" />
			<input type="hidden" value="" name="calendarID" id="calEditID" />
		</div>
		<div id="calEditButtons" style="display:none">
			<input type="submit" value="Save" name="btnSaveEditCalendar" id="btnSaveEditCalendar" />&nbsp;<input type="button" value="Cancel" name="btnCancelEditCalendar" id="btnCancelEditCalendar" />
		</div>
	</form>
</div>
<!-- add new event/edit event -->
<div id="dlgAddEditEvent" title="Events" style="display:none">
	<img src="/src/LondonFencing/calendar/assets/img/ajax-loader.gif" alt="Please Wait" style="display:none" id="imgEditImgLoad" />
	<p class="message"></p>
	<form name="frmEditEvents" id="frmEditEvents" action="" method="post">
		<table>
		<tbody id="tbEditMain">
		<tr><td>Title:</td><td><input type="text" name="eventTitle" id="eventTitle" value="" /></td></tr>
		<tr>
			<td>Calendar:</td>
			<td><select name="calendarID" id="calendarID">
				<option value="">--Select--</option>
				<?php echo($cal->display_calendar_list_events_form()); ?>
				</select>
			</td>
		</tr>
		<tr><td>Description:</td><td><textarea name="description" id="description" rows="3" cols="20"></textarea></td></tr>
		<tr><td>Location:</td><td><input type="text" name="location" id="location" value="" /></td></tr>
		<tr><td>Start Date:</td>
			<td>
			<input type="text" name="startDate" id="startDate" value="" />&nbsp;<img src="/src/LondonFencing/calendar/assets/img/calendar_icon.gif" alt="Select End Date" id="imgCalStartDate" style="cursor:pointer" />
			</td>
		</tr>
		<tr>
			<td>End Date</td>
			<td><input type="text" name="endDate" id="endDate" value="" />&nbsp;<img src="/src/LondonFencing/calendar/assets/img/calendar_icon.gif" alt="Select End Date" id="imgCalEndDate" style="cursor:pointer" /></td>
		</tr>
		<tr><td>All Day Event:</td><td><input type="checkbox" name="allDayEvent" id="allDayEvent" value="1" /></td></tr>
		<tr>
		<td>Start Time:</td>
		<td><select name="startHH" id="startHH">
			<?php 
				foreach ($hours as $key => $value){
					echo "<option value=\"$key\">$value</option>";
				}
			?>
			</select>&nbsp;:&nbsp;
			<select name="startMM" id="startMM">
			<?php 
				for ($m = 0; $m < 60; $m+=5){
					$min = ($m < 10)?"0$m":"$m";
					echo "<option value=\"$min\">$min</option>";
				}
			?>
			</select>
			<select name="startTOD" id="startTOD">
				<option value="0">AM</option>
				<option value="12">PM</option>
			</select></td>
		</tr>
		<tr><td>End Time:</td>
		<td><select name="endHH" id="endHH">
			<?php 
				foreach ($hours as $key => $value){
					echo "<option value=\"$key\">$value</option>";
				}
			?>
			</select>&nbsp;:&nbsp;
			<select name="endMM" id="endMM">
			<?php 
				for ($m = 0; $m < 60; $m+=5){
					$min = ($m < 10)?"0$m":"$m";
					echo "<option value=\"$min\">$min</option>";
				}
			?>
			</select>
			<select name="endTOD" id="endTOD">
				<option value="0">AM</option>
				<option value="12">PM</option>
			</select></td>
		</tr>
		</tbody>
		<tbody id="tbRecurrence">
		<tr>
			<td>Recurrence:</td>
			<td><select name="recurrence" id="recurrence">
				<option value="None">None</option>
				<option value="Daily">Daily</option>
				<option value="Weekly">Weekly</option>
				<option value="Monthly">Monthly</option>
				<option value="Yearly">Yearly</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Recurs Every:</td>
			<td><select name="recurrenceInterval" id="recurrenceInterval">
			<?php
				for ($i = 0; $i < 366; $i++){
					echo "<option value=\"$i\">$i</option>";
				}
			?>
			</select>
			</td>
		</tr>
		<tr>
			<td>Recurrence End:</td>
			<td><input type="text" name="recurrenceEnd" id="recurrenceEnd" />&nbsp;<img src="/src/LondonFencing/calendar/assets/img/calendar_icon.gif" alt="Select End Date" id="imgCalRecurEndDate" style="cursor:pointer" /></td>
		</tr>
		</tbody>
		<tbody id="tbDetails">
			<tr id="trDetailsURL"><td>Details Page URL:</td><td>http://<input type="text" name="detailsAlternateURL" id="detailsAlternateURL" /></td></tr>
		</tbody>
		</table>
		<input type="hidden" name="eventSaveType" value="" id="eventSaveType" />
		<input type="hidden" name="eventID" id="" value="eventID" />
		<input type="submit" name="saveEvent" id="saveEvent" value="Save" />&nbsp;<input type="button" name="cancelEditEvent" id="cancelEditEvent" value="Cancel" />
	</form>
</div>
<!-- dialog for editing recurring events -->
<div id="dlgEditRecurring" style="display:none">
<img src="/src/LondonFencing/calendar/assets/img/ajax-loader.gif" alt="Please Wait" style="display:none" id="imgRecurImgLoad" />
<p class="message"></p>
<form method="post" action="" name="frmEditRecurring" id="frmEditRecurring">
<table>
<tr><td colspan="2">
	<fieldset><legend>This is a recurring event. Edit:</legend>
	<input type="radio" name="recurEdit" id="recurEdit_this" value="this" />This instance<br />
	<input type="radio" name="recurEdit" id="recurEdit_all" value="all" />All instances
	</fieldset>
	</td>
</tr>
<tr><td>Start Date:</td><td><input type="text" name="startDate" id="recur_startDate" value="" />&nbsp;<img src="/src/LondonFencing/calendar/assets/img/calendar_icon.gif" alt="Select End Date" id="recur_imgCalStartDate" style="cursor:pointer" /></td></tr>
<tr>
	<td>End Date</td><td><input type="text" name="endDate" id="recur_endDate" value="" />&nbsp;<img src="/src/LondonFencing/calendar/assets/img/calendar_icon.gif" alt="Select End Date" id="recur_imgCalEndDate" style="cursor:pointer" /></td>
</tr>
<tr>
	<td>Start Time:</td>
	<td><select name="startHH" id="startHH">
	<?php 
		foreach ($hours as $key => $value){
			echo '<option value="'.$key.'">'.$value.'</option>';
		}
	?>
	</select>&nbsp;:&nbsp;
	<select name="startMM" id="startMM">
	<?php 
		for ($m = 0; $m < 60; $m+=5){
			$min = ($m < 10)?"0$m":"$m";
			echo '<option value="'.$min.'">'.$min.'</option>';
		}
	?>
	</select>
	<select name="startTOD" id="startTOD">
	<option value="0">AM</option>
	<option value="12">PM</option>
	</select></td>
</tr>
<tr><td>End Time:</td>
	<td><select name="endHH" id="endHH">
	<?php 
		foreach ($hours as $key => $value){
			echo '<option value="'.$key.'">'.$value.'</option>';
		}
	?>
	</select>&nbsp;:&nbsp;
	<select name="endMM" id="endMM">
	<?php 
		for ($m = 0; $m < 60; $m+=5){
			$min = ($m < 10)?"0$m":"$m";
			echo '<option value="'.$min.'">'.$min.'</option>';
		}
	?>
	</select>
	<select name="endTOD" id="endTOD">
	<option value="0">AM</option>
	<option value="12">PM</option>
	</select></td>
</tr>
</table>
<input type="hidden" name="eventSaveType" value="r" id="eventSaveType" />
<input type="hidden" name="eventID" id="" value="eventID" />&nbsp;<input type="hidden" name="recurrenceID" id="" value="recurrenceID" />
<input type="submit" name="saveEvent" id="saveRecurEvent" value="Save" />&nbsp;<input type="button" name="cancelEditRecurEvent" id="cancelEditRecurEvent" value="Cancel" />
</form>
</div>
<!-- dialog for deleting events -->
<div id="dlgDeleteEvent" title="Delete Event" style="display:none">
<img src="/src/LondonFencing/calendar/assets/img/ajax-loader.gif" alt="Please Wait" style="display:none" id="imgEditImgLoad" />
<p class="message"></p>
<form action="" method="post" name="frm_delete_event" id="frm_delete_event">
<table>
<tbody id="tbRecurring" style="display:none">
	<tr><td>
	<fieldset><legend>This is a recurring event. Delete:</legend>
		<input type="radio" name="recurDelete" id="recurDelete_this" value="this" />This instance<br />
		<input type="radio" name="recurDelete" id="recurDelete_all" value="all" />All instances
	</fieldset>
	</td>
	</tr>
</tbody>
</table>
<input type="hidden" name="eventSaveType" value="d" id="eventSaveType" />
<input type="hidden" name="eventID" id="" value="eventID" /><input type="hidden" name="recurrenceID" id="" value="recurrenceID" />
<input type="submit" name="btnDeleteEvent" id="btnDeleteEvent" value="Continue" />&nbsp;<input type="button" name="btnCancelDeleteEvent" id="btnCancelDeleteEvent" value="Cancel" />
</form>
</div>
<!-- view event details -->
<div id="dlgEventDetails" title="title" style="display:none">
<?php $cal->display_dlg_events_details();?>
<input type="button" name="editEvent" id="editEvent" value="Edit" />&nbsp;|&nbsp;<input type="button" name="deleteEvent" id="deleteEvent" value="Delete" />
</div>
<!-- help -->
<div style="display:none">
	<div style="width:400px;height:275px" id="calInfo">
	<p style="height:20px;border:1px solid #75b2e2;background-color:#fcffdc;padding:5px;font-size:14px;font-weight:bold;">
	<img src="/src/LondonFencing/calendar/assets/img/info.png" alt="Help" style="vertical-align:middle;margin-top:0px;padding:0px" />&nbsp;How to Use this Calendar
	</p>
	<ul>
	<li style="padding:10px 10px 10px 0;border-bottom:1px dotted #dddddd">Create a calendar event by clicking on the empty space on any day. Be sure to select the correct calendar from the list</li>
	<li style="padding:10px 10px 10px 0;border-bottom:1px dotted #dddddd">Edit the colours of the calendars by selecting the calendar from the side nav and click "Edit". You can select any colour you want from the selector</li>
	</ul>
	</div>
</div>
<!--end section of dialog boxes -->
<div id="leftColAdmin">
	<p>Current Calendars:</p>
	<?php echo($cal->display_calendar_list_admin()); ?>
	<input type="button" name="btnNewCalendar" id="btnNewCalendar" value="Create New Calendar" /><!-- onclick = open new dialog to add a calendar -->
	<p><a href="#calInfo" id="aCalInfo"><img src="/src/LondonFencing/calendar/assets/img//info.png" alt="Click for Help" /></a></p>
</div>
<div id="centreCol">
	<div id="calendarTitle"><h2 class="fc-header-title"></h2></div>

	<!--calendar div must remain empty for fullCalendar.js to fill-->
	<div id="calendar"></div>
</div>
</div>
<?php
include $root."/admin/templates/footer.php";
?>