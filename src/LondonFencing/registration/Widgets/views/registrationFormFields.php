<?php
$post = array(
   "RQvalALPHfirstName"                 => "",
   "RQvalALPHlastName"                  => "",
   "RQvalDATEbirthDate"                  => "",
   "RQvalALPHaddress"                     => "",
   "OPvalALPHaddress2"                   => "",
   "RQvalALPHcity"                           => "",
   "RQvalALPHprovince"                    => "ON",
   "RQvalPOSTpostalCode"                => "",
   "RQvalPHONphoneNumber"          => "",
   "RQvalMAILemail"                         => "",
   "OPvalALPHparentName"              => "",
   "RQvalALPHemergencyContact"    => "",
   "RQvalPHONemergencyPhone"      => ""
);
if($sent == 1){
	print alert_box("<strong>Thank You!</strong> Your message was sent.", 1);
}else if (isset($message) && $message != '') {
	print alert_box($message, 2);
	foreach ($_POST as $key => $value){
	     $post[$key] = $value;
	}
}
$provs = array("AB","BC","MB","NB","NL","NS","NT","NU","ON","PE","QC","SK","YT");

?>
<form action="<?php print $_SERVER['REQUEST_URI']; ?>" method="post">
    <div>
        <label for="RQvalALPHfirstName" class="req">First Name</label></td>
        <input type="text" name="RQvalALPHfirstName" id="RQvalALPHfirstName"  value = "<?php echo $post["RQvalALPHfirstName"];?>" /> *
    </div>
    <div>
        <label for="RQvalALPHlastName" class="req">Last Name</label></td>
        <input type="text" name="RQvalALPHlastName" id="RQvalALPHlastName"  value = "<?php echo $post["RQvalALPHlastName"];?>" /> *
    </div>
    <div>
        <label for="RQvalDATEbirthDate" class="req">Birth Date</label></td>
        <input type="text" name="RQvalDATEbirthDate" id="RQvalDATEbirthDate"  value = "<?php echo $post["RQvalDATEbirthDate"];?>" /> *
    </div>
    <div>
        <label for="RQvalALPHaddress" class="req">Address</label></td>
        <input type="text" name="RQvalALPHaddress" id="RQvalALPHaddress"  value = "<?php echo $post["RQvalALPHaddress"];?>" /> *
    </div>
    <div>
        <label for="OPvalALPHaddress2" class="req">Unit/Apt</label></td>
        <input type="text" name="OPvalALPHaddress2" id="OPvalALPHaddress2"  value = "<?php echo $post["OPvalALPHaddress2"];?>" />
    </div>
    <div>
        <label for="RQvalALPHcity" class="req">City</label></td>
        <input type="text" name="RQvalALPHcity" id="RQvalALPHcity"  value = "<?php echo $post["RQvalALPHcity"];?>" /> *
    </div>
    <div>
        <label for="RQvalALPHprovince" class="req">Province</label></td>
        <select name="RQvalALPHprovince" id="RQvalALPHcity">
        <?php
            foreach ($provs as $abbrev){
                echo '<option value="'.$abbrev.'"'.($post["RQvalALPHprov"] == $abbrev ? 'selected="selected"':'').'>'.$abbrev.($post["RQvalALPHprov"] == $abbrev ? '*':'').'</option>';
            }
        ?>
        </select> *
    </div>
    <div>
        <label for="RQvalALPHpostalCode" class="req">Postal Code</label></td>
        <input type="text" name="RQvalALPHpostalCode" id="RQvalALPHpostalCode"  value = "<?php echo $post["RQvalALPHpostalCode"];?>" /> *
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
        <label for="OPvalALPHparentName" class="req">Parent/Guardian</label></td>
        <input type="text" name="OPvalALPHparentName" id="OPvalALPHparentName"  value = "<?php echo $post["OPvalALPHparentName"];?>" /> (if Registrant is less than 18 years old)
    </div>
    <div>
        <label for="RQvalALPHemergencyContact" class="req">Emergency Contact</label></td>
        <input type="text" name="RQvalALPHemergencyContact" id="RQvalALPHemergencyContact"  value = "<?php echo $post["RQvalALPHemergencyContact"];?>" />*
    </div>
        <div>
        <label for="RQvalPHONemergencyPhone" class="req">Emergency Phone</label></td>
        <input type="text" name="RQvalPHONemergencyPhone" id="RQvalPHONemergencyPhone"  value = "<?php echo $post["RQvalPHONemergencyPhone"];?>" />*
    </div>
