<?php
if (isset($_GET['event']) && isset($_GET['start']) && isset($_GET['end']) && is_numeric($_GET['start']) && is_numeric($_GET['end'])){

$eventStart = "TZID=America/Toronto:".date('Ymd',$_GET['start'])."T".date('His',$_GET['start']);
$eventEnd = "TZID=America/Toronto:".date('Ymd',$_GET['end'])."T".date('His',$_GET['end']);
$output .= "BEGIN:VCALENDAR\nVERSION:2.0\n";
$output .= "BEGIN:VEVENT\n";
$output .= "UID:uid".date('U')."@".$_SERVER['SERVER_NAME']."\n";
$output .= "DTSTAMP;TZID==America/Toronto:".date('Ymd')."T".date('His')."\n";
$output .= "DTSTART;$eventStart\n";
$output .= "DTEND;$eventEnd\n";
$output .= "SUMMARY: ".urldecode(trim($_GET['event']))."\n";
if (isset($_GET['location']) && trim($_GET['location']) != ''){
    $output .= "LOCATION:".urldecode(stripslashes(trim($_GET['location'])))."\n";
}
if (isset($_GET['description']) && trim($_GET['description']) != ''){
    $output .= "DESCRIPTION:".urldecode(stripslashes(trim($_GET['description'])))."\n";
}
$output .= "PRIORITY:3\n";
$output .= "END:VEVENT\n";
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
