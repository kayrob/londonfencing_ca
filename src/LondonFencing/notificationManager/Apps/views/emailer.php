<?php
require_once dirname(dirname(__DIR__))."/notificationManager.php";
use LondonFencing\notificationManager as NOTE;

$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require $root . '/inc/init.php';
if ((isset($_POST['eList']) && is_array($_POST['eList'])) || (isset($_POST['aList']) && is_array($_POST['aList']))){
    
    $meta['title'] = 'Emailer';
    $meta['title_append'] = ' &bull; Quipp CMS';
    
    global $message;
    $addresses = array();
    $a_addresses = array();
    $i_addresses = array();
    
    if (isset($_POST['eList'])){
        foreach($_POST['eList'] as $emailID){
            if (is_numeric($emailID) && (int)$emailID > 0){
                $addresses[] = (int)$db->escape($emailID,true);
            }
        }
    }
    if (isset($_POST['iList'])){
        foreach($_POST['iList'] as $emailID){
            if (is_numeric($emailID) && (int)$emailID > 0){
                $i_addresses[] = (int)$db->escape($emailID,true);
            }
        }
    }
    if (isset($_POST['aList'])){
        foreach($_POST['aList'] as $emailID){
            if (is_numeric($emailID) && (int)$emailID > 0){
                $a_addresses[] = (int)$db->escape($emailID,true);
            }
        }
    }
    if (!empty($addresses) || !empty($a_addresses)){
        
        $placeHolders = array("%NAME%" => "Name of Email Recipient");

        switch($_POST['etype']){

            case "class-reg":
                $placeHolders["%SESSION%"] = "Session Name of this Class";
                $placeHolders["%REGKEY%"] = "Individual Registration Confirmation Code";
                break;
            case "int-reg":
                $placeHolders["%REGKEY%"] = "Individual Registration Confirmation Code";
                break;
            case "members":
                $placeHolders["%CFFNUM%"] = "Member's CFC Number";
                $placeHolders["%LOGIN%"] = "Member's Website Username";
                $placeHolders["%REGKEY%"] = "Member's Default Website Password";
                break;
        }
        
        $fields[] = array(
        'label'   => "Subject",
        'dbColName'  => "subject",
        'tooltip'   => "The subject of the email",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
        );
        
        $messageTool = "The following tokens can be used for individual messages:<br />";
        $bodyVal = "";
        foreach ($placeHolders as $tokenName => $description){
            $messageTool .= $tokenName.": ".$description."<br />";
            $bodyVal .= $tokenName."\r\n";
        }
        $messageTool .="Where listed the tokens will be replaced with individual information. e.g %NAME% will be replaced with Karen Laansoo when applied.
            <br />Tokens representing individual information should be removed for batch emailing";
        
        $fields[] = array(
        'label'   => "Message",
        'dbColName'  => "body",
        'tooltip'   => "The content of the email. ".$messageTool,
        'writeOnce'  => false,
        'widgetHTML' => "<textarea class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" rows=\"20\" cols=\"80\">FIELD_VALUE</textarea>",
        'valCode'   => "RQvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
        );
        
        $formats = array("html" => "HTML", "rtf" => "Plain Text");
        $fields[] = array(
        'label'   => "Format",
        'dbColName'  => "format",
        'tooltip'   => "Choose HTML or Plain Text. HTML will use the London Fencing email template",
        'writeOnce'  => false,
        'widgetHTML' => "",
        'valCode'   => "RQvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
        );
        $sTypes = array("single" => "Individual", "batch" => "Batch");
        $fields[] = array(
        'label'   => "Send Type",
        'dbColName'  => "batch",
        'tooltip'   => "Choosing 'Batch' will send a single email and allow recipients to see other recipents' email addresses",
        'writeOnce'  => false,
        'widgetHTML' => "",
        'valCode'   => "RQvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
        );
        
        if (isset($_POST['sendEmail']) && isset($_POST['etype']) && validate_form($_POST)) {
            
            $ntfy = new NOTE\notificationManager($db);
            switch ($_POST['etype']){
                    case 'class-reg':
                        $sent = $ntfy->emailClassParticipants($_POST['RQvalALPHSubject'], $_POST['RQvalALPHMessage'], $addresses, $_POST['RQvalALPHSend_Type'], $_POST['RQvalALPHFormat'], 'beginner');
                        break;
                    case "int-reg":
                        $sent = $ntfy->emailClassParticipants($_POST['RQvalALPHSubject'], $_POST['RQvalALPHMessage'], $addresses, $_POST['RQvalALPHSend_Type'], $_POST['RQvalALPHFormat'], 'intermediate');
                        break;
                    case 'all-reg':
                        $sent = $ntfy->emailAllMembers($_POST['RQvalALPHSubject'], $_POST['RQvalALPHMessage'], $addresses, $a_addresses, $i_addresses, $_POST['RQvalALPHSend_Type'], $_POST['RQvalALPHFormat']);
                    break;
            }

        }
        include $root. "/admin/templates/header.php";
        
        ?>
        <h1>Emailer (* <?php echo (count($addresses) + count($a_addresses));?> Recipients)</h1>
        <p>This allows the ability to send pre-selected users an email from London Fencing Club.<br />Email will be sent from <strong><?php echo Quipp()->config('mailer.from_email');?></strong></p>
        <div class="boxStyle">
	<div class="boxStyleContent">
		<div class="boxStyleHeading">
			<h2>Create Email</h2>
			<div class="boxStyleHeadingRight">
			</div>
		</div>
		<div class="clearfix">&nbsp;</div>
		<div id="template">
<?php
        if (isset($sent) && $sent === true && empty($ntfy->errs)){
          echo '<p>All emails where sent!</p>';  
        }
        else{
            if ($message != '') {
                print $message;
            }
            if (isset($sent) && $sent === false){
                echo print_r($ntfy->errs, true);
            }
            $formBuffer = "<form enctype=\"multipart/form-data\" name=\"tableEditorForm\" id=\"tableEditorForm\" method=\"post\" action=\"/admin/apps/notificationManager/emailer\">
                <table>";

            //print the base fields
            $f=0;

            foreach ($fields as $field) {

                $formBuffer .= "<tr>";
                //prepare an ID and Name string with a validation string in it

                if ($field['dbColName'] != false) {

                    $newFieldIDSeed = str_replace(" ", "_", $field['label']);
                    $newFieldID = $field['valCode'] . $newFieldIDSeed;

                    $field['widgetHTML'] = str_replace("FIELD_ID", $newFieldID, $field['widgetHTML']);

                    //set value if one exists
                    if ($field['dbColName'] == 'sysStatus') {
                        if ($field['dbValue'] == 'active') {
                            $field['widgetHTML'] = str_replace("FIELD_VALUE", 'checked="checked"', $field['widgetHTML']);
                        } else {
                            $field['widgetHTML'] = str_replace("FIELD_VALUE", '', $field['widgetHTML']);
                        }
                    } 
                    else if ($field['dbColName'] == 'body'){
                        $field['dbValue'] = (isset($_POST[$newFieldID]) && $message != '')?$_POST[$newFieldID] : $bodyVal;
                        $field['widgetHTML'] = str_replace("FIELD_VALUE", $field['dbValue'] , $field['widgetHTML']);
                    }
                    else if ($field['dbColName'] == 'format'){
                        $field['dbValue'] = (isset($_POST[$newFieldID]) && $message != '')?$_POST[$newFieldID]:"rtf";
                        $field['widgetHTML'] = "<select name=\"".$newFieldID."\" id=\"".$newFieldID."\">";
                        foreach ($formats as $fType => $fLabel){
                            $field['widgetHTML'] .= "<option value=\"".$fType."\"".($fType == $field['dbValue']?'selected="selected"':'').">".$fLabel.($fType == $field['dbValue']?'*':'')."</option>";
                        }
                        $field['widgetHTML'] .= "</select>";
                    }
                    else if ($field['dbColName'] == 'batch'){
                        $field['dbValue'] = (isset($_POST[$newFieldID]) && $message != '')?$_POST[$newFieldID]:"single";
                        $field['widgetHTML'] = "<select name=\"".$newFieldID."\" id=\"".$newFieldID."\">";
                        foreach ($sTypes as $sType => $sLabel){
                            $field['widgetHTML'] .= "<option value=\"".$sType."\"".($sType == $field['dbValue']?'selected="selected"':'').">".$sLabel.($sType == $field['dbValue']?'*':'')."</option>";
                        }
                        $field['widgetHTML'] .= "</select>";

                    }
                    else {
                        if (isset($_POST[$newFieldID]) && $message != '') {
                            $field['dbValue'] = $_POST[$newFieldID];
                        }
                        $field['widgetHTML'] = str_replace("FIELD_VALUE", $field['dbValue'], $field['widgetHTML']);

                    }

                }

                //write in the html
                $formBuffer .= "<td valign=\"top\"><label for=\"".$newFieldID."\">" . $field['label'] . "</label></td><td>" . $field['widgetHTML'] . " <p>" . $field['tooltip'] . "</p></td>";
                $formBuffer .= "</tr>";
            }
            $formBuffer .= "<tr><td colspan='2'>
                <input type=\"hidden\" name=\"nonce\" value=\"".Quipp()->config('security.nonce')."\" />
                        <input type=\"hidden\" name=\"etype\" id=\"etype\" value=\"".$_POST['etype']."\" />";
            if (!empty($addresses)){
                foreach ($addresses as $eAddr){
                    $formBuffer .= "<input type=\"hidden\" name=\"eList[]\" id=\"eList_".$eAddr."\" value=\"".$eAddr."\" />";
                }
            }
            if (!empty($a_addresses)){
                foreach ($a_addresses as $aAddr){
                    $formBuffer .= "<input type=\"hidden\" name=\"aList[]\" id=\"aList_".$aAddr."\" value=\"".$aAddr."\" />";
                }
            }
            
            $formBuffer .= "</td></tr>";
            $formBuffer .= "</table>";
            $formBuffer .= "<div class=\"clearfix\" style=\"margin-top: 10px; height:10px; border-top: 1px dotted #B1B1B1;\">&nbsp;</div>";
            $formBuffer .= "<input class='btnStyle green' type=\"submit\" name=\"sendEmail\" id=\"sendEmail\" value=\"Send Email\" />";
            $formBuffer .= "</form>";
            //print the form
            print $formBuffer;
        }
        ?>
    </div><!-- end template -->
    <div class="clearfix">&nbsp;</div>

</div><!-- boxStyleContent -->
</div><!-- boxStyle -->
<?php

//end of display logic


include $root. "/admin/templates/footer.php";
    }
    else{
        echo '<p>No Addresses Selected</p>';
    }
    
}
else{
    print_r($_POST,true);
}
/*else{
    $auth->boot_em_out(1);
}*/
