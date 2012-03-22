<?php
namespace LondonFencing\calendar\Widgets;
use LondonFencing\calendar as Cal;
/**
 * calendarWidgets class displays widgets for public viewing of events
 * Date created: Nov 29 2010 by Karen Laansoo
 *
 * @package apps/calendar
 */
class calendarWidgets extends Cal\calendar {
	/**
	* Retrieves all events from events and recurring events table by UNION. Start and End date are pre-set if not provided
	* winery ID is app specific
	* @access protected
	* @param string $startDate
	* @param string $endDate
	* @return resource
	*/	
	protected function retrieve_all_events($startDate = false, $endDate = false)
	{
		$whereDate = ($startDate == false)?date("Y")."-".date("m")."-01":$startDate;
		$whereEndOp = ($endDate == false)?">=":"<";
		$whereEndStr = ($endDate == false)?$whereDate:$endDate;
		
		$qryCal = sprintf("(SELECT ce.calendarID,ce.itemID,ce.location,ce.description,ce.eventStartDate,ce.eventEndDate,ce.detailPage,ce.detailsAlternateURL,ce.eventTitle,ce.sysDateCreated
FROM tblCalendarEvents as ce 
WHERE ce.eventStartDate >= '%s' AND ce.eventEndDate " .$whereEndOp. "'%s')",(string)$whereDate,(string)$whereEndStr);

		$qryRecur = sprintf("(SELECT ce.calendarID,concat(ce.itemID,'_',re.itemID) as itemID,ce.location,ce.description,re.eventStartDate,re.eventEndDate,ce.detailPage,ce.detailsAlternateURL,ce.eventTitle,ce.sysDateCreated
FROM tblCalendarEvents as ce INNER JOIN tblCalendarRecurringEvents as re ON (ce.itemID = re.calendarEventID)
WHERE ce.itemID = re.calendarEventID AND re.eventStartDate >= '%s' AND re.eventEndDate " .$whereEndOp. "'%s')",(string)$whereDate,(string)$whereEndStr);

		$qry = $qryCal ." UNION " . $qryRecur . " ORDER BY eventStartDate ASC, eventStartDate ASC";
		return $this->db->query($qry);
	}
	/**
	 * Display a list of upcoming events based on the number of events requested
	 * @access public
	 * @param integer $limit
	 * @param string  $calendarURL
	 * @see get_calendar_details()
	 * @see DB_MySQL::valid()
	 */
	public function upcoming_events_widget($limit){
		if (intval($limit, 10) > 0) {
			$res = $this->retrieve_all_events();
			$calendars = $this->get_calendar_details();
			if (false !== $this->db->valid($res) && isset($calendars))  {
				$j = 0;				
				while ($row = $this->db->fetch_assoc($res)) {
					list($startDate, $startTime) = explode(" ", trim($row['eventStartDate']));
					list($endDate, $endTime) = explode(" ", trim($row['eventEndDate']));
					$startTimeStamp = $this->create_timestamp(trim($row['eventStartDate']));
					if (array_key_exists(substr(trim($row['calendarID']), 0, 2), $calendars) && $startTimeStamp > date('U')) {
						if ($j < $limit) {
							$link = (trim($row['detailPage']) == 1 && trim($row['detailsAlternateURL']) != "")?"http://".trim($row['detailsAlternateURL']): $this->url . "?event=".trim($row['itemID'])."&start=".$startTimeStamp;
							$events[] = array(
								"id" => trim($row["itemID"]),
								"start" => $startTimeStamp,
								"end" => $this->create_timestamp(trim($row['eventEndDate'])),
								"link" => $link,
								"title" => trim($row["eventTitle"])
							);
							$j++;
						}
					}
				}
				if (isset($events)){
					return $events;
				}
			}
		}
		return false;
	}	
	/**
	 * Output RSS feed based on parameters sent in query string. Default is to display all
	 * Only events that are occuring greater than 30 days in advance are currently displayed
	 *
	 * @access public
	 * @param false|string $calendarID
	 * @param integer $limit
	 * @see get_calendar_details()
	 * @see retrieve_all_events()
	 */
	public function output_rss($calendarID=false, $limit=100)
	{
		$calendars = $this->get_calendar_details();
		$description = (preg_match("/^[0-9]{1,6}$/", $calendarID, $matches) && array_key_exists($calendarID, $calendars))?$calendars[$calendarID]['name']:"All Events";
		$channel = array("title"        => "RSS Feed for ".basename($_SERVER['SERVER_NAME']),
			"description"  => "RSS Feed for $description",
			"link"         => "http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'],
			"copyright"    => "Copyright (C) ".date("Y")." ".basename($_SERVER['SERVER_NAME']));
		if (is_array($matches) || $calendarID == false) {
			$res = $this->retrieve_all_events();
			if (false !== $this->db->valid($res)) {
				$items = array();
				while ($row = $this->db->fetch_assoc($res)) {
					$startTimeStamp = $this->create_timestamp(trim($row['eventStartDate']));
					$pubTimeStamp = $this->create_timestamp(trim($row['eventStartDate']));
					if (array_key_exists(trim($row['calendarID']), $calendars) && $startTimeStamp >= (date('U')+60*60*24*30)) {
						$link = "http://".$_SERVER['SERVER_NAME']. $this->url ."?event=".trim($row['itemID'])."&start=".$startTimeStamp;
						$source = false;
						if (trim($row['detailPage']) == 1 && trim($row['detailsAlternateURL']) != ""){
							$link = "http://".trim($row['detailsAlternateURL']);
						}
						if ($calendar == false){
							$source = "http://".$_SERVER['SERVER_NAME']."/rss/".str_replace(" ", "_", str_replace("'", "", $calendars[trim($row['calendarID'])]['name']))."php";
						}
						array_push($items, array("title"=>trim($row["eventTitle"]),
								"description"=>trim($row["description"]),
								"link"=>$link,
								"pubDate"=>date("D, d M Y H:i:s O", $pubTimeStamp),
								"calendar"=>substr(trim($row['calendarID']), 0, 2),
								"source"=>$source,
							));
					}
				}
				if (count($items) > 0) {
					$j = 0;
					$output = '<?xml version="1.0" encoding="ISO-8859-1"?>';
					$output .= '<rss version="2.0">';
					$output .= "<channel>";
					$output .= "<title>".$channel["title"]."</title>";
					$output .= "<description>".$channel["description"]."</description>";
					$output .= "<link>" . $channel["link"] . "</link>";
					$output .= "<copyright>".$channel["copyright"]."</copyright>";
					foreach ($items as $item) {
						if ($item['calendar'] == $calendarID || $calendarID == false) {
							if ($j < $limit) {
								$output .= "<item>";
								$output .= "<title>".$item["title"]."</title>";
								$output .= "<description>".$item["description"]."</description>";
								$output .= "<link>".htmlspecialchars($item["link"])."</link>";
								$output .= "<pubDate>".$item["pubDate"]."</pubDate>";
								$output .= "</item>";
								$j++;
							}
						}

					}
					$output .= "</channel>";
					$output .= "</rss>";
					echo $output;
				}
			}
		}
	}
	/**
	 * Output calendar events in iCalendar (.ics) format
	 * Calendar, and start/stop dates can be submitted as parameters via a query string (like ajax)
	 *
	 * @access public
	 * @param array   $get
	 * @get_calendar_details()
	 * @create_timestamp()
	 */
	public function output_ics($get)
	{
		$calendars = $this->get_calendar_details();
		$endDate = false;
		$dayOfWeek = array("SU", "MO", "TU", "WE", "TH", "FR", "SA");
		if (!isset($get['calendar']) || array_key_exists(trim($get['calendar']), $calendars)) {
			$startDate = (isset($get['start']) && preg_match("/^[0-9]{8,}$/", trim($get['start']), $matches))?date("Y-m-d", trim($get['start'])):date("Y-m-d", date('U'));
			$endDate = (isset($get['end']) && preg_match("/^[0-9]{8,}$/", trim($get['end']), $matches))?date("Y-m-d", trim($get['end'])):false;
			$where = "eventStartDate >= '$startDate'";
			if ($endDate != false) {
				$where .= " and eventEndDate <= '$endDate'";
			}
			if (isset($get['calendar']) && array_key_exists(trim($get['calendar']), $calendars)) {
				$where .= " and calendarID = ".intval($get['calendar'], 10);
			}
			if (isset($get['event']) && preg_match("/^[0-9]$/",trim($get['event']),$matches)){
				$where = " itemID = ".intval($get['event'],10);
			}
			$res = $this->db->result_please(false, 'tblCalendarEvents', false, $where, false, false);
			if (false !== $this->db->valid($res)) {
				$output = "BEGIN:VCALENDAR\nVERSION:2.0\n";
				while ($row = $this->db->fetch_assoc($res)) {
					if (array_key_exists(trim($row['calendarID']), $calendars)) {
						$startTimeStamp = $this->create_timestamp(trim($row['eventStartDate']));
						$endTimeStamp = $this->create_timestamp(trim($row['eventEndDate']));
						$consecDays = ceil(($endTimeStamp-$startTimeStamp)/(60*60*24));
						$endTimeStamp += ($consecDays > 1)?60*60*24:0;
						$eventStart = "TZID=".date("e").":".str_replace(" ", "T", preg_replace("/[:-]/", "", trim($row['eventStartDate'])));
						$eventEnd = "TZID=".date("e").":".str_replace(" ", "T", preg_replace("/[:-]/", "", trim($row['eventEndDate'])));
						$eventStart = ($consecDays > 1)?"VALUE=DATE:".str_replace("-", "", substr(trim($row['eventStartDate']), 0, 10)):$eventStart;
						$eventEnd = ($consecDays > 1)?"VALUE=DATE:".str_replace("-", "", date("Ymd", $endTimeStamp)):$eventEnd;
						$description = ($consecDays > 1)?"(".date("g:i a", $startTimeStamp)."-".date("g:i a", $endTimeStamp).")":"";
						$output .= "BEGIN:VEVENT\n";
						$output .= "UID:uid".trim($row['itemID'])."@".$_SERVER['SERVER_NAME']."\n";
						$output .= "DTSTAMP;TZID=".date("e").":".str_replace(" ", "T", preg_replace("/[:-]/", "", trim($row['sysDateCreated'])))."\n";
						$output .= "DTSTART;$eventStart\n";
						$output .= "DTEND;$eventEnd\n";
						$output .= "SUMMARY: London Fencing Club ".trim($row['eventTitle'])."\n";
						$output .= "LOCATION:".stripslashes(trim($row['location']))."\n";
						$output .= "DESCRIPTION:".stripslashes(trim($row['description']))." $description\n";
						$output .= "PRIORITY:3\n";
						if ($consecDays > 1) {
							for ($i = 1; $i < $consecDays; $i++) {
								$consecDayOfWeek[] = $dayOfWeek[date('w', $startTimeStamp)+$i];
							}
						}
						if (trim($row['recurrence']) != 'None') {
							$recurrenceTimeStamp = $this->create_timestamp(trim($row['recurrenceEnd']));
							$recurrenceEnd = str_replace(" ", "T", preg_replace("/[:-]/", "", substr(trim($row['recurrenceEnd']), 0, 10)." 23:59:59"));
							$output .= "RRULE:FREQ=".strtoupper(trim($row['recurrence'])).";INTERVAL=".trim($row['recurrenceInterval']);
							if (trim($row['recurrence']) == 'Yearly') {
								$output .= ";BYMONTH=".date('n', $startTimeStamp);
							}
							if (trim($row['recurrence']) == 'Monthly' || trim($row['recurrence']) == 'Yearly') {
								$recurrenceEnd = date("Y", $recurrenceTimeStamp).date("n", $recurrenceTimeStamp).date("t", $recurrenceTimeStamp)."T235959";
								$dateDiffStart = floor(date('j', $startTimeStamp) - 1);
								$weekNumStart = ceil($dateDiffStart/7);
								$output .= ";BYDAY=".$weekNumStart.$dayOfWeek[date('w', $startTimeStamp)];
								if ($consecDays > 1) {
									for ($j = 0; $j < count($consecDayOfWeek); $j++) {
										$output .= ",".$weekNumStart.$consecDayOfWeek[$j];
									}
								}
							}
							$output .= ";UNTIL=$recurrenceEnd";

						}
						$output .= "\nEND:VEVENT\n";
					}
				}
				$output .= "END:VCALENDAR";
				echo $output;
			}
		}
	}
	/**
	 * Displays the div container for quick view calendar dialog which is populated via ajax request data
	 *
	 * @access public
	 */
	public function quick_view_dialog()
	{
		echo '<div id="dlgQuickView" title="Events"></div>';
	}
	/**
	 * Returns a list of events formatted in a dl element for quick view calendar ajax request
	 *
	 * @access public
	 * @param string  $timestamp
	 * @see get_calendar_details()
	 * @see retrieve_all_events()
	 * @see create_timestamp()
	 * @return string
	 */
	public function get_quick_view_events_ajax($timestamp){
		if (preg_match("/^[0-9]{8,}$/", $timestamp, $matches)) {
			$dateSel = date("Y-m-d", $timestamp);
			$endDateStamp = ($timestamp)+(60*60*24);
			$dateSelEnd = date("Y-m-d", $endDateStamp);
			$calendars = $this->get_calendar_details();
			$res = $this->retrieve_all_events($dateSel, $dateSelEnd);
			if ($res != false) {
				$output = "<dl>";
				while ($row = $this->db->fetch_assoc($res)) {
					list($startDate, $startTime) = explode(" ", trim($row['eventStartDate']));
					list($endDate, $endTime) = explode(" ", trim($row['eventEndDate']));
					$startTimeStamp = $this->create_timestamp(trim($row['eventStartDate']));
					$endTimeStamp = $this->create_timestamp(trim($row['eventEndDate']));
					if (array_key_exists(substr(trim($row['calendarID']), 0, 2), $calendars)) {
						$eventDate = ($startDate == $endDate)?date('M d', $startTimeStamp):date('M d', $startTimeStamp)."-".date('M d', $endTimeStamp);
						$link = (trim($row['detailPage']) == 1 && trim($row['detailsAlternateURL']) != "")?"http://".trim($row['detailsAlternateURL']): $this->url . "?event=".trim($row['itemID'])."&start=".$startTimeStamp;
						$output .= "<dt style='color:".$calendars[trim($row['calendarID'])]['colour']."'><a href=$link>".trim($row["eventTitle"])."</a></dt>";
						$output .= "<dd>$eventDate &#8226; ".date("g:i a", $startTimeStamp)."-".date("g:i a", $endTimeStamp)."<br />".trim($row['description'])."</dd>";
					}
				}
				$output .= "</dl>";
				return $output;
			}
		}
		return "false";
	}
	/**
	 * Returns day of week for first day of specified month
	 *
	 * @access protected
	 * @param string  $Year
	 * @param string  $Month
	 * @return string
	 */
	protected function get_first_day($Year, $Month)
	{
		$dayOneMonth=mktime(12, 00, 00, $Month, 1, $Year);
		$firstDay=date('w', $dayOneMonth) + 1;
		return $firstDay;
	}
	/**
	 * Return the number of days in a specified month
	 *
	 * @access protected
	 * @param string  $Year
	 * @param string  $Month
	 * @return string
	 */
	protected function month_length($Year, $Month)
	{
		$lastDayMonth=$Month+1;
		$numDays=mktime(0, 0, 0, $lastDayMonth, 0, $Year);
		$length=date('t', $numDays);
		return $length;
	}
	protected function format_month($monthNum){
		return (str_pad($monthNum,2,"0",STR_PAD_LEFT));
	}
	/**
	 * Returns an array of dates that have events occurring within the current calendar month
	 *
	 * @access protected
	 * @param string  $Month
	 * @param string  $Year
	 * @see get_calendar_details()
	 * @see retrieve_all_events()
	 * @return array
	 */
	protected function get_quick_view_dates($Month, $Year)
	{
		$eventStart = $Year."-".str_pad($Month,2,"0",STR_PAD_LEFT)."-01";
		$monthNext = (($Month + 1) > 12)?"01":(($Month + 1));
		$yearNext = (($Month + 1) > 12)?($Year + 1):$Year;
		$eventEnd = $yearNext."-".str_pad($monthNext,2,"0",STR_PAD_LEFT)."-01";
		$calendars = $this->get_calendar_details();
		$res = $this->retrieve_all_events($eventStart,$eventEnd);
		if ($this->db->valid($res) != false) {
			while ($row = $this->db->fetch_assoc($res)) {
				$startTimeStamp = $this->create_timestamp(trim($row['eventStartDate']));
				if (date('n', $startTimeStamp) == $Month && date('Y', $startTimeStamp) == $Year && array_key_exists(substr(trim($row['calendarID']), 0, 2), $calendars)) {
					$datesAvailable[date('j', $startTimeStamp)] = mktime(0, 0, 0, date('m', $startTimeStamp), date('j', $startTimeStamp), date('Y', $startTimeStamp));
				}
			}
			if (isset($datesAvailable)){
				return array_unique($datesAvailable);
			}
		}
		return array();
	}
	/**
	 * Creates a small calendar for viewing dates with events listed in db
	 * CSS is completely customizable
	 * returned as string so it can be accessed statically and dynamically (ajax)
	 * @access public
	 * @see get_quick_view_dates()
	 * @see get_first_day()
	 * @see month_length()
	 */
	public function quick_view_calendar($month = false, $year = false)
	{
		/*
		get the current and next month as numerical values 1 through 12
		get the year for each of the current and next month as YYYY for display
		*/
		$Month = ($month != false && $month > 0 && $month < 13)?$month:date('n');
		$Year = ($year != false && preg_match("/^20[0-9]{2}$/",$year,$matches))?$year:date('Y');
		$datesAvailable = $this->get_quick_view_dates($Month, $Year);
		/*
		call functions to create arrays for each month for:
		1) the day of the week of the first day of the month as numerical values 0-6
		2) the total number of days in the month
		3) the total number of days to display (includes white space to display the proper
		numerical date values under the correct day of the week.
		*/
		$firstDayOfWeek = $this->get_first_day($Year, $Month);
		$daysOfMonth = $this->month_length($Year, $Month);
		$displayDays = $daysOfMonth + $firstDayOfWeek;
		$daysEndOfMonth = 6 - date('w', mktime(0, 0, 0, $Month, $daysOfMonth, $Year));
		//determine number of days for each month to display highlight
		$today = date('j');
		$MonthNames=array("January", "February", "March", "April", "May", "June", "July",
			"August", "September", "October", "November", "December");
		$cal =  "<table id='calendarWidgetTable'>";
		$cal .= "<tr class='calHeader2'><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr>";
		//populate table
		$cal .= "<tr>";
		for ($i=1; $i < $displayDays; $i++) {
			if ($i < $firstDayOfWeek) {
				//$cal .= "<td class=\"tdCalSpacer\">&nbsp;</td>";
				$cal .= "<td class=\"tdCalSpacer\">".date("j",(mktime(0,0,0,$Month,1,$Year)-60*60*24*($firstDayOfWeek-$i)))."</td>";
			}
			else {
				$style = (array_key_exists(($i-$firstDayOfWeek+1), $datesAvailable))?"class=\"tdQvEvents\"":"";
				$onclick = (array_key_exists(($i-$firstDayOfWeek+1), $datesAvailable))?"onclick=show_quick_widget_events(this,'".$datesAvailable[($i-$firstDayOfWeek+1)]."')":"";
				if (($i-$firstDayOfWeek+1) == $today && $Month == date('n') && $Year == date('Y')) {
					$cal .= "<td class='calToday' $onclick>".($i-$firstDayOfWeek+1)."</td>";
				}
				else {
					$cal .= "<td $style $onclick>".($i-$firstDayOfWeek+1)."</td>";
				}
			}
			if ($i == ($displayDays - 1) && $daysEndOfMonth > 0) {
				for ($j = 0; $j < $daysEndOfMonth; $j++) {
					$cal .= "<td class=\"tdCalSpacer\">".date("j",(mktime(0,0,0,$Month,date("t"),$Year)+(60*60*24*(1+$j))))."</td>";
				}
			}
			if ($i % 7==0 && $i != ($displayDays-1)) {
				$cal .= "</tr><tr>";
			}
		}
		$cal .= "</tr></table>";
		return $cal;
	}
	/**
	* Returns the calendar body via ajax so users can scroll through months
	* @access public
	* @param int $adv
	* @param string $currentDate
	* @see quick_view_calendar();
	*/
	public function quick_view_calendar_ajax($adv,$currentDate){
		if ($adv == 1 || $adv == -1){
			$currentMonth = date("n",$currentDate);
			$currentYear = date("Y",$currentDate);
			$newMonth = $currentMonth + ($adv);
			$newYear = $currentYear;
			if ($newMonth == 13){
				$newMonth = 1;
				$newYear++;
			}
			else if ($newMonth == 0){
				$newMonth = 12;
				$newYear--;
			}
			$newTimeStamp = mktime(0,0,0,$newMonth,1,$newYear);
			$calTitle = date("F Y",$newTimeStamp);
			return $this->quick_view_calendar($newMonth,$newYear)."~".$newTimeStamp."~".$calTitle;
		}
		return 'false';
	}
	/**
	* Display search results on events page from side menu form
	* query can be based on multiple options
	* @access public
	* @param array $post
	* @param array $myTrips
	*/
	public function get_search_results($post){
		$where = false;
		$keys = false;
		if (isset($post["month"]) && preg_match("/^[01]{1}[0-9]{1}$/",trim($post["month"]),$matches)){
			$date = "e.eventStartDate";
			$searchState[] = date("F",mktime(0,0,0,trim($post["month"]),1,date("Y")));
			if (isset($post["day"]) && preg_match("/^[0123]{1}[0-9]{1}$/",trim($post["day"]),$matches)){
				$searchState[] = trim($post["day"]);
				$date .= " = '".date("Y")."-".trim($post["month"])."-".trim($post["day"])."'";
			}
			else{
				$date .= " >= '".date("Y")."-".trim($post["month"])."-01'";
			}
			$where[] = $date;
		}
		if (isset($post["keyword"]) && trim($post["keyword"]) != ""){
			$keys = "(e.description like '%".trim($post["keyword"])."%' OR e.eventTitle like '%".trim($post["keyword"])."%')";
			$searchState[] = trim($post["keyword"]);
		}
		if ($where == false && $keys == false){
			return false;
		}
		else{
			$qry = "SELECT e.itemID FROM tblCalendarEvents as e";
			$qryRecur = "SELECT concat(e.itemID,'_',re.itemID) as itemID FROM tblCalendarEvents as e INNER JOIN tblCalendarRecurringEvents as re ON e.itemID = re.calendarEventID";
			if ($keys != false){
				$joinTags = " WHERE ";
				$qry .= $joinTags.$keys;
				$qryRecur .= $joinTags.$keys;
			}
			if ($where != false){
				$joinTags = ($keys == false)?" WHERE ":" AND ";
				$qry .= $joinTags.implode(" AND ",$where);
				$qryRecur .= $joinTags.implode(" AND ",$where);
				$qryRecur = str_replace("e.eventStart","re.eventStart",$qryRecur);
			}
			$qry = "(".$qry.") UNION (".$qryRecur.")";
			$res = $this->db->query($qry);
			$data["searchState"] = implode(", ",$searchState);
			if ($this->db->valid($res) != false){
				while ($row = $this->db->fetch_assoc($res)){
					$details = $this->get_event_details_byID(array("event"=>trim($row["itemID"])));
					$info[] = array(
						"allDay"=>$details[0],
						"start" =>$details[1],
						"end" => $details[2],
						"location" => $details[3],
						"description" => $details[4],
						"recurrence" => $details[5],
						"title" => $details[6],
						"id" => trim($row["itemID"])
					);
				}
				$data["details"] = $info;
			}
			return $data;
		}
	}
	/**
	* This method displays upcoming events in a list widget within full Calendar page. 
	* Similar to listWidget but shows all details rather than linking to fullCalendar
	* Also accessed by ajax request when calendars are selected or deselected from the list
	* @access public
	* @param integer $limit
	* @param false|array $get
	* @see retrieve_all_events()
	* @see get_calendar_details()
	* @see create_timestamp()
	*/
    public function fullCalendar_upcoming_events($limit,$get = false)
    {
    	//if (intval($limit,10) > 0){
    		$startDate = date("Y-m")."-01";
    		$endDate = (date("n")+1 > 12)?(date("Y")+1)."-".$this->format_month((date("n")+1)-12)."-01":date("Y")."-".$this->format_month(date("n")+1)."-01";
    		if (isset($get['timestamp']) && preg_match("/^[0-9]{8,}$/",trim($get['timestamp']),$match)){
    			if (isset($get['cmmd']) && preg_match("/^(prev|next)$/",trim($get['cmmd']),$matches)){
    				if (trim($get["cmmd"]) == "prev"){
    					$endDate = date("Y-m",trim($get['timestamp']))."-01";
    					$month = $this->format_month(date("n",trim($get['timestamp']))-1);
    					$startDate = (date("n",trim($get['timestamp']))-1 == 0)?(date("Y",trim($get['timestamp']))-1)."-12-01":date("Y",trim($get['timestamp']))."-$month-01";
    				}
    				else if (trim($get["cmmd"]) == "next"){
    					$stMonth = $this->format_month(date("n",trim($get['timestamp']))+1);
    					$endMonth = $this->format_month(date("n",trim($get['timestamp']))+2);
    					$startDate = (date("n",trim($get['timestamp']))+1 == 13)?(date("Y",trim($get['timestamp']))+1)."-01-01":date("Y",trim($get['timestamp']))."-$stMonth-01";
    					$endDate = (date("n",trim($get['timestamp']))+1 == 13)?(date("Y",trim($get['timestamp']))+1)."-02-01":date("Y",trim($get['timestamp']))."-$endMonth-01";
    				}
    			}
    			else{
    				$stMonth = $this->format_month((date("n",trim($get['timestamp']))+1)-12);
    				$endMonth = $this->format_month(date("n",trim($get['timestamp']))+1);
    				$startDate = date("Y-m",trim($get['timestamp']))."-01";
    				$endDate = (date("n",trim($get['timestamp']))+1 > 12)?(date("Y",trim($get['timestamp']))+1)."-$stMonth-01":date("Y",trim($get['timestamp']))."-$endMonth-01";
    			}
    		}
    		$startTimeStamp = $this->create_timestamp($startDate." 00:00:00");
            $res = $this->retrieve_all_events($startDate,$endDate);
			$calendars = $this->get_calendar_details();
            if ($this->db->valid($res) && isset($calendars)) {
            	$calList = (!is_array($get['view']))?array_keys($calendars):$get['view'];
				$j = 0;
				echo "<dl>";
				while ($row = $this->db->fetch_assoc($res)) {
                	list($startDate, $startTime) = explode(" ", trim($row['eventStartDate']));
                    list($endDate, $endTime) = explode(" ", trim($row['eventEndDate']));
                    $startTimeStamp = $this->create_timestamp(trim($row['eventStartDate']));
                    $endTimeStamp = $this->create_timestamp(trim($row['eventEndDate']));
                    if (array_key_exists(substr(trim($row['calendarID']), 0, 2), $calendars) && in_array(trim($row['calendarID']),$calList) /*&& $startTimeStamp > date('U')*/) {
                    	if ($j < $limit) {
                        	$eventDate = ($startDate == $endDate)?date('M d, Y', $startTimeStamp):date('M d, Y', $startTimeStamp)."-".date('M d, Y', $endTimeStamp);
                            $link = (trim($row['detailPage']) == 1 && trim($row['detailsAlternateURL']) != "")?"<a href='http://".trim($row['detailsAlternateURL'])."'>Full Details</a>":"";
                            echo("<dt style='font-size:13px; font-weight:bold; color:".$calendars[trim($row['calendarID'])]['colour']."'>".
                            trim($row["eventTitle"])."
                            </dt>
                            <dd>$eventDate &#8226; ".date("g:i a", $startTimeStamp)."-".date("g:i a", $endTimeStamp)."<br />");
                            if ($link != ""){
                            	echo($link);
                            }
                            else{
                            	echo(trim($row['location'])."<br />".trim($row['description']));
                            }
                            echo "<span id=\"pAddEvent\" style=\"float:right;\"><a href=\"../../../../rss/icalEvents.php?event=".trim($row['itemID'])."\">Add to My Calendar</a></span></dd>";
                            $j++;
                        }
                    }
				}
				echo "</dl>";
             }
             echo("<p id=\"plistTimeStamp\" style=\"display:none\">$startTimeStamp</p>");
     	//}
     }
	/*
	* No constructor called so db object passed directly to Calendar class
	*/
}
?>