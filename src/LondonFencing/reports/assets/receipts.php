<?php
require_once('../../../../inc/init.php');
require_once(dirname(__DIR__).'/reports.php');
use LondonFencing\reports as RPT;

if (isset($_POST['submitReceipts']) && isset($_POST['taxesStart']) && isset($_POST['taxesEnd']) 
        && strtotime($_POST['taxesStart']) !== false && strtotime($_POST['taxesEnd']) !== false 
        && isset($_POST['taxesGroup']) && !empty($_POST['taxesGroup'])){
    
    $rpts = new RPT\reports($db);
    $recipients = array();
    switch($_POST['taxesGroup']){
        case 'beginner':
            $recipients = $rpts->getBeginnerReceipts(strtotime($_POST['taxesStart']), strtotime($_POST['taxesEnd']));
            break;
        case 'club':
            $recipients = $rpts->getClubReceipts(strtotime($_POST['taxesStart']), strtotime($_POST['taxesEnd']));
            break;
        
        case 'intermediate':
            $recipients = $rpts->getIntermediateReceipts(strtotime($_POST['taxesStart']), strtotime($_POST['taxesEnd']));
            break;
        
        default:
            header('location:/admin/apps/reports/index?rpt=Tax%20Receipts&e=2');
    }
}
else{
   header('location:/admin/apps/reports/index?rpt=Tax%20Receipts&e=1');
}