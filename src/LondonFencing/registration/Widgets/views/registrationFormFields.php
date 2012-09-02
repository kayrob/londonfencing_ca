<?php
//post fields are set in the file that calls this as the include. Beginner and Club Membership use the same for but have difft settings for $post
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
    <?php
     if (isset($post["OPvalMAILaltEmail"])){
 ?>
        <div>
        <label for="OPvalMAILaltEmail">Additional Email</label>
        <input type="text" name="OPvalMAILaltEmail" id="OPvalMAILaltEmail"  value = "<?php echo $post["OPvalMAILaltEmail"];?>" />
    </div>
<?php
     }
?>
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
<?php
     if (isset($post["OPvalALPHcffNumber"])){
 ?>
        <div>
        <label for="OPvalALPHcffNumber">CFF Number</label>
        <input type="text" name="OPvalALPHcffNumber" id="OPvalALPHcffNumber"  value = "<?php echo $post["OPvalALPHcffNumber"];?>" /> (Transition &amp; Excellence)
    </div>
<?php
     }
    if (isset($regPost) && isset($feeTypes) && isset($membershipTypes)){
?>
     <div>
        <label for="RQvalALPHmembershipType" class="req">Membership Type</label>
        <select name="RQvalALPHmembershipType" id="RQvalALPHmembershipType">
        <?php
            foreach ($membershipTypes as $memTypes){
                echo '<option value="'.$memTypes.'"'.($post["RQvalALPHmembershipType"] == $memTypes ? 'selected="selected"':'').'>'.$memTypes.($post["RQvalALPHmembershipType"] == $memTypes ? '*':'').'</option>';
            }
        ?>
        </select> *
    </div>
    <div>
        <label for="RQvalALPHfeeType" class="req">Fee Type</label>
        <select name="RQvalALPHfeeType" id="RQvalALPHfeeType">
        <?php
            foreach ($feeTypes as $ft => $ftLabel){
                echo '<option value="'.$ft.'"'.($post["RQvalALPHfeeType"] == $ft ? 'selected="selected"':'').'>'.$ftLabel.($post["RQvalALPHfeeType"] == $abbrev ? '*':'').'</option>';
            }
        ?>
        </select> *
    </div>
<?php
    }
?>
    <div>
        <label for="OPvalALPHnotes">Allergies and/or Medical Concerns</label>
        <textarea name="OPvalALPHnotes" id="OPvalALPHnotes" cols="20" rows="1" style="width:300px"><?php echo $post["OPvalALPHnotes"];?></textarea>
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