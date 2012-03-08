<?php
if (isset($_GET['event']) && isset($_GET['start']) && isset($_GET['end']) && is_numeric($_GET['start']) && is_numeric($_GET['end'])){

$eventStart = "TZID=".date("e").":".date('Ymd',$_GET['start'])."T".date('Hi',$_GET['start']);
$eventEnd = "TZID=".date("e").":".date('Ymd',$_GET['end'])."T".date('Hi',$_GET['end']);
$output .= "BEGIN:VEVENT\n";
$output .= "UID:uid".date('U')."@".$_SERVER['SERVER_NAME']."\n";
$output .= "DTSTAMP;TZID=".date("e").":".date('Ymd')."T".date('Hi')."\n";
$output .= "DTSTART;$eventStart\n";
$output .= "DTEND;$eventEnd\n";
$output .= "SUMMARY: ".trim($_GET['event'])."\n";
$output .= "LOCATION:".(isset($_GET['location']) ? stripslashes(trim($_GET['location'])) : '')."\n";
$output .= "DESCRIPTION:".(isset($_GET['description']) ? trim($_GET['description']) : '')."\n";
$output .= "PRIORITY:3\n";
$output .= "\nEND:VEVENT\n";
$output .= "END:VCALENDAR";

preg_match("/(www.)?(.*)(.com|.ca)/",$_SERVER['SERVER_NAME'],$matches);
$fileName = str_replace(".","",$matches[2])."Events";
header('Content-Type: text/Calendar');
header("Content-Disposition: inline; filename=$fileName.ics");

echo $output;
}
else{
    header('location:http://'.$_SERVER["SERVER_NAME"]);
}
