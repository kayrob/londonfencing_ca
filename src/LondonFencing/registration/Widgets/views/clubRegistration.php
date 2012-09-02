<?php 
global $user;

require_once dirname(dirname(__DIR__))."/registration.php";
require_once dirname(dirname(dirname(__DIR__))) . "/members/members.php";

use LondonFencing\registration as Reg;
use LondonFencing\members as MEMB;

$reg = new Reg\registration($db);
$mem = new MEMB\members($db);

$sessionNfo = $reg->getAdvancedSeason();

$regID = $mem->getRegistered($user->id, $sessionNfo["itemID"]);

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
    "11"    => "QC",
    "12"    => "SK",
    "13"    => "YT"               
    ) ;

if (!empty($sessionNfo)){
    global $message;
    $sent = 0;
    $body = '';
    $sessionDate = date('F j, Y', $sessionNfo['seasonStart']);
?>
<h2>Club Registration for Season: <?php echo date('Y', $sessionNfo['seasonStart']) . "-" . date('Y', $sessionNfo['seasonEnd']); ?></h2>

<?php
    if ($regID > 0){
        print alert_box("Thank you!<br />Your registration is complete. You can update your contact information below, or print out your consent form", 1);
    }
    
    if (!empty($_POST) && isset($_POST["nonce"]) && validate_form($_POST)) {

            $valid = 1;
            $regAge = ($sessionNfo["seasonStart"] - strtotime($_POST['RQvalDATEbirthDate']))/(60*60*24*365);

            if ($regAge < 9){
                $message .= "<li>You must be a minimum age of nine (9) by the time the season starts to register</li>";
                $valid = 0;
            }
            if ($regAge < 18 && trim($_POST['OPvalALPHparentName']) == ""){
                $message .= "<li>A Parent or Legal Guardian name must be provided. <em>Text</em></li>";
                $valid = 0;
            }
            if ($valid == 1){
                list($sent, $message) = $reg->saveClubRegistration($_POST, $regID, $user->id);
            }
    }
    if ($sent == 1 && $regID == 0){
            $regID = $mem->getRegistered($user->id, $sessionNfo["itemID"]);
            include_once __DIR__ ."/clubRegConfirm.php";
        }
    else{
            $post = array(
            "RQvalALPHfirstName"                 => $user->get_meta("First Name"),
            "RQvalALPHlastName"                  => $user->get_meta("Last Name"),
            "RQvalDATEbirthDate"                  => $user->get_meta("Birthdate"),
            "RQvalALPHgender"                     => $user->get_meta("Gender"),
            "RQvalALPHaddress"                     => $user->get_meta("Address"),
            "OPvalALPHaddress2"                   => $user->get_meta("Unit/Apt"),
            "RQvalALPHcity"                           => $user->get_meta("City"),
            "RQvalALPHprovince"                    => $provID[$user->get_meta("Province")],
            "RQvalPOSTpostalCode"                => $user->get_meta("Postal Code"),
            "RQvalPHONphoneNumber"          => $user->get_meta("Phone Number"),
            "RQvalMAILemail"                         => $user->get_meta("E-Mail"),
            "OPvalMAILaltEmail"                     => $user->get_meta("Alternate E-Mail"),
            "OPvalALPHparentName"              => $user->get_meta("Parent/Guardian"),
            "RQvalALPHemergencyContact"    => $user->get_meta("Emergency Contact"),
            "RQvalPHONemergencyPhone"      => $user->get_meta("Emergency Phone Number"),
            "OPvalALPHnotes"                        => $user->get_meta("Notes"),
            "OPvalALPHcffNumber"                => $user->get_meta("CFF Number")
            ); 
            $feeTypes = array("annually" => "Annual (one-time)", "quarterly" => "Quarterly (4-times)", "monthly" => "Monthly");
            $membershipTypes = array("Excellence", "Foundation","Transition");
            
            if ($regID == 0){
                $regPost = array(
                "RQvalALPHmembershipType" => "",
                "RQvalALPHfeeType"                => ""   
                 );
            }
            
            include_once __DIR__ ."/registrationFormFields.php";
            global $quipp;
            $quipp->js['footer'][] = "/src/LondonFencing/registration/assets/js/registration.js";
    }
}
else{
        print alert_box("Club Registration is Currently Unavailable", 3);
}