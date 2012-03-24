<?php
require_once(dirname(dirname(__DIR__)))."/calendar.php";
use LondonFencing\calendar\Widgets AS cWid;
if ($this instanceof Page && isset($db)){
    $practices = array();
    $cal = new cWid\calendarWidgets($db);
    $calendars = $cal->get_calendar_details();
    if (is_array($calendars)){
        $practices = array();
        foreach ($calendars as $calID => $calInfo){
            if (preg_match("%^(beginner|advanced|intermediate)%i",$calInfo['name'],$match)){
                $events = $cal->get_calendar_events_ajax(array('calendar' => $calID, 'start' => date('U'), 'end' => strtotime('+ 16 days')));
               if ($events !== false){
                    $events = json_decode($events);
                    foreach ($events as $eData){
                        if (!isset($practices[$match[1]]) || strtotime($eData->end) < $practices[$match[1]]['start']){
                            $practices[$match[1]] = array(
                                "start"             =>  strtotime($eData->start),
                                "end"              => strtotime($eData->end),
                                "location"        =>  $eData->location,
                                "title"              =>  "London Fencing Club: ".$eData->title,
                                "eID"               => $eData->id,
                                "type"              => $match[1]
                            );
                        }
                    }
                }
            }
        }
    }
?>
<section class="callout" id="practiceWidget">
    <h2>Next Practice</h2>
<?php
    if (isset($practices) && !empty($practices)){
        $p = 0;
        $practices = multi_array_subval_sort($practices,'start');
        foreach ($practices as $type => $pData){
           $mapLoc = str_replace(" ","+",stripslashes($pData['location']));
           $map = "http://maps.google.com/maps/api/staticmap?center=43.021341,-81.212730&zoom=13&markers=".$mapLoc."&size=500x300&sensor=false";
           $h4Class = ($p > 0) ?' class="bordered"' : '';
           echo '<h4'.$h4Class.'>' .$pData['type'].'</h4>';
           echo '<p>';
           echo '<a href="/src/LondonFencing/calendar/assets/rss/icalEvents.php?event='.$pData["eID"].'"><img src="/themes/LondonFencing/img/plusCalendar.png" alt="add to calendar" title="Add to Calendar" width="49px; height="49px" /></a>';
           echo '<a href="#mapit_'.$pData["eID"].'" class="fbMap"><img src="/themes/LondonFencing/img/map.png" alt="view map" width="49p9x; height="px" title="View Map" /></a>';    
           echo'<span class="lowlight">Time:</span>&nbsp;&nbsp;'.date('g:i a',$pData['start']).' to  '.date('g:i a',$pData['end']).'<br />';
           echo '<span class="lowlight">Date:</span>&nbsp;&nbsp;&nbsp;'.date('D M j, Y',$pData['start']).'<br />';
           echo '<span class="lowlight">Where:</span>&nbsp;'.stripslashes($pData['location']).'</p>';
           echo '<div style="display:none"><img src="'.$map.'" id="mapit_'.$pData["eID"].'" /></div>';
           $p++;
        }
        global $quipp;
        $quipp->js['footer'][] = "/src/LondonFencing/calendar/assets/js/practice.js";
    }
}
?>
</section>