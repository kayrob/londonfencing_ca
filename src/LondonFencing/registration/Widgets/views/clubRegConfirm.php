<?php
if (isset($user->id) && isset($regID) && (int)$regID > 0 && isset($reg) && $reg instanceof LondonFencing\registration\registration){
    
    $sessRes = $db->query(sprintf("SELECT * FROM `tblMembersRegistration` WHERE `itemID` = '%d' AND `userID` = '%d'", 
            (int)$regID,
            (int)$user->id
    ));
    
    if ($sessRes->num_rows == 1){
   
        $sessionSaved = $db->fetch_assoc($sessRes);
        $address = $user->get_meta("Address").", ".$user->get_meta("City")." ".$provID[$user->get_meta("Province")].", ".$user->get_meta("Postal Code");
        if (trim($user->get_meta("Unit/Apt")) != ""){
            $address = trim($user->get_meta("Unit/Apt"))." -  ".$address;
        }
        
        $emailTemplate = file_get_contents(dirname(dirname(dirname(__DIR__))).'/StaticPage/emailTemplate.html');
        $feeField = array('annually' => 'annualFee', 'quarterly' => 'quarterlyFee', 'monthly' => 'monthlyFee');
        
        $regKey = str_replace('LFC', str_pad($regID,2,'0',STR_PAD_LEFT), $user->get_meta("Registration Key"));
        
        $phone = str_format("(###) ###-####", str_replace("-","",$user->get_meta("Phone Number")));
        $ePhone = str_format("(###) ###-####", str_replace("-","",$user->get_meta("Emergency Phone Number")));
        
        $userEmail = $user->get_meta("E-Mail");
        
        $title = "Registration to London Fencing Club";
        $body = '<p><label>Season: </label>&nbsp;'.date('Y', $sessionNfo['seasonStart']) . "-" . date('Y', $sessionNfo['seasonEnd']).'<br />';
        $body .= '<label>Membership Type: </label>&nbsp;'.$sessionSaved["membershipType"].'</p><p>';
        $body .= '<label>Fee Type: </label>&nbsp;'.$sessionSaved["feeType"].'</p><p>';
        $body .= '<label>First Name: </label>&nbsp;'.$user->get_meta("First Name").'<br />';
        $body .= '<label>Last Name: </label>&nbsp;'.$user->get_meta("Last Name").' <br />';
        $body .= '<label>Date of Birth: </label>&nbsp;'.$user->get_meta("Birthdate").'<br />';
        $body .= '<label>Gender: </label>&nbsp;'.$user->get_meta("Gender").' <br />';
        $body .= '<label>Address: </label>&nbsp;'.$address.'<br />';
        $body .= '<label>Phone Number: </label>&nbsp;'.$phone.'<br />';
        $body .= '<label>Email Address: </label>&nbsp;'.$userEmail.'<br />';
        $body .= '<label>Parent/Guardian: </label>&nbsp;'.($user->get_meta("Parent/Guardian") != "" ?$user->get_meta("Parent/Guardian"):"N/A").'<br />';
        $body .= '<label>Emergency Contact: </label>&nbsp;'.$user->get_meta("Emergency Contact").'<br />';
        $body .= '<label>Emergency Phone: </label>&nbsp;'.$ePhone.'<br />';
        $body .= '<label>CFF Number: </label>&nbsp;'.($sessionSaved["membershipType"] == 'Foundation' ? 'N/A' : $user->get_meta("CFF Number")).'<br />';
        $body .= '<label>Confirmation Number: </label>&nbsp;'.$regKey.'<br />';
        $body .= '<label>Date Registered: </label>&nbsp;'.substr($sessionSaved['sysDateCreated'],0,10).'</p>';

        $body .= '<p>Print out your form to sign by clicking the link or copying it and pasting it into your browser: 
            <a href="http://'.$_SERVER["SERVER_NAME"].'/club-consent/'.$sessionNfo['itemID'].'/'.$regKey.'">http://'.$_SERVER["SERVER_NAME"].'/club-consent/'.$sessionNfo['itemID'].'/'.$regKey.'</a>';
        $body .= '<p>&nbsp;</p><p>Next Steps:</p>';
        $body .= '<ol>
        <li>Read the Terms and Conditions listed on the printable registration sheet</li>
        <li>Print this form out, sign it, and bring it with payment to the first class.<br />This form MUST be signed by the participant (if over the age of 18) 
            OR by the Parent/Guardian listed on the form (if the participant is under the age of 18)</li>
        <li>Make cheques payable to <strong>The London Fencing Club</strong> for the value of $'.number_format($sessionNfo[$feeField[$sessionSaved['feeType']]],2).'</li>
    </ol>';
        $body .= '<p>&nbsp;</p><p>Come dressed to move! All fencers are required to wear:</p>';
        $body .= '<ol><li>Athletic shoes with non-marking soles</li><li>Track pants (no shorts, jeans, khakis)</li></ol>';

        $emailBody = str_replace('%SERVERNAME%',$_SERVER['SERVER_NAME'],str_replace('%BODY%',$body,str_replace('%TITLE%',$title,$emailTemplate)));
        $subject = "London Fencing Session Registration";
        $from = Quipp()->config('mailer.from_email');
        $admEmail = Quipp()->config('mailer.from_email');

        $mail = new PHPMailer\PHPMailer();
        $mail->IsHTML(true);
        $mail->SetFrom($from);
        $mail->AddAddress($userEmail);
        $mail->AddBCC($admEmail);
        $mail->Subject = $subject;
        $mail->Body = $emailBody;
        if ($mail->send()){
                print alert_box("Thank you!<br />Please follow the steps below in order to complete your registration", 1);
        }
        else{
            print alert_box("An Email could not be sent.<br />Please use the print button below or contact <a href='mailto:\"".Quipp()->config('mailer.from_email')."\"'>".Quipp()->config('mailer.from_email')."</a>", 2);
        }
?>
<div>
    <h3>Next Steps:</h3>
    <ol>
        <li>Read the Terms and Conditions listed on the printable registration sheet</li>
        <li>Print this form out, sign it, and bring it with payment to the first class.<br />This form MUST be signed by the participant (if over the age of 18) 
            OR by the Parent/Guardian listed on the form (if the participant is under the age of 18)</li>
        <li>Make cheques payable to <strong>The London Fencing Club</strong> for the value of $<?php echo number_format($sessionNfo[$feeField[$sessionSaved['feeType']]],2);?></li>
    </ol>
    <h3>Come dressed to move! All fencers are required to wear:</h3>
    <ol><li>Athletic shoes with non-marking soles</li><li>Track pants (no shorts, jeans, khakis)</li></ol>
    <p>&nbsp;</p>
    <p>You will receive an email at the address you provided along with the information you submitted and a link to the printable version of this form</p>
    <p><a href="/club-consent/<?php echo $sessionNfo['itemID'];?>/<?php echo $regKey;?>" class="btnStyle" target="_blank">Print Form</a></p>
</div>
<?php
    }
    else{
        print alert_box("An Error Occured and your Registration information could not be displayed.<br />Please contact <a href='mailto:\"".Quipp()->config('mailer.from_email')."\"'>".Quipp()->config('mailer.from_email')."</a>", 2);
    }
}
