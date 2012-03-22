<?php
require_once(dirname(dirname(__DIR__)))."/tournaments.php";
use LondonFencing\tournmaents as Tmnts;

if ($this instanceof Page && isset($db)){


$tournObj = new Tmnts\tournaments($db);
$tournaments = $tournObj->getUpcomingTournaments();
$tourns = multi_array_subval_sort($tournaments,'tdate');

?>

<section class="tourneysNav">
    <h2>Filter</h2>
<?php
if (!empty($tourns)){
        
        $monthYr = "";
        echo '<ul>';
        foreach ($tourns as $tourn){
            if ($tourn['tdate'] >= date('U') && $monthYr != date('Y-m', $tourn['tdate'])){
                echo '<li><a href="/'.$_GET['p'].'&filter='.mktime(0,0,0, date('m',$tourn['tdate']),1, date('Y',$tourn['tdate'])).'">'.date('F Y', $tourn['tdate']).'</a></li>';
                $monthYr = date('Y-m', $tourn['tdate']);
            }
        }
        if (isset($_GET['filter'])){
            echo '<li class="clearFilter"><a href="/'.$_GET['p'].'">Clear Filter</li>';
        }
        echo '</ul>';
}
?>
</section>
<?php
}