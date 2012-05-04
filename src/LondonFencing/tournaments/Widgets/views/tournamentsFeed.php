<?php
require_once(dirname(dirname(__DIR__)))."/tournaments.php";
use LondonFencing\tournmaents as Tmnts;

if ($this instanceof Page && isset($db)){


$tournObj = new Tmnts\tournaments($db);
$tournaments = $tournObj->getUpcomingTournaments();
$tourns = multi_array_subval_sort($tournaments,'tdate');
?>

<section class="callout">
    <h2>Tournaments</h2>
<?php
if (!empty($tourns)){

        $p = 0;
        foreach ($tourns as $tourn){
            if ($tourn['tdate'] >= date('U')){
                //if eID link to cal ICS, else static ICS with specific data
                $description = ltrim($tourn['description'],"<br />");
                $date = date('M j, Y g:i a',$tourn['tdate']);
                $date .= (date('Y-m-d', $tourn['tdate']) == date('Y-m-d', $tourn['tend']))? ' to '.date('g:i a',$tourn['tend']): ' - '.date('M j, Y g:i a', $tourn['tend']);
                $date = str_ireplace('12:00 am','',$date);
                
                $location = (preg_match('%[Ll]ocation\:(\s)?(.*)%', $description, $matches)) ? $matches[2] :'' ;
                $icsHREF = ($tourn["eID"] !== false) ? "/src/LondonFencing/calendar/assets/rss/icalEvents.php?event=".$tourn["eID"]:"/src/LondonFencing/StaticPage/ics.php?event=".urlencode($tourn["title"])."&start=".$tourn['tdate']."&end=".$tourn['tend'].'&location='.$location;
                $h4Class = ($p > 0) ?' class="bordered"' : '';
                echo '<h4'.$h4Class.'>'.preg_replace('%\(.*\)%' , '', $tourn["title"]).'</h4>';
                echo '<p>';
                echo '<a href="'.$icsHREF.'" class="icons green"><img src="/themes/LondonFencing/img/plus.png" alt="add to calendar" title="Add to Calendar" width="20px; height="20px" /></a>';
                if  (isset($tourn['link']) && $tourn['link'] !== false){
                    echo '<a href="'.$tourn['link'].'" target="_blank" class="icons blue"><img src="/themes/LondonFencing/img/extLink.png" alt="more info" width="20px; height="20px" title="More Info" /></a>';
                }
                echo'<span class="lowlight">Date:</span>&nbsp;&nbsp;&nbsp;&nbsp;'.$date.'<br />';
                if ($location != ''){
                    echo '<span class="lowlight">Where:</span>&nbsp;&nbsp;'.$location.'<br />';
                }
                else{
                    echo '<span class="lowlight">&nbsp;</span>&nbsp;&nbsp;<br />';
                }
                echo '</p>';
                $p++;
                if ($p == 2){
                    break;
                }
            }
        }
}
?>
    <p class="moreTournaments"><a href="/tournaments" class="readMore">more tournaments</a>
</section>
<?php
}