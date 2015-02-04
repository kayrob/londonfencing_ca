<?php
require_once('../../../../inc/init.php');
require_once(dirname(__DIR__).'/reports.php');
use LondonFencing\reports as RPT;

if (isset($_POST['submitFoundation']) && isset($_POST['foundationStart']) && isset($_POST['foundationEnd']) 
        && strtotime($_POST['foundationStart']) !== false && strtotime($_POST['foundationEnd']) !== false){
        
        $rpts = new RPT\reports($db);
        $members = $rpts->getFoundationsMembers(strtotime($db->escape($_POST['foundationStart'], true)), strtotime($db->escape($_POST['foundationEnd'],true)));
        if (!empty($members)){
//$csv = "CFF Licence,Lastname,Firstname,Email,Gender,Club,Club - 2,Year of Birth,Address,City,Province,Postal Code,Country,Phone,Language,Fencer,Booster,CFF license ( please check -$18. will be added)\n";
            $csv = "Firstname,Lastname,Gender,Club,Year of Birth,Membership Level,Address,City,Postal Code,Phone,CFF license (if known),Email\n";
        foreach($members as $mInfo){
                $address = (!empty($mInfo["address2"]) ? $mInfo['address2']."-".$mInfo['address'] : $mInfo['address']);
                $postal = str_replace(" ", "", $mInfo['postalCode']);
                $postal = substr($postal, 0, 3) ." ". substr($postal, 3);
                $phone = preg_replace("%[^\d]%", "-", $mInfo['phoneNumber']);
              /*  $csv .= ",\"".$mInfo['lastName']."\",\"".$mInfo['firstName']."\",".$mInfo["email"].",".$mInfo['gender'].",LFC,,".date("Y",$mInfo["birthDate"]).",";
                $csv .= "\"".trim($address, "-")."\",";
                $csv .= "\"".$mInfo['city']."\",".$mInfo['province'].",".$postal.",Canada,".$phone.",";
                $csv .= ",1,,\n";*/
                $csv .= "\"".$mInfo['firstName']."\",\"".$mInfo['lastName']."\",".$mInfo['gender'].",LON,".date("Y",$mInfo["birthDate"]).",";
                $csv .= "Recreation,\"".trim($address, "-")."\",";
                $csv .= "\"".$mInfo['city']."\",".$postal.",".$phone.",,".$mInfo["email"]."\n";
            }
header("Content-type: application/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=LFC_Foundation_".$_POST['foundationStart']."-".$_POST['foundationEnd'].".csv");
header("Pragma: no-cache");
header("Expires: 0");
echo $csv;
        }
        else{
            header('location:/admin/apps/reports/index?rpt=Foundation&e=0');
        }
}
else{
   header('location:/admin/apps/reports/index?rpt=Foundation&e=1');
}
