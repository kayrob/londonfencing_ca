<?php
namespace LondonFencing\calendar\Apps;
use LondonFencing\calendar as Cal;
/**
* Admin methods for the events calendar. Not to be used without /includes/apps/calendar/Calendar.php
* created: November 11, 2010 by Karen Laansoo
* @package apps/calendar
*/
class adminCalendar extends Cal\calendar{
	/**
	* @var array $recurrenceOpts
	* @access protected
	*/
	protected $recurrenceOpts = array("None","Daily","Weekly","Monthly","Yearly");
	/**
	* Remove recurring events. Single event or all based on if $recurringID is set
	* @access protected
	* @param string $eventID
	* @param string $recurringID
	* @return string|false
	*/
	protected function delete_events_recurring($eventID,$recurringID){
		if (preg_match("/^\d{1,6}$/",$eventID,$matches)){
			$where = sprintf("tblCalendarRecurringEvents.calendarEventID = '%d'",(int)$eventID);
			$where .= ($recurringID != false)?sprintf(" AND tblCalendarRecurringEvents.itemID = '%d'",(int)$recurringID):"";
			$qry = "DELETE FROM tblCalendarRecurringEvents WHERE $where";
			$res = $this->db->query($qry);
			$rowsAffected = $this->db->affected_rows($res);
			if ($recurringID !== false){
				if ($rowsAffected == 1){
					return 'true';
				}
			}
			else{
				if ($rowsAffected >= 0){
					return 'true';
				}
			}
		}
		return false;
	}
	/**
	* Main method to delete an event
	* If event is a child (recurring) of a parent, and is the only one selected, then only it will be deleted
	* Deleting a parent event of recurring events removes all instances
	* @access public
	* @param array $post
	* @see delete_events_recurring()
	* @return string
	*/
	public function delete_events_main($post){
		if (isset($post['eventID']) && preg_match("/^\d{1,6}$/",trim($post['eventID']),$matches)){
			$eventID = trim($post['eventID']);
			$recurringID = false;
			if (isset($post['recurrenceID']) && preg_match("/^\d{1,6}$/",trim($post['recurrenceID']),$matches)){
				if (isset($post['recurDelete']) && trim($post['recurDelete']) == 'this'){
					return $this->delete_events_recurring($eventID,trim($post['recurrenceID']));
				}
				else{
					return $this->delete_events_recurring($eventID,false);
				}
			}
			else if($this->delete_events_recurring($eventID,false) == true){
				$qry = sprintf("DELETE FROM tblCalendarEvents WHERE tblCalendarEvents.itemID = %d",(int)$eventID);
				$res = $this->db->query($qry);
				if ($this->db->affected_rows($res) == 1){
					return 'true';
				}
			}
			else{
				return 'Event could not be deleted';
			}
		}
		else{
			return 'Event was not selected';
		}
	}
	/**
	* Returns the recurrence interval and recurrence end date. Values are auto set if information is not provided from web form
	* @access protected
	* @param string $recurrence
	* @param string $interval
	* @param string $endDate
	* @return array
	*/
	protected function set_recurrence_values($recurrence,$interval,$endDate){
		$recurrenceInterval = 0;
		$recurrenceEnd = "Null";
		if ($recurrence != "None"){
			$recurrenceInterval = (isset($interval) && preg_match("/^[0-9]{1,3}$/",trim($interval),$matches))?intval(trim($interval),10):1;
			$recurrenceEnd = (isset($endDate) && preg_match("/^20[0-9]{2}([-])(0[1-9]|1[012])([-])([012][0-9]|3[01])$/",trim($endDate),$matches))?$this->db->escape($endDate):date("Y")."-12-31";
		}
		return array($recurrenceInterval,$recurrenceEnd);
	}
	/**
	* Set event start and end times based on time of day selected from admin web forms
	* @access protected
	* @param array $post
	* @see DB_MySQL::escape()
	* @return array
	*/
	protected function format_add_edit_dateTime($post){
		$eventStartTime = (isset($post['startTOD']))?(trim($post['startHH'])+trim($post['startTOD'])).":".trim($post['startMM']):trim($post['startHH']).":".trim($post['startMM']);
		$eventEndTime = (isset($post['endTOD']))?(trim($post['endHH'])+trim($post['endTOD'])).":".trim($post['endMM']):trim($post['endHH']).":".trim($post['endMM']);
		if ($eventStartTime == $eventEndTime){
			$eventEndTime = (isset($post['endTOD']))?(trim($post['endHH'])+trim($post['endTOD'])).":".(trim($post['endMM'])+15):trim($post['endHH']).":".(trim($post['endMM'])+15);
		}
		$startDate = trim($post['startDate'])." ".$eventStartTime;
		$endDate = trim($post['endDate'])." ".$eventEndTime;
		if (trim($post['startDate']) == trim($post['endDate']) && $this->create_timestamp($endDate) < $this->create_timestamp($startDate)){
			$eventEndTime = (trim($post['endHH']) < 12)?(trim($post['endHH'])+12).":".trim($post['endMM']):trim($post['startHH']).":".(trim($post['startMM'])+15);
			$endDate = trim($post['endDate'])." ".$eventEndTime;
		}
		return array($startDate,$endDate);
	}
	/**
	* Method to create an associative array of event fields/values
	* Used for both edit event and add new event forms
	* @access protected
	* @param array $post
	* @see format_add_edit_dateTime()
	* @see set_recurrence_values()
	* @return array
	*/
	protected function create_edit_event_values($post){
		$values['calendarID'] = $this->db->escape(trim($post['calendarID']));
		$values['eventTitle'] = $this->db->escape(trim(urldecode($post['eventTitle'])),true);
		list($values['eventStartDate'],$values['eventEndDate']) = $this->format_add_edit_dateTime($post);
		$values['eventStartDate'] = $this->db->escape($values['eventStartDate']);
		$values['eventEndDate'] = $this->db->escape($values['eventEndDate']);
		$values['location'] = $this->db->escape(trim(urldecode($post['location'])),true);
		$values['description'] = $this->db->escape(strip_tags(trim(urldecode($post['description'])),"<a>,<strong>,<em>,<i>,<b>"));
		$values['recurrence'] = "None";
		list($values['recurrenceInterval'],$values['recurrenceEnd']) = array("","");
		if (isset($post['recurrence']) && in_array(trim($post['recurrence']),$this->recurrenceOpts) != false){
			$values['recurrence'] = $this->db->escape(trim($post['recurrence']),true);
		}
		if (isset($post['recurrenceInterval']) && isset($post['recurrenceEnd'])){
			list($values['recurrenceInterval'],$values['recurrenceEnd']) = $this->set_recurrence_values($values['recurrence'],$post['recurrenceInterval'],$post['recurrenceEnd']);
		}
		$values['allDayEvent'] = (isset($post['allDayEvent']) && trim($post['allDayEvent']) == "1")?"1":"0";
		$values['detailPage'] = ((isset($post['detailsAlternateURL'])) && trim($post['detailsAlternateURL']) != "")?"1":"0";
		$values['detailsAlternateURL'] = (isset($post['detailsAlternateURL']))?$this->db->escape(strip_tags(trim(urldecode($post['detailsAlternateURL']))))."":"";
		return $values;
	}
	/**
	* Insert recurring dates. This is called from a loop from add new or edit events
	* @access protected
	* @param string $eventID
	* @param string $recurStart
	* @param string $recurEnd
	* @return integer
	*/
	protected function add_recurring_dates($eventID,$recurStart,$recurEnd){
		$qry = sprintf("INSERT INTO tblCalendarRecurringEvents (calendarEventID,eventStartDate,eventEndDate) values(%d,'%s','%s')",
		(int)$eventID,
		(string)$recurStart,
		(string)$recurEnd
		);
		$res = $this->db->query($qry);
		return $this->db->affected_rows($res);
	}
	/**
	* Create an array of recurring event start and end dates for yearly recurrences
	* @access protected 
	* @param string $eventID
	* @param string $recurrence
	* @param integer $interval
	* @param string $recurEnd
	* @param string $firstEventStart
	* @param string $firstEventEnd
	* @see create_timestamp()
	* @return array|void
	*/
	protected function create_recurring_yearly_dates($eventID,$recurrence,$interval,$recurEnd,$firstEventStart,$firstEventEnd){
		if (preg_match('/^[0-9]{1,6}$/',$eventID,$matches)){
			$recurrenceEndDate = ($recurEnd != "")?$this->create_timestamp(trim($recurEnd)." ".substr(trim($firstEventEnd,"'"),11,5)):mktime(0,0,0,12,31,date('Y'));
			$firstEventStartTime = $this->create_timestamp($firstEventStart);
			$firstEventEndTime = $this->create_timestamp($firstEventEnd);
			if ($recurrenceEndDate > $firstEventEndTime){
				$numYears = date('Y',$recurrenceEndDate) - date('Y',$firstEventEndTime);
				if ($numYears > 0){
					$dayOfWeekEventStart = date('w',$firstEventStartTime);
					$dateDiffStart = floor(date('j',$firstEventStartTime) - 1);
					$weekNumStart = ceil($dateDiffStart/7);
					for ($y = 1; $y <= $numYears; $y++){
						$nextYearStartTime = mktime(0,0,0,date('m',$firstEventStartTime),1,date('Y',$firstEventStartTime)+($y*$interval));
						$newDayOfWeek = 1;
						if (date('w',$nextYearStartTime) > $dayOfWeekEventStart){
							$newDayOfWeekStart = 1 + ((7+$dayOfWeekEventStart) - date('w',$nextYearStartTime));
						}
						else if (date('w',$nextYearStartTime) < $dayOfWeekEventStart){
							$newDayOfWeekStart = (1 + ($dayOfWeekEventStart - date('w',$nextYearStartTime)));
						}
						$newDayOfWeekStart += ($weekNumStart*7 - 7);
						$events[-1+$y]['eventStartDate'] = mktime(0,0,0,date('m',$nextYearStartTime),$newDayOfWeekStart,date('Y',$nextYearStartTime));
						$events[-1+$y]['eventEndDate'] =  $events[-1+$y]['eventStartDate'] + ($firstEventEndTime - $firstEventStartTime);
					}
				}
				if (isset($events)){
					return $events;
				}
			}
		}
		return;
	}
	/**
	* Create an array of recurring event start and end dates for monthly recurrences
	* @access protected 
	* @param string $eventID
	* @param string $recurrence
	* @param integer $interval
	* @param string $recurEnd
	* @param string $firstEventStart
	* @param string $firstEventEnd
	* @see create_timestamp()
	* @return array|void
	*/
	protected function create_recurring_monthly_dates($eventID,$recurrence,$interval,$recurEnd,$firstEventStart,$firstEventEnd){
		if (preg_match('/^[0-9]{1,6}$/',$eventID,$matches)){
			$recurrenceEndDate = ($recurEnd != "")?$this->create_timestamp(trim($recurEnd)." ".substr(trim($firstEventEnd,"'"),11,5)):mktime(0,0,0,12,31,date('Y'));
			$firstEventStartTime = $this->create_timestamp($firstEventStart);
			$firstEventEndTime = $this->create_timestamp($firstEventEnd);
			if ($recurrenceEndDate > $firstEventEndTime){
				$numMonths = ceil(($recurrenceEndDate - $firstEventEndTime)/(60*60*24*7*4));
				$numRecurEvents = $numMonths/$interval;
				if ($numRecurEvents > 0){
					$dayOfWeekEventStart = date('w',$firstEventStartTime);
					$dateDiffStart = floor(date('j',$firstEventStartTime) - 1);
					$weekNumStart = ceil($dateDiffStart/7);
					for ($m = 1; $m <= $numRecurEvents; $m++){
						$nextMonthStart = date('n',$firstEventStartTime) + ($interval*$m);
						$nextMonthStartTime = ($nextMonthStart > 12)?mktime(0,0,0,($nextMonthStart - 12),1,date('Y',$firstEventStartTime)+1):mktime(0,0,0,$nextMonthStart,1,date('Y',$firstEventStartTime));
						$newDayOfWeekStart = 1;
						if (date('w',$nextMonthStartTime) > $dayOfWeekEventStart){
							$newDayOfWeekStart = 1 + ((7+$dayOfWeekEventStart) - date('w',$nextMonthStartTime));
						}
						else if (date('w',$nextMonthStartTime) < $dayOfWeekEventStart){
							$newDayOfWeekStart = (1 + ($dayOfWeekEventStart - date('w',$nextMonthStartTime)));
						}
						$newDayOfWeekStart += ($weekNumStart*7 - 7);
						$events[-1+$m]['eventStartDate'] = mktime(0,0,0,date('m',$nextMonthStartTime),$newDayOfWeekStart,date('Y',$nextMonthStartTime));
						$events[-1+$m]['eventEndDate'] =  $events[-1+$m]['eventStartDate'] + ($firstEventEndTime - $firstEventStartTime);
					}
					if (isset($events)){
						return $events;
					}
				}
			}
		}
		return;
	}
	/**
	* Create an array of recurring event start and end dates for daily and weekly recurrences
	* @access protected 
	* @param string $eventID
	* @param string $recurrence
	* @param integer $interval
	* @param string $recurEnd
	* @param string $firstEventStart
	* @param string $firstEventEnd
	* @see create_timestamp()
	* @return array|void
	*/
	protected function create_recurring_daily_weekly_dates($eventID,$recurrence,$interval,$recurEnd,$firstEventStart,$firstEventEnd){
		if (preg_match('/^[0-9]{1,6}$/',$eventID,$matches)){
			$recurrenceEndDate = ($recurEnd != "")?$this->create_timestamp(trim($recurEnd)." ".substr(trim($firstEventEnd,"'"),11,5)):mktime(0,0,0,12,31,date('Y'));
			$firstEventStartTime = $this->create_timestamp($firstEventStart);
			$firstEventEndTime = $this->create_timestamp($firstEventEnd);
			if ($recurrenceEndDate > $firstEventEndTime){
				$recurrenceFactor = array('Daily'=>1,'Weekly'=>7);
				$oneDay = 60*60*24;
				$intervalFactor = $oneDay * $recurrenceFactor[$recurrence] * $interval;
				$nextInstanceStart = $firstEventStartTime + $intervalFactor;
				$nextInstanceEnd = $firstEventEndTime + $intervalFactor;
				$j = 1;
				while ($nextInstanceEnd <= $recurrenceEndDate){
					$events[-1+$j]['eventStartDate'] = $nextInstanceStart;
					$events[-1+$j]['eventEndDate'] = $nextInstanceEnd;
					$j++;
					$nextInstanceStart += $intervalFactor;
					$nextInstanceEnd += $intervalFactor;
				}
			}
			if (isset($events)){
				return $events;
			}
		}
		return;
	}
	/**
	* Create (insert) recurring events. Method called is based on recurrence type
	* @access protected
	* @param string $eventID
	* @param string $recurrence
	* @param integer $interval
	* @param string $recurEnd
	* @param string $firstEventStart
	* @param string $firstEventEnd
	* @see create_recurring_monthly_dates()
	* @see create_recurring_yearly_dates()
	* @see create_recurring_daily_weekly_dates()
	* @see add_recurring_dates()
	* @return string
	*/
	protected function create_recurring_dates($eventID,$recurrence,$interval,$recurEnd,$firstEventStart,$firstEventEnd){
		if (preg_match('/^[0-9]{1,6}$/',$eventID,$matches)){
			switch($recurrence){
				case "Monthly":
					$events = $this->create_recurring_monthly_dates($eventID,$recurrence,$interval,$recurEnd,$firstEventStart,$firstEventEnd);
					break;
				case "Yearly":
					$events = $this->create_recurring_yearly_dates($eventID,$recurrence,$interval,$recurEnd,$firstEventStart,$firstEventEnd);
					break;
				default:
					$events = $this->create_recurring_daily_weekly_dates($eventID,$recurrence,$interval,$recurEnd,$firstEventStart,$firstEventEnd);
					break;
			}
			if (isset($events)){
				$res = "";
				for ($e = 0; $e < count($events); $e++){
					$res += $this->add_recurring_dates($eventID,date('Y-m-d',$events[$e]['eventStartDate'])." ".substr(trim($firstEventStart,"'"),11,5),date('Y-m-d',$events[$e]['eventEndDate'])." ".substr(trim($firstEventEnd,"'"),11,5));
				}
			}
			if (isset($res) && $res == count($events)){
				return 'true';
			}
			else{
				return "recurring events could not be added";
			}
		}
		return 'recurring events were not added';
	}
	/**
	* Verify that data submitted via add new event, or edit event is valid before saving changes
	* @access protected
	* @param array $post
	* @return true|string
	*/
	protected function verify_edit_add_event_data($post){
		if (isset($post['eventTitle']) && trim($post['eventTitle']) != ""){
				if (isset($post['startDate']) && preg_match("/^20[0-9]{2}([-])(0[1-9]|1[012])([-])([012][0-9]|3[01])$/",trim($post['startDate']),$matches) 
				&& isset($post['endDate']) && preg_match("/^20[0-9]{2}([-])(0[1-9]|1[012])([-])([012][0-9]|3[01])$/",trim($post['endDate']),$matches) && 
				isset($post['startHH']) && preg_match('/^(0[0-9]|1[01])$/',trim($post['startHH']),$matches) && isset($post['startMM']) && preg_match('/^[012345][05]$/',trim($post['startMM']),$matches) 
				&& isset($post['endHH']) && preg_match('/^(0[0-9]|1[01])$/',trim($post['endHH']),$matches) && isset($post['endMM']) && preg_match('/^[012345][05]$/',trim($post['endMM']),$matches)){
					return true;
				}
				return "invalid dates given";
		}
		return "no event title provided";
	}
	/**
	* Main method called via ajax to add new events
	* @access public
	* @param array $post
	* @see verify_edit_add_event_data()
	* @see create_edit_event_values()
	* @see create_recurring_dates()
	* @return string
	*/
	public function add_new_event($post){
		$validData = 'no calendar selected';
		if (isset($post['calendarID']) && preg_match('/^[0-9]{1,6}$/',trim($post['calendarID']),$matches)){
			$validData = $this->verify_edit_add_event_data($post);
			if ($validData == true){
				$insertData = $this->create_edit_event_values($post);
				if (is_array($insertData)){
					$fields = array("sysDateCreated");
					$values = array("NOW()");
					foreach($insertData as $field => $value){
						array_push($fields,$field);
						($field == "calendarID")?array_push($values,intval($value,10)):array_push($values,"'".$value."'");
					}
					$qryFields = implode(",",$fields);
					$qryValues = implode(",",$values);
					$qry = "INSERT INTO tblCalendarEvents ($qryFields) values($qryValues)";
					$res = $this->db->query($qry, $this->db->dblink);
					$newID = $this->db->insert_id($this->db->dblink);
					if ($this->db->affected_rows($res) == 1){
						if (trim($post['recurrence']) != "None"){
							return $this->create_recurring_dates($newID,trim($post['recurrence']),$insertData['recurrenceInterval'],$insertData['recurrenceEnd'],$insertData['eventStartDate'],$insertData['eventEndDate']);
						}
						$validData = 'true';
					}
					else{
						$validData = "event could not be saved"."&nbsp".mysql_error($this->db->dblink);
						;
					}
				}
				else{
					$validData = "event could not be saved"."&nbsp".mysql_error($this->db->dblink);
				}
			}
		}
		return $validData;
	}
	/**
	* If a recurring event is modified and "all instances" selected, then start and end date for each child + parent is retrieved so start date can be updated
	* @access protected
	* @param string $table
	* @param string $itemID
	* @param string $whereField
	* @see DB::result_please
	* @return array|void
	*/
	protected function get_event_date($table,$itemID,$whereField){
		$res = $this->db->result_please($itemID,$table,"$table.eventStartDate,$table.eventEndDate");
		if ($res != false){
			while ($row = $this->db->fetch_assoc($res)){
				list($startDate,$startTime) = explode(" ",trim($row['eventStartDate']));
				list($endDate,$endTime) = explode(" ",trim($row['eventStartDate']));
			}
			return array($startDate,$endDate);
		}
	}
	/**
	* Method creates query to update events including recurring
	* @access protected
	* @param string $eventID
	* @param string $set
	* @param string $table
	* @return integer
	*/
	protected function update_event_main($eventID,$set,$table){
		if (trim($set) != ""){
			$qry = sprintf("UPDATE $table SET ".trim($set)." WHERE $table.itemID = '%d'",(int)$eventID);
			$res = $this->db->query($qry);
			if ($res == 1){
				return 1;
			}
			else{
				return ($this->db->affected_rows($res));
			};
		}
		return 0;
	}
	/**
	* Main method accessed by ajax request to update recurring events
	* Parent events are updated if "all instances" was selected
	* @access public
	* @param array $post
	* @see format_add_edit_dateTime()
	* @see update_event_main()
	* @see get_event_date()
	* @return string
	*/
	public function update_recurring_events($post){
		$validData = 'events could not be updated';
		if (isset($post['eventID']) && preg_match("/^\d{1,6}$/",trim($post['eventID']),$matches) && isset($post['recurrenceID']) && preg_match('/^[0-9]{1,6}$/',trim($post['recurrenceID']),$match)){
			if (!isset($post['recurEdit']) || trim($post['recurEdit']) == 'this'){
				list($eventStart,$eventEnd) = $this->format_add_edit_dateTime($post);
				$set = sprintf("eventStartDate = '%s', eventEndDate = '%s'",(string)$eventStart,(string)$eventEnd);
				if ($this->update_event_main(trim($post['recurrenceID']),$set,'tblCalendarRecurringEvents') == 1){
					$validData = 'true';
				}
			}
			else{
				list($post['startDate'],$post['endDate']) = $this->get_event_date("tblCalendarEvents",trim($post['eventID']),"itemID");
				list($eventStartMain,$eventEndMain) = $this->format_add_edit_dateTime($post);
				$setMain = "tblCalendarEvents.eventStartDate = $eventStartMain, tblCalendarEvents.eventEndDate = $eventEndMain";
				if ($this->update_event_main(trim($post['eventID']),$setMain,'tblCalendarEvents') == 1){
					$qry = "select * from tblCalendarRecurringEvents where calendarEventID=".trim($post['eventID']);	
					$res = $this->db->result_please(false,'tblCalendarRecurringEvents',false,sprintf("calendarEventID= '%d'",(int)trim($post['eventID'])));
					if ($res !== false){
						while ($row = $this->db->fetch_assoc($res)){
							list($post['startDate'],$startTime) = explode(" ",trim($row['eventStartDate']));
							list($post['endDate'],$endTime) = explode(" ",trim($row['eventStartDate']));
							list($recurStart,$recurEnd) = $this->format_add_edit_dateTime($post);
							$setRecur[trim($row['itemID'])] = "eventStartDate = $recurStart, eventEndDate = $recurEnd";
						}
						if (isset($setRecur)){
							$recurUpdate = 0;
							foreach ($setRecur as $recurID => $recurSet){
								$recurUpdate += $this->update_event_main($recurID,$recurSet,"tblCalendarRecurringEvents");
							}
							if ($recurUpdate == count($setRecur)){
								$validData = 'true';
							}
						}
					}
				}
			}
		}
		else{ $validData = 'no event or calendar selected';}
		return $validData;
	}
	/**
	* Main method to update an event. Recurring (child) events will be deleted and reset if recurrence is set
	* @access public
	* @param array $post
	* @see verify_edit_add_event_data()
	* @see create_edit_event_values()
	* @see update_event_main()
	* @see delete_events_recurring()
	* @see create_recurring_dates()
	* @return string
	*/
	public function update_event($post){
		$validData = 'event could not be updated';
		if (isset($post['eventID']) && preg_match("/^\d{1,6}$/",trim($post['eventID']),$matches) && isset($post['calendarID']) && preg_match('/^[0-9]{1,6}$/',trim($post['calendarID']),$match)){
			$validData = $this->verify_edit_add_event_data($post);
			if ($validData == true){
				$eventID = trim($post['eventID']);
				$dataSet = $this->create_edit_event_values($post);
				if (isset($dataSet) && is_array($dataSet)){
					$set = "";
					foreach($dataSet as $field => $value){
						$set .= ($field != "calendarID")?sprintf("tblCalendarEvents.$field = '%s'",(string)$value):sprintf("tblCalendarEvents.$field = %d",(int)$value);
						$set .= ",";
					}
					if ($this->update_event_main($eventID,rtrim($set,","),'tblCalendarEvents') == 1){
						if ($this->delete_events_recurring($eventID,false) == true){
							if (trim($post['recurrence']) != "None"){
								return $this->create_recurring_dates($eventID,trim($post['recurrence']),$dataSet['recurrenceInterval'],$dataSet['recurrenceEnd'],$dataSet['eventStartDate'],$dataSet['eventEndDate']);
							}
						}
					}
					$validData = 'true';
				}
				else{ $validData = 'data missing or incomplete'.mysql_error($this->db->dblink);}
			}
			else{ $validData = 'data missing or incomplete'.mysql_error($this->db->dblink);}
		}
		else{ $validData = 'no event or calendar selected'.mysql_error($this->db->dblink);}
		return $validData;
	}
	/**
	* Create query to update main calendar details
	* @access protected
	* @param array $post
	* @see DB_MySQL::escape()
	* @return integer
	*/
	protected function update_calendar_details($post){
		if (isset($post['itemID']) && preg_match('/^[0-9]{1,6}$/',trim($post['itemID']),$matches)){
			$set = '';
			foreach ($post as $field => $value){
				if ($field != 'itemID'){
					$set .= sprintf("$field = '%s',",(string)$this->db->escape(strip_tags(trim(urldecode($value)))));
				}
			}
			if ($set != ''){
				$qry = sprintf("UPDATE tblCalendars SET ".rtrim($set,',')." WHERE itemID = '%d'",(int)trim($post['itemID']));
                                echo $qry;
				$res = $this->db->query($qry, $this->db->dblink);
				if ($this->db->affected_rows($res) == 1){
					return 1;
				}
			}
		}
		return 0;
	}
	/**
	* Main method to update calendar details from ajax call
	* @access public
	* @param array $post
	* @see update_calendar_details()
	* @return string
	*/
	public function update_calendar($post){
		if (isset($post['itemID']) && preg_match('/^[0-9]{1,6}$/',trim($post['itemID']),$matches)){
			if (isset($post['calendarName']) && trim($post['calendarName']) != '' && preg_match('/^#[0-9a-zA-Z]{3,6}$/',urldecode(trim($post['eventBackgroundColor'])),$matches)){
				if ($this->update_calendar_details($post) == 1){
					return 'true';
				}
				else{
					return 'changes could not be saved';
				}
			}
			else{
				return 'you did not provide a calendar name';
			}
		}
		else{
			return 'no calendar selected';
		}
	}
	/**
	* Main method to disable a calendar via ajax call
	* Calendars are not deleted so they can be brought back if needed
	* @access public
	* @param array $post
	* @see update_calendar_details()
	* @return string
	*/
	public function disable_calendar($post){
		if (isset($post['itemID']) && preg_match('/^[0-9]{1,6}$/',trim($post['itemID']),$matches)){
			$post['sysStatus'] = 'inactive';
			$post['sysOpen'] = '0';
			if ($this->update_calendar_details($post) == 1){
				return 'true';
			}
			else{
				return 'changes could not be saved.';
			}
		}
		else{
			return 'no calendar selected';
		}
	}
	/**
	* Method to add a new calendar via ajax
	* @access public
	* @param array $post
	* @see DB_MySQL::escape()
	*/
	public function add_new_calendar($post){
		if (isset($post['calendarName']) && trim($post['calendarName'] != "")){
			$calendarName = $this->db->escape(strip_tags(trim($post['calendarName'])));
			$eventBackground = (preg_match('/^#[0-9a-zA-Z]{3,6}$/',urldecode(trim($post['eventBackgroundColor'])),$matches))?urldecode(trim($post['eventBackgroundColor'])):"#cccccc";
			$qry = sprintf("INSERT INTO tblCalendars (calendarName,eventBackgroundColor,sysStatus,sysOpen,sysDateCreated) values ('%s','%s','active','1',NOW())",
				(string)$calendarName,
				(string)$eventBackground
			);
			$res = $this->db->query($qry, $this->db->dblink);
			$newID = $this->db->insert_id($this->db->dblink);
			if ($this->db->affected_rows($res) == 1){
				return $newID;
			}
			else{
				return "data could not be saved";
			}			
		}
		else{
			return "You must submit a name for your calendar";
		}
	}
	/**
	*Method accessed by php and ajax to display list of available calendars in admin view
	*List is reloaded when a new calendar is added
	*@access public
	*@see get_calendars_common()
	*@return string
	*/
	public function display_calendar_list_admin(){
		$res = $this->get_calendars_common();
		$calsList = '<p id="calsList">There are no calendars</p>';
		if ($res != false){
			$calsList = '<ul id="calsList">';
			while ($row = $this->db->fetch_assoc($res)){
				$checked = (trim($row['sysStatus']) == 'active' && trim($row['sysOpen']) == 1)?'checked="checked"':'';
				$calsList .= '<li style="color:'.trim($row['eventBackgroundColor']).';font-weight:bold">
				<div class="calendarsListItem"><input type="checkbox" style="margin-right:5px;" id="calendarInfo'.trim($row['itemID']).'" name="calendarInfo[]" '.$checked.' value="'.trim($row['itemID']).'" /><span id="cal'.trim($row['itemID']).'">'.trim($row['calendarName']).
				'</span></div></li>'; //add onclick event
			}
			$calsList .= '</ul>';
		}
		return $calsList;
	}
	/**
	* Method to display calendars available in add/edit event form
	* @access public
	* @see get_calendars_common()
	* @return string
	*/
	public function display_calendar_list_events_form(){
		$res = $this->get_calendars_common();
		$calsList = "";
		if ($res != false){
			while($row = $this->db->fetch_assoc($res)){
				$calsList .= '<option value="'.trim($row['itemID']).'" style="color:'.trim($row['eventBackgroundColor']).'">'.trim($row['calendarName']).'</option>';
			}
		}
		return $calsList;
	}
	//no constructor: pass the db object directly to the parent class
}
?>