<?php
header('Content-Type: text/xml');
require_once("../inc/init.php");
$calendar = (isset($_GET['calendar']) && preg_match("/^[0-9]{1,6}$/",trim($_GET['calendar']),$matches))?trim($_GET['calendar']):false;
$cal->output_rss($calendar);
?>