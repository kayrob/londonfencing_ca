<?php
require_once dirname(dirname(dirname(__DIR__)))."/registration/registration.php";
use LondonFencing\registration as Reg;
require_once dirname(dirname(__DIR__)) ."/calendar.php";
use LondonFencing\calendar\Widgets AS cWid;

if (!isset($reg) && !$reg instanceof Reg\registration){
    $reg = new Reg\registration($db);
}

if ($this instanceof Page && isset($db) && isset($_GET['p']) && preg_match('%(intermediate|beginner)%', $_GET['p'],$matches)){

    $sessionNfo = $reg->getRegistrationSession($matches[1]);
    $practices = array();
    if (!isset($sessionNfo['isOpen'])){
        
        $cal = new cWid\calendarWidgets($db);
        $calendars = $cal->get_calendar_details();
        if (is_array($calendars)){
            foreach ($calendars as $calID => $calInfo){
                if (preg_match("%^{$matches[1]}%i",$calInfo['name'],$match)){
                    $events = $cal->get_calendar_events_ajax(array('calendar' => $calID, 'start' => date('U'), 'end' => strtotime('+ 16 days')));
                    if ($events !== false){
                        $events = json_decode($events);
                        foreach ($events as $eData){
                            if (count($practices) < 3){
                                $practices[] = array(
                                    "start"           =>  strtotime($eData->start),
                                    "end"             => strtotime($eData->end),
                                    "location"        =>  $eData->location,
                                    "title"           =>  "London Fencing Club: ".$eData->title,
                                    "eID"             => $eData->id,
                                    "type"            => $match[1]
                                );
                            }
                            else{
                                break;
                            }
                        }
                    }
                }
            }
        }
    }
?>
<div id="practiceSideNav">
    <div class="blankMainHeader"><h2>Next Practice</h2></div>
<?php
    if (!empty($practices)){
        foreach ($practices as $index => $pData){
           $mapLoc = (strstr($pData['location'],"Boyle") !== false)?"530+Charlotte+Street+London+Ontario":str_replace(" ","+",stripslashes($pData['location']))."+London+Ontario";
           $ulClass = ($index > 0) ? " class=\"bordered\"": "";
           $map = "http://maps.google.com/maps/api/staticmap?zoom=15&amp;markers=".$mapLoc."&amp;size=600x400&amp;sensor=false";
           echo '<ul'.$ulClass.'>';
           echo'<li><span class="lowlight">Time:</span>&nbsp;&nbsp;'.date('g:i a',$pData['start']).' to  '.date('g:i a',$pData['end']).'<br />';
           echo '<span class="lowlight">Date:</span>&nbsp;&nbsp;&nbsp;'.date('D M j, Y',$pData['start']).'<br />';
           echo '<span class="lowlight">Where:</span>&nbsp;'.stripslashes($pData['location']).'</li>';
           echo '<li><a href="#mapit_'.$index.'" class="fbMap icons blue"><i class="icon-location" title="View Map"></i></a>';
           echo '<a href="/src/LondonFencing/calendar/assets/rss/icalEvents.php?event='.$pData["eID"].'" class="icons green"><i class="icon-plus" title="Add to Calendar"></i></a></li>';    
           echo '</ul>';
           echo '<div style="display:none"><img src="'.$map.'" id="mapit_'.$index.'" alt="" /></div>';
           $p++;
        }
        global $quipp;
        $quipp->js['footer'][] = "/src/LondonFencing/calendar/assets/js/practice.js";
    }
    else{
        echo "<p>No {$matches[1]} practices scheduled.<br/>Please check back soon.</p>";
    }
?>
</div>
<?php
}