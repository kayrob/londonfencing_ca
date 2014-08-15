<?php
require_once(dirname(dirname(__DIR__)))."/tournaments.php";
use LondonFencing\tournmaents as Tmnts;

if ($this instanceof Page && isset($db)){


$tournObj = new Tmnts\tournaments($db);
$tournaments = $tournObj->getUpcomingTournaments();
$tourns = multi_array_subval_sort($tournaments,'tdate');

?>

<section class="filtersNav">
    <div class="blankMainHeader"><h2>Filter</h2></div>
<?php
if (!empty($tourns)){
        
        $monthYr = "";
        echo '<ul>';
        foreach ($tourns as $tourn){
            if ($tourn['tdate'] >= date('U') && $monthYr != date('Y-m', $tourn['tdate'])){
                echo '<li>
                    <a href="/'.$_GET['p'].'&amp;filter='.mktime(0,0,0, date('m',$tourn['tdate']),1, date('Y',$tourn['tdate'])).'">'.date('F Y', $tourn['tdate']).'</a>
                    <a class="icons gray" href="/'.$_GET['p'].'&amp;filter='.mktime(0,0,0, date('m',$tourn['tdate']),1, date('Y',$tourn['tdate'])).'">
                        <i class="icon-arrow-right"></i></a>
                        </li>';
                $monthYr = date('Y-m', $tourn['tdate']);
            }
        }
        if (isset($_GET['filter'])){
            echo '<li ><a href="/'.$_GET['p'].'">Clear Filter</a><a href="/'.$_GET['p'].'" class="icons gray"><i class="icon-arrow-left"></i></a></li>';
        }
        echo '</ul>';
}
?>
</section>
<?php
}