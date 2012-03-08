<?php
namespace  LondonFencing\src\Tournmaents;
require_once dirname(__DIR__)."/calendar/Calendar.php";

use LondonFencing\src\calendar\Calendar as Cal;
use \Exception as Exception;

class Tournaments  extends Cal\Calendar{
        
        protected function getOntarioTournaments(){
            $rss = @simplexml_load_file("http://www.airsetpublic.com/syndicate/public/16972/year.xml");
            if (is_object($rss)){
                
                    $feed = $rss->channel;
                    foreach ($feed->item as $tourn){
                        
                        preg_match("%(http:\/\/[a-zA-z\.\?\-0-9_=\/\%]*)(<br \/>)*(.*)%",  (string)$tourn->description,$dMatch);
                        $description = (isset($dMatch[1]))?trim(str_replace($dMatch[1],"",(string)$tourn->description)):(string)$tourn->description;
                        $regLink =  (isset($dMatch[1]))? $dMatch[1] : false;
                        preg_match('%End:\s?([A-Za-z]+\s\d{1,2}\,\s?\d{4}(.*([apAP][mM]))?)%',$description,$mEnd);
                        
                        $tEnd = (isset($mEnd[1])) ? strtotime(str_replace('at','',$mEnd[1])) : (strtotime((string)$tourn->pubDate) + (60*60*5));
                        $tourney[] = array(
                            "tdate"             =>  strtotime((string)$tourn->pubDate),
                            "tend"              => $tEnd,
                            "description"   =>  ltrim(ltrim($description,"<br />"),"<br>"),
                            "title"              =>  (string)$tourn->title,
                            "link"              => $regLink,
                            "eID"               => false
                        );
                    }
            }
            if (isset($tourney)){
                return $tourney;
            }
            return array();
        }
        protected function getCalendarTournaments(){
            $calendars = $this->get_calendar_details();
            $tournCalID = false;
            if (is_array($calendars)){
                foreach ($calendars as $calID => $calInfo){
                    if (strtolower($calInfo['name']) == 'tournaments'){
                        $tournCalID = $calID;
                    }
                }
            }
            if ($tournCalID !== false){
                $tEvents_json = $this->get_calendar_events_ajax(array('calendar'=>$tournCalID, 'start' => date('U'), 'end' => strtotime('+ 6 months')));
                if ($tEvents_json != 'false'){
                    $tEvents_obj = json_decode($tEvents_json);
                    foreach($tEvents_obj as $eData){
                        $description =$eData->description."<br />";
                        
                        $description .= 'Start: '.date('F j, Y g:i a',strtotime($eData->start)).'<br />End: '.date('F j, Y g:i a',strtotime($eData->end));
                        if ($eData->location != ""){
                            $description .= '<br />'.$eData->location;
                        }
                        $tourney[] = array(
                            "tdate"             =>  strtotime($eData->start),
                            "tend"              => strtotime($eData->end),
                            "description"   =>  $description,
                            "title"              =>  $eData->title,
                            "link"              => (isset($eData->altUrl) && trim($eData->altUrl) != '' ? trim($eData->altUrl) : false),
                            "eID"               => $eData->id
                        );
                    }
                }
                if (isset($tourney)){
                    return $tourney;
                }
            }
            return array();
        }
        public function getUpcomingTournaments(){
            $rssTourney = $this->getOntarioTournaments();
            $calTourney = $this->getCalendarTournaments();
            $tourneys = array_merge($rssTourney, $calTourney);
            return $tourneys;
        }
}
