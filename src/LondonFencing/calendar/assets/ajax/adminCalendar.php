<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' || (isset($_GET['isAjax']) && trim($_GET['isAjax']) == 'y') && count($_POST) > 0) {
	require_once '../../../../inc/init.php';
                  require_once '../../Calendar.php';
	require_once '../../Apps/adminCalendar.php';
	$response = "false";
	$cal = new adminCalendar($db);
	//set initial sources feed for fullCalendar
	if (isset($_GET['request'])) {
		if (trim($_GET['request']) == 'calendar') {
			if (isset($_GET['action'])) {
                                                                        $post = $_GET;
                                                                        unset($post["action"]);
                                                                        unset($post["request"]);
				switch (trim($_GET['action'])) {
				case('u'):
					$response = $cal->update_calendar($post);
					break;
				case('d'):
					$response = $cal->disable_calendar($post);
					break;
				default:
					$response = $cal->add_new_calendar($post);
					break;
				}
			}
		}
		else if (trim($_GET['request']) == 'event') {
				if (isset($_GET['action'])) {
                                                                                         $post = $_GET;
                                                                                         unset($post["action"]);
                                                                                         unset($post["request"]);
					switch (trim($_GET['action'])) {
					case('r'):
						$response = $cal->update_recurring_events($post);
						break;
					case('u'):
						$response = $cal->update_event($post);
						break;
					case('d'):
						$response = $cal->delete_events_main($post);
						break;
					default:
						$response = $cal->add_new_event($post);
						break;
					}
				}
			}
		else if (trim($_GET['request']) == 'list') {
				$response = $cal->display_calendar_list_admin();
			}
		else if (trim($_GET['request']) == 'style') {
				$response = $cal->buildCalendarCSS();
			}
	}
echo $response;
}
else {
	header("location:/");
}
?>