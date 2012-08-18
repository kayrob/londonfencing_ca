<?php
require_once('../../../../inc/init.php');
require_once dirname(__DIR__).'/registration.php';

use LondonFencing\registration as REG;

if (isset($_GET['s']) && is_numeric($_GET['s']) && isset($_GET['r'])){
    $regKeyData = explode("-", $_GET['r']);
    if (!isset($_SESSION['userID'])){
        $auth->boot_em_out(1);
    }
    if (count($regKeyData) != 3){
        header('location:http://'.$_SERVER["SERVER_NAME"]);
        exit;
    }
    $regKey = $regKeyData[0]."-".$regKeyData[1]."-LFC";
    
    $reg = new REG\Registration($db);
    $regNfo = $reg->getSavedClubRegistration($_GET['s'], $user->id, $regKeyData[2]);
    
    
    if (!empty($regNfo) && $user->get_meta('Registration Key') == $regKey && (int)$regNfo['formDate'] == 0){
        
        $feeField = array('annually' => 'annualFee', 'quarterly' => 'quarterlyFee', 'monthly' => 'monthlyFee');
        $feeTypes = array("annually" => "Annual (one-time)", "quarterly" => "Quarterly (4-times)", "monthly" => "Monthly");
        $provID = array(
            "1" => "AB", 
            "2" => "BC", 
            "3" => "MB", 
            "4" => "NB", 
            "5" => "NL",
            "6" => "NT",
            "7" => "NS",
            "8" => "NU",
            "9" => "ON",
            "10" => "PE",
            "11" => "QC",
            "12" => "SK",
            "13" => "YT"               
            ) ;
?>
<!doctype html>
<html>
    <head>
        <title>London Fencing Club</title>
        <style type="text/css" media="all">
            body {font-family: Arial, Verdana, Sans-serif; font-size:14px; width:740px;}
            div{width:640px;display:block;margin:0 0 5px 50px}
            label {line-height: 24px;display:inline-block;width: 230px; font-weight:bold;}
            div.sig{margin:40px 0 10px 50px}
            #head, #printHead{font-size: 18px;text-transform: uppercase;text-align:center;font-weight:bold}
            #footer{margin-top:20px;text-align:center}
            .termsTitle, .termsForm{text-transform: uppercase; font-weight: bold; text-align: center}
            #aprint{text-align: center;
                background: #4a762b; 
                border: none;
                 -moz-box-shadow: 1px 1px 5px rgba(0,0,0,0.12);
                -webkit-box-shadow: 1px 1px 5px rgba(0,0,0,0.12);
                box-shadow: 1px 1px 5px rgba(0,0,0,0.12);
                font-size: 12px;
                color: #FFF;
                padding: 8px 20px;
                border-radius: 3px;
                -moz-border-radius: 3px;
                -webkit-border-radius: 3px;
                font-style: italic;
                margin-top: 10px;
                line-height: normal; 
                cursor:pointer}
            #printHead{display:none}
        </style>
        <style type="text/css"media="print">
            #termsRefs{
                page-break-after: always;
            }
            #aprint{display:none;}
            #printHead{display:block}
        </style>
        <!--[if lte IE7]>
        <style type="css">
                label {display:inline;}
        </style>
        <![endif] -->
    </head>    
    <body>
        <a id="aprint" onclick="window.print()" class="btnStyle">Print</a>
        <div id="head">London Fencing Club Registration: <?php echo date('Y', $regNfo['seasonStart'])."-".date('Y', $regNfo['seasonEnd']); ?></div>
        <div id="termsRefs">
    <?php
     $birthDate = strtotime($user->get_meta('Birthdate'));
     $parentName = $user->get_meta('Parent/Guardian');
     $fn = $user->get_meta('First Name');
     $ln = $user->get_meta('Last Name');
    if ((date('U') - $birthDate)/(60*60*24*365) < 18 || trim($parentName) != ''){
        $terms = str_replace('%DATE%', date('F j, Y', $regNfo['seasonStart']), file_get_contents(__DIR__.'/minorTerms.php'));
        $terms = str_replace('%MINORNAME%', $fn.' '.$ln, $terms);
        echo str_replace('%PARENTNAME%', $parentName, $terms);
    }
    else{
        $terms = str_replace('%DATE%', date('F j, Y', $regNfo['seasonStart']), file_get_contents(__DIR__.'/adultTerms.php'));
        echo str_replace('%NAME%', $fn.' '.$ln, $terms);
    }
    ?>
    </div>
        <div id="printHead">London Fencing Club Registration: <?php echo date('Y', $regNfo['seasonStart'])."-".date('Y', $regNfo['seasonEnd']); ?></div>
    <div>
        <label>First Name: </label><?php echo $fn;?>
    </div>
    <div>
        <label>Last Name: </label><?php echo $ln;?>
    </div>
    <div>
        <label>Birth Date: </label><?php echo date('Y-m-d',$birthDate);?>
    </div>
        <div>
        <label>Gender: </label><?php echo $user->get_meta("Gender");?>
    </div>
    <div>
        <label>Address: </label><?php echo $user->get_meta("Address");?>
    </div>
    <div>
        <label>Unit/Apt: </label><?php echo $user->get_meta("Unit/Apt");?>
    </div>
    <div>
        <label>City: </label><?php echo $user->get_meta("City");?>
    </div>
    <div>
        <label>Province: </label><?php  echo $provID[$user->get_meta("Province")]; ?>
    </div>
    <div>
        <label>Postal Code: </label><?php echo $user->get_meta("Postal Code");?>
    </div>
    <div>
        <label>Phone Number: </label><?php echo str_format("(###) ###-####", str_replace("-","",$user->get_meta("Phone Number")));?>
    </div>
    <div>
        <label>Email: </label><?php echo $user->get_meta("E-Mail");?>
    </div>
    <div>
        <label>Parent/Guardian: </label><?php echo (trim($parentName) == '' ? 'N/A' : $parentName); ?>
    </div>
    <div>
        <label>Emergency Contact: </label><?php echo $user->get_meta("Emergency Contact");?>
    </div>
    <div>
        <label>Emergency Phone: </label><?php echo str_format("(###) ###-####", str_replace("-","",$user->get_meta("Emergency Phone Number")));?>
    </div>
    <div>
        <label>CFF Number: </label><?php echo $user->get_meta("CFF Number");?>
    </div>
    <div>
        <label>Membership Type: </label><?php echo $regNfo['membershipType'];?>
    </div>
    <div>
        <label>Fee Type: </label><?php echo $feeTypes[$regNfo['feeType']];?>
    </div>
   <div>
        <label>Allergies/Medical Conditions: </label><?php echo $user->get_meta("Notes");?>
    </div>
    <div class="sig">
        <label>Signature: </label>_____________________________________________
    </div>
        <div class="sig">
        <label>Date Signed: </label>______________________________________________
    </div>
    </div>
    <div class="sig">
        <label>Fee: </label>$<?php echo number_format($regNfo[$feeField[$regNfo['feeType']]],2);?><br /><br />Please make cheques payable to: London Fencing Club
    </div>
    <div id="footer"><img src="/src/LondonFencing/registration/assets/img/printLogo.png" /></div>
    </body>
</html>
<?php
    }
    else{
        header('location:http://'.$_SERVER["SERVER_NAME"]);
    }
}
else{
    header('location:http://'.$_SERVER["SERVER_NAME"]);
}
