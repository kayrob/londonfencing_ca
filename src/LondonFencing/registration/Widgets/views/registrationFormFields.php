<?php

if (isset($sessionNfo['regMax']) && isset($sessionNfo['count']) && (int)$sessionNfo['count'] >= (int)$sessionNfo['regMax']){
    print alert_box("This session is currently full<br />Please complete the form to be put on the waitlist. You will be notified by email if a space becomes available", 3);
}

$sessionDate = date("F j, Y", $sessionNfo['eventStart']);

$post = array(
   "RQvalALPHfirstName"                 => "",
   "RQvalALPHlastName"                  => "",
   "RQvalDATEbirthDate"                  => "",
   "RQvalALPHgender"                     => "",
   "RQvalALPHaddress"                     => "",
   "OPvalALPHaddress2"                   => "",
   "RQvalALPHcity"                           => "",
   "RQvalALPHprovince"                    => "ON",
   "RQvalPOSTpostalCode"                => "",
   "RQvalPHONphoneNumber"          => "",
   "RQvalMAILemail"                         => "",
   "OPvalALPHparentName"              => "",
   "RQvalALPHemergencyContact"    => "",
   "RQvalPHONemergencyPhone"      => "",
    "OPvalALPHnotes"                        => ""
);
if (isset($message) && $message != '') {
        print alert_box($message, 2);
        foreach ($_POST as $key => $value){
	$post[$key] = $value;
        }
}
$provs = array("AB","BC","MB","NB","NL","NS","NT","NU","ON","PE","QC","SK","YT");
$gender = array("F" => "Female", "M" => "Male");
?>
<h3>Please complete all fields marked with *</h3>
<p><strong>Parent/Guardian is required for fencers under the age of 18</strong><br />&nbsp;</p>
<form action="<?php print $_SERVER['REQUEST_URI']; ?>" method="post" id="begIntRegForm">
    <div>
        <label for="RQvalALPHfirstName" class="req">First Name</label>
        <input type="text" name="RQvalALPHfirstName" id="RQvalALPHfirstName"  value = "<?php echo $post["RQvalALPHfirstName"];?>" /> *
    </div>
    <div>
        <label for="RQvalALPHlastName" class="req">Last Name</label>
        <input type="text" name="RQvalALPHlastName" id="RQvalALPHlastName"  value = "<?php echo $post["RQvalALPHlastName"];?>" /> *
    </div>
    <div>
        <label for="RQvalDATEbirthDate" class="req">Birth Date</label>
        <input type="text" name="RQvalDATEbirthDate" id="RQvalDATEbirthDate"  value = "<?php echo $post["RQvalDATEbirthDate"];?>" /> *
    </div>
    <div>
        <label for="RQvalALPHgender" class="req">Gender</label>
        <select name="RQvalALPHgender" id="RQvalALPHgender">
        <?php
            foreach ($gender as $gAbbr => $sex){
                echo '<option value="'.$gAbbr.'"'.($post["RQvalALPHgender"] == $gAbbr ? 'selected="selected"':'').'>'.$sex.($post["RQvalALPHprovince"] == $gAbbr ? '*':'').'</option>';
            }
        ?>
        </select> *
    </div>
    <div>
        <label for="RQvalALPHaddress" class="req">Address</label>
        <input type="text" name="RQvalALPHaddress" id="RQvalALPHaddress"  value = "<?php echo $post["RQvalALPHaddress"];?>" /> *
    </div>
    <div>
        <label for="OPvalALPHaddress2" class="req">Unit/Apt</label>
        <input type="text" name="OPvalALPHaddress2" id="OPvalALPHaddress2"  value = "<?php echo $post["OPvalALPHaddress2"];?>" />
    </div>
    <div>
        <label for="RQvalALPHcity" class="req">City</label>
        <input type="text" name="RQvalALPHcity" id="RQvalALPHcity"  value = "<?php echo $post["RQvalALPHcity"];?>" /> *
    </div>
    <div>
        <label for="RQvalALPHprovince" class="req">Province</label>
        <select name="RQvalALPHprovince" id="RQvalALPHprovince">
        <?php
            foreach ($provs as $abbrev){
                echo '<option value="'.$abbrev.'"'.($post["RQvalALPHprovince"] == $abbrev ? 'selected="selected"':'').'>'.$abbrev.($post["RQvalALPHprovince"] == $abbrev ? '*':'').'</option>';
            }
        ?>
        </select> *
    </div>
    <div>
        <label for="RQvalPOSTpostalCode" class="req">Postal Code</label>
        <input type="text" name="RQvalPOSTpostalCode" id="RQvalALPHpostalCode"  value = "<?php echo $post["RQvalPOSTpostalCode"];?>" /> *
    </div>
    <div>
        <label for="RQvalPHONphoneNumber" class="req">Phone Number</label>
        <input type="text" name="RQvalPHONphoneNumber" id="RQvalPHONphoneNumber" value = "<?php echo $post["RQvalPHONphoneNumber"];?>"/> *
    </div>
    <div>
        <label for="RQvalMAILemail" class="req">Email</label>
        <input type="email" name="RQvalMAILemail" id="RQvalMAILemail" value = "<?php echo $post["RQvalMAILemail"];?>"/> *
    </div>
    <div>
        <label for="OPvalALPHparentName" class="req">Parent/Guardian</label>
        <input type="text" name="OPvalALPHparentName" id="OPvalALPHparentName"  value = "<?php echo $post["OPvalALPHparentName"];?>" /> (if Participant is less than 18 years old)
    </div>
    <div>
        <label for="RQvalALPHemergencyContact" class="req">Emergency Contact</label>
        <input type="text" name="RQvalALPHemergencyContact" id="RQvalALPHemergencyContact"  value = "<?php echo $post["RQvalALPHemergencyContact"];?>" />*
    </div>
    <div>
        <label for="RQvalPHONemergencyPhone" class="req">Emergency Phone</label>
        <input type="text" name="RQvalPHONemergencyPhone" id="RQvalPHONemergencyPhone"  value = "<?php echo $post["RQvalPHONemergencyPhone"];?>" />*
    </div>
    <div>
        <label for="OPvalALPHnotes">Allergies and/or Medical Concerns</label>
        <textarea name="OPvalALPHnotes" id="OPvalALPHnotes" cols="50" rows="2"><?php echo $post["OPvalALPHnotes"];?></textarea>
    </div>
    <div class="submitWrap">
        <input type="button" id="regSubmit" value="Submit" name="sub-reg" class="btnStyle" />
        <input type="hidden" name="nonce" value="<?php echo Quipp()->config('security.nonce');?>" />
        <input type="hidden" name="RQvalNUMBsessionID" value="<?php echo (isset($sessionNfo['itemID'])?$sessionNfo['itemID']:'');?>" />
    </div>
</form>
<div style="display:none">
    <div  id="dvAConsent">
<?php echo str_replace('%DATE%', $sessionDate, file_get_contents(dirname(dirname(__DIR__)).'/assets/adultTerms.php')); ?>
        <p class="termsBtn"><a id="btnAdult" class="btnStyle" onclick="sendReg()">I have read and understand the terms of registration</a></p>
    </div>
</div>
<div style="display:none">
    <div id="dvMConsent" >
<?php echo str_replace('%DATE%', $sessionDate, file_get_contents(dirname(dirname(__DIR__)).'/assets/minorTerms.php')); ?>
        <p class="termsBtn"><a id="btnMinor" class="btnStyle" onclick="sendReg()">I have read and understand the terms of registration</a></p>
    </div>
</div>
<a href="#" id="terms" style="display:none"></a>