<?php
namespace LondonFencing\calendar;
require_once __DIR__ .'/Widgets/calendarWidgets.php';
require_once __DIR__ .'/Apps/adminCalendar.php';
/**
* Calendar class retrieves data for public viewing of events and passes it back to jQuery Full Calendar
* Date created: Nov 10 2010 by Karen Laansoo
* @package apps/calendar
*/
class calendar{
	protected $db;
	public $url;
	/**
	* Common function for getting calenadar data for view and ajax calls
	* basic check to see if and how many calendars exist
	* @access protected
	* @see DB::result_please()
	* @return resource|false
	*/
	protected function get_calendars_common($id = false){
		if (is_object($this->db)){
                                            return $this->db->result_please($id,'tblCalendars',false,false,false,false);
		}
		return false;
	}
	/**
	* Display a checkbox list of calendars on event/calendar page
	* these inputs are tied to jquery fullCalendar events feeds
	* if a specific calendar is selected, only check the one selected
	* @access public
	* @param string|false $id
	* @see get_calendars_common()
	*/
	public function get_calendars_view($id = false){
		$res = $this->get_calendars_common();
		if ($res !== false){
			echo('<form name="frm_calendar_list" id="frm_calendar_list" action="" method="post" onsubmit="return false">');
			while ($row = $this->db->fetch_assoc($res)){
				$checked = ($id == false || $id == trim($row['itemID']))?'checked="checked"':'';
				echo('<div class="calendarsListItem"><input type="checkbox" name="calendar['.trim($row['itemID']).']" id="calendar'.trim($row['itemID']).'" value="'.trim($row['itemID']).'" '.$checked.' />
				&nbsp;<span class="calendarName" style="color:'.trim($row['eventBackgroundColor']).'">'.substr(trim($row['calendarName']),0,13).'</span><span class="spICS"><a href="../../../../rss/icalEvents.php?calendar='.trim($row['itemID']).'">ICS</a></span></div>');
			}
			echo('</form>');
		}
		//else do nothing b/c no calendars	
	}
	/**
	* This method retrieves calendars available for the fullCalendar events feed. URL will have itemID appended to the params list
	* @access public
	* @see get_calendars_common()
	* @return object|false
	*/
	public function get_calendars_ajax(){
		$res = $this->get_calendars_common();
		if ($res !== false){
			while($row = $this->db->fetch_assoc($res)){
				$calendars['cal'.trim($row['itemID'])] = trim($row['itemID']);
			}
		}
		if (isset($calendars)){
			return json_encode($calendars);
		}
		return 'false';
	}
	/**
	* Display event details source for dialog boxes
	* For both admin and public views
	* @access public
	*/
	public function display_dlg_events_details($public=false,$allDay="",$start="",$end="",$location="",$description="",$recurrence="",$eventID=""){
		$recurStyle = ($recurrence != "")?"":'style="display:none"';
echo <<<DLGTBL
<table id="tblEventDetails">
	<tr id="tr_allDay" style="display:none"><td colspan="2">$allDay</td></tr>
	<tr><td><strong>From:</strong></td><td id="tdDateStart">$start</td></tr>
	<tr><td><strong>To:</strong></td><td id="tdDateEnd">$end</td></tr>
	<tr><td><strong>Location:</strong></td><td id="tdLocation">$location</td></tr>
	<tr><td><strong>Description:</strong></td><td id="tdDescription">$description</td></tr>
	<tr id="tr_recurring" $recurStyle><td><strong>Event Occurs:</strong></td><td>$recurrence</td></tr>
</table>
DLGTBL;
		if ($public == true){
			$addEventParam = (preg_match("/^([0-9]{1,6})(_)?([0-9]{1,6})?$/",$eventID,$matches))?$matches[1]:"";
			echo("<p id=\"pAddEvent\"><a href=\"../../../../rss/icalEvents.php?event=$addEventParam\">Add to My Calendar</a></p>");
		}
	}
	/**
	* Build CSS to be displayed in body.
	* This is also called via ajax when calendar main is updated in admin so event colours get updated automatically on calendar
	* @access public
	* @see get_calendars_common()
	*/
	public function buildCalendarCSS(){
		$res = $this->get_calendars_common();
		if ($res !== false){
			while ($row = $this->db->fetch_assoc($res)){
				echo("
				.cal_".trim($row['itemID']).",
				.fc-agenda .cal_".trim($row['itemID'])." .fc-event-time,
				.cal_".trim($row['itemID'])." a{
					color: ".trim($row['eventBackgroundColor']).";
					background-color: transparent;
					border:0px;
				}
				.allDay_".trim($row['itemID']).",
				.fc-agenda .allDay_".trim($row['itemID'])." .fc-event-time,
				.allDay_".trim($row['itemID'])." a{
					color: #ffffff;
					background-color: ".trim($row['eventBackgroundColor']).";
					border-color: ".trim($row['eventBackgroundColor']).";
				}
				.recur_".trim($row['itemID']).",
				.fc-agenda .recur_".trim($row['itemID'])." .fc-event-time,
				.recur_".trim($row['itemID'])." a{
					color: ".trim($row['eventBackgroundColor']).";
					border: 0px;
					background-color: transparent;
					background-image: url(/src/LondonFencing/calendar/assets/img/recurringEventIcon.png);
					background-repeat: no-repeat;
					background-position: right;				
				}
				.recur_allDay_".trim($row['itemID']).",
				.fc-agenda .recur_allDay_".trim($row['itemID'])." .fc-event-time,
				.recur_allDay_".trim($row['itemID'])." a{
					color: #ffffff;
					border-color: ".trim($row['eventBackgroundColor']).";
					background-color: ".trim($row['eventBackgroundColor']).";
					background-image: url(/src/LondonFencing/calendar/assets/img/recurringEventIconAllDay.png);
					background-repeat: no-repeat;
					background-position: right;
				}
				");
			}
		}
	}
	/**
	* Create a timestamp for initial event start and initial event end date for generating start and stop dates for child (recurring) events and widgets
	* @access protected
	* @param string $dateTime
	* @return string|false
	*/
	protected function create_timestamp($dateTime){
		list($date,$time) = explode(" ",$dateTime);
		if (isset($date) && isset($time)){
			$date = trim($date,"'");
			return mktime(intval(substr($time,0,2),10),intval(substr($time,3,2),10),0,intval(substr($date,5,2),10),intval(substr($date,8,2),10),intval(substr($date,0,4),10));
		}
		return false;
	}
	/**
	* Create an array of events based on results retrieved from database to be returned to calendar as json encoded
	* This is used for main (parent) events and recurring (child) events
	* @access protected
	* @param resource $res
	* @param int $calID
	* @param true|false $recurring
	* @return array
	*/
                protected function set_event_data($res,$calID,$recurring = false){
                                    $events = array();
		if ($res !== false){
			
			while ($row = $row = $this->db->fetch_assoc($res)){
				$eventsArray['id'] = trim($row['itemID']);
				$eventsArray['title'] = trim($row['eventTitle']);
				$eventsArray['allDay'] = (trim($row['allDayEvent']) == '0')?false:true;
				$eventsArray['start'] = trim($row['eventStartDate']);
				$eventsArray['end'] = trim($row['eventEndDate']);
				$eventsArray['className'] = (trim($row['allDayEvent']) == '0')?"cal_$calID":"allDay_$calID";
				$eventsArray['description'] = trim($row['description']);
				$eventsArray['location'] = trim($row['location']);
				$eventsArray['altUrl'] = (trim($row['detailPage']) == 1)?trim($row['detailsAlternateURL']):"";
				$eventsArray['recurrence'] = 'None';
				if (trim($row['recurrence']) != 'None'){
					$eventsArray['recurrence'] = trim($row['recurrence']);
					$eventsArray['className'] = (trim($row['allDayEvent']) == '0')?"recur_$calID":"recur_allDay_$calID";
					$recurrenceDescription = (trim($row['recurrenceInterval']) == 1 || trim($row['recurrenceInterval']) == 0)?trim($row['recurrence']):"Every ".trim($row['recurrenceInterval'])." ".str_replace("ly","s",trim($row['recurrence']));
					$eventsArray['recurrenceDescription'] = $recurrenceDescription;
					$eventsArray['recurrenceEnd'] = substr(trim($row['recurrenceEnd']),0,10);
					if ($recurring !== false){$eventsArray['recurrenceID'] = trim($row['recurrenceID']);}
				}
				$events[] = $eventsArray;
			}
		}
		return $events;
	}
	/**
	* Retrieve repeating events based on parent record ID (eventID). 
	* Each event returns all of the same information as parent event
	* @access protected
	* @param array $get
	* @see DB::result_please()
	* @see set_event_data()
	* @return array
	*/
	protected function get_repeating_events($get){
		if (preg_match("/^[0-9]{1,6}$/",intVal($get['calendar'],10),$matches)){
			$select = "re.itemID as recurrenceID,re.eventStartDate,re.eventEndDate,ce.itemID,ce.eventTitle,ce.location,ce.description,ce.recurrence,ce.recurrenceInterval,
			ce.allDayEvent,ce.detailPage,ce.detailsAlternateURL,ce.recurrenceEnd";
			
			$from = "tblCalendarRecurringEvents as re INNER JOIN tblCalendarEvents as ce ON (re.calendarEventID = ce.itemID)";
			
			$where = sprintf("ce.calendarID = '%d' 
			AND UNIX_TIMESTAMP(re.eventStartDate) >= '%s' 
			AND UNIX_TIMESTAMP(re.eventEndDate) <= '%s'",
			(int)$get['calendar'],(string)$this->db->escape($get['start'],true),(string)$this->db->escape($get['end']));
			$res = $this->db->result_please(false,$from,$select,$where,false,false);
			if ($res !== false){
				$events = $this->set_event_data($res,intVal($get['calendar'],10),true);
			}
		}
		if (isset($events)){
			return $events;
		}
		return array();
	}
	/**
	* Retrieve repeating events based on parent record ID (eventID)
	* @access protected
	* @param array $get
	* @see DB::result_please()
	* @see set_event_data()
	* @return array
	*/
	protected function get_calendar_events_details($get){
		$where = sprintf("calendarID ='%d' AND UNIX_TIMESTAMP(eventStartDate) >= '%s' AND UNIX_TIMESTAMP(eventEndDate) <= '%s'",
		(int)$get['calendar'],(string)$this->db->escape($get['start'],true),(string)$this->db->escape(trim($get['end']),true));
		
		$res = $this->db->result_please(false,'tblCalendarEvents',false,$where,false,false);
		$events = array();
		if ($res !== false){
			$events = $this->set_event_data($res,intVal($get['calendar'],10));
		}
		$recurEvents = $this->get_repeating_events($get);
		if (isset($events) && isset($recurEvents)){
			$events = array_merge($events,$recurEvents);
			return json_encode($events);
		}
		else{
			return 'false';
		}
	}
	/**
	* Retrieves events based on start time and end time parameters
	* Return json encoded string
	* @access public
	* @param array $get
	* @see get_calendars_common()
	* @see get_calendar_events_details()
	* @return false|string
	*/
	public function get_calendar_events_ajax($get){
		if (isset($get['calendar']) && isset($get['start']) && isset($get['end'])){
			$res = $this->get_calendars_common($get['calendar']);
			if ($res !== false){
				return $this->get_calendar_events_details($get);
			}
		}
		return 'false';
	}
	/**
	* Return the calendar name and background colour for each active calendar
	* @access public
	* @see get_calendars_common()
	* @return void|array
	*/
	public function get_calendar_details(){
		$res = $this->get_calendars_common();
		if ($res !== false){
			while ($row = $this->db->fetch_assoc($res)){
				$calendar[trim($row['itemID'])]['name'] = trim($row['calendarName']);
				$calendar[trim($row['itemID'])]['colour'] = trim($row['eventBackgroundColor']);
			}
			return $calendar;
		}
		return;
	}
	/**
	* Retrieve event details based on event ID sent from a widget on a page different from the main calendar
	* @access public
	* @param array $get
	* @see DB::result_please()
	* @see set_event_data()
	* @return void|array
	*/
	public function get_event_details_byID($get){
		if (isset($get["event"]) && preg_match("/^([0-9]{1,6})(_)?([0-9]{1,6})?$/",$get["event"],$matches)){
			$res = $this->db->result_please($matches[1],"tblCalendarEvents");
			if ($res !== false){
				$events = $this->set_event_data($res,"");
			}
			if (isset($matches[3]) && isset($events) && count($events) > 0){
				$resRecur = $this->db->result_please($matches[3],"tblCalendarRecurringEvents");
				if ($resRecur != false){
					while($row = $this->db->fetch_assoc($resRecur)){
						$events[0]['start'] = trim($row['eventStartDate']);
						$events[0]['end'] = trim($row['eventEndDate']);
					}
				}
			}
			if (isset($events) && count($events) > 0){
				list($startDate,$startTime) = explode(" ",$events[0]['start']);
				list($endDate,$endTime) = explode(" ",$events[0]['end']);
				$startHour = ltrim(substr($startTime,0,2),"0").":".substr($startTime,3,2)." am";
				$endHour = ltrim(substr($endTime,0,2),"0").":".substr($endTime,3,2)." am";
				if (ltrim(substr($startTime,0,2),"0") > 12){
					$startHour = (substr($startTime,0,2)-12).":".substr($startTime,3,2)." pm";
				}
				if (ltrim(substr($endTime,0,2),"0") > 12){
					$endHour = (substr($endTime,0,2)-12).":".substr($endTime,3,2)." pm";
				}
				$events[0]['start'] = substr($events[0]['start'],5,2)."/".ltrim(substr($events[0]['start'],8,2),"0")."/".substr($events[0]['start'],0,4)." $startHour";
				$events[0]['end'] = substr($events[0]['end'],5,2)."/".ltrim(substr($events[0]['end'],8,2),"0")."/".substr($events[0]['end'],0,4)." $endHour";
				$events[0]['allDay'] = ($events[0]['allDay'] == true)?"<em>This is an all day event</em>":"";
				$events[0]['recurrenceDescription'] = (isset($events[0]['recurrenceDescription']))?"This event occurs ".$events[0]['recurrenceDescription']:"";
				return array($events[0]['allDay'],$events[0]['start'],$events[0]['end'],$events[0]['location'],$events[0]['description'],$events[0]['recurrenceDescription'],$events[0]['title']);
			}
		}
		return;
	}
	/**
	* constructor explicitly called to use this keyword and set db object
	*/
	public function __construct($db){
		if (isset($db) && is_object($db)){
			$this->db = $db;
		}
	}
}
?>