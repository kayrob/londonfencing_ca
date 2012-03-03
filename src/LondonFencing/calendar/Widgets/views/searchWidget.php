<?php
if (!isset($cal)){
	$cal = new calendarWidgets($db);
	$cal->url = '/events';
}
$srchResults = $cal->get_search_results($_POST);
if ($srchResults == false){
	echo "<h3>Search Results: you did not select any criteria.</h3>";
}
else{
	echo "<h3>Search Results For:</h3>";
	echo "<p><em>".$srchResults["searchState"]."</em></p>";
	if (isset($srchResults["data"])){
		echo "<dl id=\"dlListWidget\">";
		foreach ($data as $details){
			//echo out info here "allDay","start","end","location","description","recurrence","title","id"
		}
		echo "</dl>";
	}
	else{
		echo "<p>Your search did not return any results.</p>";
	}
}
