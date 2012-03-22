<?php
require_once('../../../../../inc/init.php');
require_once dirname(dirname(__DIR__)).'/calendar.php';

use LondonFencing\calendar\Widgets as CAL;
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' || (isset($_GET['isAjax']) && trim($_GET['isAjax']) == 'y')){
	$response = "false";
                  $cal = new CAL\calendarWidgets($db);
	//set initial sources feed for fullCalendar
	if (isset($_GET['calendar']) && trim($_GET['calendar']) === 's'){
		$response = $cal->get_calendars_ajax();
	}
	else if (isset($_GET['calendar']) && preg_match('/^[0-9]{1,6}$/',$_GET['calendar'],$matches)){
		$response = $cal->get_calendar_events_ajax($_GET);
	}
	else if (isset($_GET['qview']) && preg_match('/^[0-9]{8,}$/',$_GET['qview'],$matches)){
		$response = $cal->get_quick_view_events_ajax(trim($_GET['qview']));
	}
	else if (isset($_GET["qvAdv"]) && preg_match("/(-)?[1]{1}/",trim($_GET["qvAdv"]),$matches) && isset($_GET["qvStamp"]) && preg_match("/^[0-9]{8,}$/",trim($_GET["qvStamp"]),$match)){
		$response = $cal->quick_view_calendar_ajax(trim($_GET["qvAdv"]),trim($_GET["qvStamp"]));
	}
    	else if (isset($_GET['qview']) && trim($_GET['qview']) == "list" && isset($_GET['view']) && is_array($_GET['view'])){
                    $post = $_GET;
                    unset($post['qview']);
        	$response = $cal->fullCalendar_upcoming_events(10,$post);
    	}
echo($response);
}
else{
	header("location:/");
}