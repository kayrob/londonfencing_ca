<?php
preg_match("/(www.)?(.*)(.com|.ca)/",$_SERVER['SERVER_NAME'],$matches);
$fileName = str_replace(".","",$matches[2])."Events";
header('Content-Type: text/Calendar');
header("Content-Disposition: inline; filename=$fileName.ics");
require_once("../inc/init.php");

//auto send $_GET so calendar or date set can be selected
$cal->output_ics($_GET);
?>