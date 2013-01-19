<?php
require_once('../../../../inc/init.php');
require_once(dirname(__DIR__).'/reports.php');
use LondonFencing\reports as RPT;

if (!$auth->has_permission("canCreateReports")){
    $auth->boot_em_out(3);
    exit;
}
$rpt = new RPT\reports($db);
$adv = $rpt->getMembersEmergencyList();
$classes = $rpt->getClassesEmergencyList();
$beg = array();
$int = array();
if (!empty($classes)){
    foreach ($classes as $clID => $data){
        if ($data['level'] == 'beginner'){
            $beg[$clID] = $data;
        }
        else{
            $int[$clID] = $data;
        }
    }
}
?>
<!DOCTYPE HTML>
<html lang="en">
    <head>
        <title>London Fencing Club &bull; Emergency Contacts</title>
        <style type="text/css" media="all">
            body{font-family: Arial, Verdana, Sans-serf;width:640px;margin:0px auto}
            h1{font-size: 24px}
            h2{font-size: 18px;}
            table{width:100%}
            thead th{border-bottom: 3px double #ccc;padding-bottom: 10px}
            th{font-size:14px;text-align:left;padding:3px;}
            td{font-size:12px;width:33%;padding:4px}
            tbody tr:nth-child(even){background-color:#e2e2e2}
            .prntHdr{display:none}
            
        </style>
        <style type="text/css" media="print">
            .print{display:none}
            .break{page-break-after: always}
            .prntHdr{display:block}
        </style>
    </head>
    <body>
        <input type="button" class="print" value="print" onclick="window.print()" />
        <h1>London Fencing Club &bull; Emergency Contact List</h1>
<?php
    if (!empty($adv)){
        echo '<h2>Club Members</h2>';
        echo '<table>';
        echo '<thead><tr><th>Fencer Name</th><th>Emergency Contact</th><th>Contact Phone Number</th></tr></thead>';
        echo '<tbody>';
        foreach($adv as $member){
            echo '<tr><td>'.$member['name'].'</td><td>'.$member['contact'].'</td><td>'.$member['phone'].'</td></tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }
    if (!empty($int)){
        echo '<div class="break"></div>';
        echo '<h1 class="prntHdr">London Fencing Club &bull; Emergency Contact List</h1>';
        echo '<h2>Intermediate Members</h2>';
        echo '<table>';
        echo '<thead><tr><th>Fencer Name</th><th>Emergency Contact</th><th>Contact Phone Number</th></tr></thead>';
        echo '<tbody>';
        foreach($int as $member){
            echo '<tr><td>'.$member['name'].'</td><td>'.$member['contact'].'</td><td>'.$member['phone'].'</td></tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }
    if (!empty($beg)){
        echo '<div class="break"></div>';
        echo '<h1 class="prntHdr">London Fencing Club &bull; Emergency Contact List</h1>';
        echo '<h2>Beginner Class</h2>';
        echo '<table>';
        echo '<thead><tr><th>Fencer Name</th><th>Emergency Contact</th><th>Contact Phone Number</th></tr></thead>';
        echo '<tbody>';
        foreach($beg as $member){
            echo '<tr><td>'.$member['name'].'</td><td>'.$member['contact'].'</td><td>'.$member['phone'].'</td></tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }
    ?>
    </body>
    
    
    
</html>