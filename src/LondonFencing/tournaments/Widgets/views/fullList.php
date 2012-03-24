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
    <h2>Tournaments <?php echo (isset($_GET['filter']) ? ' - '.date('F Y', $_GET['filter']): ''); ?></h2>
<?php
if (!empty($tourns)){

        $start = 10 * ($page - 1);
        $p = 0;
        $end = ($showAll === true)? count($tourns) : $start + 10;
        foreach ($tourns as $tourn){
            if ($tourn['tdate'] >= $filterStartDate && $tourn['tdate'] < $filterEndDate && $p >= $start){
                //if eID link to cal ICS, else static ICS with specific data
                $icsHREF = ($tourn["eID"] !== false) ? "/admin/assets/rss/icalEvents.php?event=".$tourn["eID"]:"/src/LondonFencing/StaticPage/ics.php?event=".$tourn["title"]."&start=".$tourn['tdate']."&end=".$tourn['tend'];
                $description = ltrim($tourn['description'],"<br />");
                $date = date('M d, Y g:i a',$tourn['tdate']);
                $date .= (date('Y-m-d', $tourn['tdate']) == date('Y-m-d', $tourn['tend']))? ' to '.date('g:i a',$tourn['tend']): ' - '.date('M d, Y g:i a', $tourn['tend']);
                $date = str_ireplace('12:00 am','',$date);
                
                $location = (preg_match('%[Ll]ocation\:(\s)?(.*)%', $description, $matches)) ? $matches[2] :'' ;
                $h4Class = ($p > 0) ?' class="bordered"' : '';
                echo '<h4'.$h4Class.'>'.preg_replace('%\(.*\)%' , '', $tourn["title"]);
                echo '<a href="'.$icsHREF.'"><img src="/themes/LondonFencing/img/plusCalendar_32.png" alt="add to calendar" title="Add to Calendar" width="32px; height="32px" /></a>';
                if  (isset($tourn['link']) && $tourn['link'] !== false){
                    echo '<a href="'.$tourn['link'].'" target="_blank"><img src="/themes/LondonFencing/img/extLink_32.png" alt="more info" width="32px; height="32px" title="More Info" /></a>';
                }           
                echo '</h4>';
                echo '<p>';
                echo'<span class="lowlight">Date:</span>&nbsp;&nbsp;&nbsp;&nbsp;'.$date.'<br />';
                if ($location != ''){
                    echo '<span class="lowlight">Where:</span>&nbsp;&nbsp;'.$location.'<br />';
                }
                echo '</p>';
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