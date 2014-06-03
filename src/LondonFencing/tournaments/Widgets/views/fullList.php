<?php
require_once(dirname(dirname(__DIR__)))."/tournaments.php";
use LondonFencing\tournmaents as Tmnts;

if ($this instanceof Page && isset($db)){


$tournObj = new Tmnts\tournaments($db);
$tournaments = $tournObj->getUpcomingTournaments();
$tourns = multi_array_subval_sort($tournaments,'tdate');

$showAll = (isset($_GET['show']) && $_GET['show'] == 'all' || isset($_GET['filter'])) ? true : false;
$page = (isset($_GET['page']) && (int)$_GET['page'] > 0 && $showAll === false) ? (int)$_GET['page'] : 1;

$filterStartDate = (isset($_GET['filter']))? $_GET['filter'] : date('U');
$filterEndDate = (isset($_GET['filter'])) ? strtotime('next month', $_GET['filter']): strtotime('+1 year');
?>

<section class="tournaments">
   	<div class="blankMainHeader">
    	<h2>Tournaments <?php echo (isset($_GET['filter']) ? ' - '.date('F Y', $_GET['filter']): ''); ?></h2>
    </div>
<?php
if (!empty($tourns)){

        $start = 10 * ($page - 1);
        $p = 0;
        $end = ($showAll === true)? count($tourns) : $start + 10;
        $border = false;
        foreach ($tourns as $tourn){
            if ($tourn['tdate'] >= $filterStartDate && $tourn['tdate'] < $filterEndDate && $p >= $start){
                //if eID link to cal ICS, else static ICS with specific data
                $description = ltrim($tourn['description'],"<br />");
                $date = date('M j, Y g:i a',$tourn['tdate']);
                $date .= (date('Y-m-d', $tourn['tdate']) == date('Y-m-d', $tourn['tend']))? ' to '.date('g:i a',$tourn['tend']): ' - '.date('M j, Y g:i a', $tourn['tend']);
                $date = str_ireplace('12:00 am','',$date);
                
                $location = (preg_match('%[Ll]ocation\:(\s)?(.*)%', $description, $matches)) ? $matches[2] :'' ;
                
                $icsHREF = ($tourn["eID"] !== false) ? "/src/LondonFencing/calendar/assets/rss/icalEvents.php?event=".$tourn["eID"]:"/src/LondonFencing/StaticPage/ics.php?event=".urlencode($tourn["title"])."&start=".$tourn['tdate']."&end=".$tourn['tend'].'&location='.$location;
                $h4Class =  '';
                if ($border == true){
                    $h4Class = ' class="bordered"';
                }
                else{
                    $border = true;
                }
                echo '<h4'.$h4Class.'>'.preg_replace('%\(.*\)%' , '', $tourn["title"]).'</h4>';
                echo '<ul>';
                echo '<li>';
                echo'<span class="lowlight">Date:</span>&nbsp;&nbsp;&nbsp;&nbsp;'.$date.'<br />';
                if ($location != ''){
                    echo '<span class="lowlight">Where:</span>&nbsp;&nbsp;'.$location.'<br />';
                }
                if (!empty($tourn['about'])){
                    echo '<span class="lowlight">About:</span>&nbsp;&nbsp;'.str_replace('<br />',', ',nl2br($tourn['about'])).'<br />';
                }
                echo '</li><li>';
                if  (isset($tourn['link']) && $tourn['link'] !== false){
                    $tournLink = preg_replace("%http(s)?:\/\/%", "", $tourn["link"]);
                    echo '<a href="http://'.$tournLink.'" target="_blank" class="icons blue"><i class="icon-link" title="More Info"></i></a>';
                }
                echo '<a href="'.$icsHREF.'" class="icons green"><i class="icon-plus" title="Add to Calendar"></i></a>';
                echo '</li>';
                echo '</ul>';
            }
            $p++;
            if ($p == $end){
                break;
            }
        }
}
if (!isset($_GET['filter'])){
    echo pagination(ceil(count($tourns) / 10), $page, "/tournaments&page=", 1 );
}
?>
</section>
<?php
}