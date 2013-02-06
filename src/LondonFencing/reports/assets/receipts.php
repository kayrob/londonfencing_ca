<?php
require_once('../../../../inc/init.php');
require_once(dirname(__DIR__).'/reports.php');
use LondonFencing\reports as RPT;

if (isset($_POST['submitReceipts']) && isset($_POST['taxesStart']) && isset($_POST['taxesEnd']) 
        && strtotime($_POST['taxesStart']) !== false && strtotime($_POST['taxesEnd']) !== false 
        && isset($_POST['taxesGroup']) && !empty($_POST['taxesGroup'])){
    
    $rpts = new RPT\reports($db);
    $created = "fail";
    $regKey = (isset($_POST['taxesSingle']) && !empty($_POST['taxesSingle'])) ? $_POST['taxesSingle'] : '';
    switch($_POST['taxesGroup']){
        case 'beginner':
            $created = $rpts->getBeginnerReceipts(strtotime($_POST['taxesStart']), strtotime($_POST['taxesEnd']), $regKey);
            break;
        case 'club':
            //$user is set in init.php
            $created = $rpts->getClubReceipts(strtotime($_POST['taxesStart']), strtotime($_POST['taxesEnd']), $user, $regKey);
            break;
        case 'intermediate':
            $created = $rpts->getIntermediateReceipts(strtotime($_POST['taxesStart']), strtotime($_POST['taxesEnd']), $regKey);
            break;
        default:
            header('location:/admin/apps/reports/index?rpt=TaxReceipts&e=2');
            break;
    }
    if ($created == 'success'){
        header('location:/admin/apps/reports/index?rpt=Tax_Receipts&s=1');
    }
    else{
        header('location:/admin/apps/reports/index?rpt=Tax_Receipts&e=0');
    }
}
else{
   header('location:/admin/apps/reports/index?rpt=Tax_Receipts&e=1');
}