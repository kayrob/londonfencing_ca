<?php

try {
    $html = file_get_contents(__DIR__ . "/swp_tourneys.txt");
    $regLinks = array();
    $moreLinks = array();

    $qry = "";
    if (!empty($html)) {
        $hxtml = '<?xml version="1.0" ?><tourneys>' . str_replace('&', '&amp;', str_replace('&nbsp;', '', strip_tags($html, '<tr><a>'))) . '</tourneys>';
        $xml = simplexml_load_string($hxtml);
        for ($i = 2; $i < count($xml->tr); $i++) {
            $rows = $xml->tr[$i];
            $nfo = array($rows);
            
            foreach($nfo as $index => $tData){
                $regAttr = $tData->a[3]->attributes();
                preg_match('%javascript:OpenWindow\(\"(event_registration.asp\?d=\d+&v=\d+&e=\d+)\",\d+,\d+,\"(yes|no)\",\"event_registration\"\)%', $regAttr['href'], $matchReg);
                $moreAttr = (!empty($tData->a[9])) ? $tData->a[9]->attributes() : array();
                
                $eventTitle = mysql_escape_string((string)$tData->a[5]);
                list($startTime, $endTime) = explode('-', $tData->a[2]);
                $eventStart = date('Y-m-d G:i',strtotime($tData->a[1].' '.$startTime));
                $eventEnd = date('Y-m-d G:i', strtotime($tData->a[1].' '.$endTime));
                $location = "Toronto Fencing Academy Sword Players 5 Kodiak Cr. #2, Toronto, ON, M3J 3E5";
                $description = $tData->a[6].'<br />Level: '.$tData->a[0];
                $description .= '<br />Weapons: '.$tData->a[7];
                $description .= '<br />Ages: '.$tData->a[8];
                if (isset($moreAttr['href'])){
                    $description .= '<br /><br /><a href="'.$moreAttr['href'].'" target="_blank">More Info</a>';
                }
                $altLink = (isset($matchReg[1])) ?"http://www.fencersnetwork.com/calendar/".$matchReg[1] : '';
                $qry .= "INSERT INTO `tblCalendarEvents` (`calendarID`,  `recurrence`, `recurrenceInterval`, `allDayEvent`, `sysDateCreated`,`eventTitle`, `eventStartDate`, `eventEndDate`, `location`, `description`,`detailPage`, `detailsAlternateURL` )
                  VALUES ( '4', 'none', '0', '0', NOW(), '{$eventTitle}', '{$eventStart}', '{$eventEnd}', '{$location}', '".mysql_escape_string($description)."', '1','{$altLink}');";
                  
                  $qry .= '<br />';
            }
        }
        echo $qry;
    } else {
        echo "File empty";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}


//http://www.fencersnetwork.com/calendar/event_registration.asp?d=1706&v=6&e=7087

/*
 *  `calendarID`,  `recurrence`, `recurrenceInterval`, `allDayEvent`, `sysDateCreated``eventTitle`, `eventStartDate`, `eventEndDate`, `location `, `description`,`detailPage`, `detailsAlternateURL` 
 *  '4', 'none', '0', '0', NOW()
 */