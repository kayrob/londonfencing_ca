<?php
require_once('../../../../inc/init.php');
require_once(dirname(__DIR__).'/reports.php');
use LondonFencing\reports as RPT;
$sent = "fail";
if (isset($_POST["rpt"]) && $_POST["rpt"] = "Send_Tax_Receipts"){
    $rpts = new RPT\reports($db);
    $sent = $rpts->emailTaxReceipts();
echo $sent;
}
else{
    header("location: /");
}