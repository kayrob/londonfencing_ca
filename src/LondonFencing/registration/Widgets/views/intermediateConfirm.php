<?php
if (isset($regKey) && isset($message) && isset($reg) && $reg instanceof LondonFencing\registration\registration){
    
    $sessionSaved = $reg->getIntRegRecord($regKey);
    if ($sessionSaved !== false){
        $address = $sessionSaved['address'].", ".$sessionSaved["city"]." ".$sessionSaved["province"].", ".$sessionSaved["postalCode"];
        if (trim($sessionSaved['address2']) != ""){
            $address = trim($sessionSaved['address2'])." -  ".$address;
        }
        $sessionYear = (date('n') >=9 )?date('Y')."-".(date("Y")+1): (date("Y") - 1)."-".date("Y");
        $emailTemplate = file_get_contents(dirname(dirname(dirname(__DIR__))).'/StaticPage/emailTemplate.html');
        
        $phone = str_format("(###) ###-####", str_replace("-","",$sessionSaved["phoneNumber"]));
        $ePhone = str_format("(###) ###-####", str_replace("-","",$sessionSaved["emergencyPhone"]));
        $title = "Registration to London Fencing Club";
        $body = '<p><label>Session: </label>&nbsp;Intermediate '.$sessionYear.'<br />';
        $body .= '<label>First Name: </label>&nbsp;'.$sessionSaved['firstName'].'<br />';
        $body .= '<label>Last Name: </label>&nbsp;'.$sessionSaved['lastName'].' <br />';
        $body .= '<label>Date of Birth: </label>&nbsp;'.date('Y-m-d',$sessionSaved['birthDate']).'<br />';
        $body .= '<label>Gender: </label>&nbsp;'.$sessionSaved['gender'].' <br />';
        $body .= '<label>Address: </label>&nbsp;'.$address.'<br />';
        $body .= '<label>Phone Number: </label>&nbsp;'.$phone.'<br />';
        $body .= '<label>Email Address: </label>&nbsp;'.$sessionSaved["email"].'<br />';
        $body .= '<label>Additional Email: </label>&nbsp;'.(trim($sessionSaved["altEmail"]) != "" ?$sessionSaved["altEmail"]:"N/A").'<br />';
        $body .= '<label>Parent/Guardian: </label>&nbsp;'.(trim($sessionSaved["parentName"]) != "" ?$sessionSaved["parentName"]:"N/A").'<br />';
        $body .= '<label>Emergency Contact: </label>&nbsp;'.$sessionSaved["emergencyContact"].'<br />';
        $body .= '<label>Emergency Phone: </label>&nbsp;'.$ePhone.'<br />';
        $body .= '<label>Confirmation Number: </label>&nbsp;'.$sessionSaved['registrationKey'].'<br />';
        $body .= '<label>Date Registered: </label>&nbsp;'.$sessionSaved['sysDateCreated'].'</p>';
        $body .= '<p>Print out your form to sign by clicking the link or copying it and pasting it into your browser: 
            <a href="http://'.$_SERVER["SERVER_NAME"].'/print-reg/I/'.$sessionSaved['registrationKey'].'">http://'.$_SERVER["SERVER_NAME"].'/print-reg/I/'.$sessionSaved['registrationKey'].'</a>';
        $body .= '<p>&nbsp;</p><p>Next Steps:</p>';
        $body .= '<ol>
        <li>Read the Terms and Conditions listed on the printable registration sheet</li>
        <li>Print this form out, sign it, and bring it with payment to the first class.<br />This form MUST be signed by the participant (if over the age of 18) 
            OR by the Parent/Guardian listed on the form (if the participant is under the age of 18)</li>
        <li>Make cheques payable to <strong>The London Fencing Club</strong></li>
    </ol>';
        $body .= '<p>&nbsp;</p><p>Come dressed to move! All fencers are required to wear:</p>';
        $body .= '<ol><li>Athletic shoes with non-marking soles</li><li>Track pants (no shorts, jeans, khakis)</li></ol>';
        $body .= '<p>All other fencing equipment will be provided by the Club</p>';

        $emailBody = str_replace('%SERVERNAME%',$_SERVER['SERVER_NAME'],str_replace('%BODY%',$body,str_replace('%TITLE%',$title,$emailTemplate)));
        $subject = "London Fencing ".ucwords($sessionSaved['level'])." Session Registration";
        $from = Quipp()->config('mailer.from_email');
        $admEmail = $db->return_specific_item(false, 'sysStorageTable', 'value', '--', "application='intermediate-registration'");
        if ($admEmail == "--"){
            $admEmail = Quipp()->config('mailer.from_email');
            //$admEmail = 'robertsonkaren@rogers.com';
        }
        $mail = new PHPMailer\PHPMailer();
        $mail->IsHTML(true);
        $mail->SetFrom($from);
        $mail->AddAddress($sessionSaved['email']);
        if (!empty($sessionSaved['altEmail'])){
            $mail->AddAddress($sessionSaved['altEmail']);
        }
        $mail->AddBCC($admEmail);
        $mail->Subject = $subject;
        $mail->Body = $emailBody;
        if ($mail->send()){
            print alert_box("Thank you!<br />Please follow the steps below in order to complete your registration", 1);
        }
        else{
            print alert_box("An Email could not be sent.<br />Please use the print button below or contact <a href='mailto:\"".Quipp()->config('mailer.from_email')."\"'>".Quipp()->config('mailer.from_email')."</a>", 2);
        }
    }
    else{
        print alert_box("An Error Occured and your Registration information could not be displayed.<br />Please contact <a href='mailto:\"".Quipp()->config('mailer.from_email')."\"'>".Quipp()->config('mailer.from_email')."</a>", 2);
    }
}
?>
<div>
    <h3>Next Steps:</h3>
    <ol>
        <li>Read the Terms and Conditions listed on the printable registration sheet</li>
        <li>Print this form out, sign it, and bring it with payment to the first class.<br />This form MUST be signed by the participant (if over the age of 18) 
            OR by the Parent/Guardian listed on the form (if the participant is under the age of 18)</li>
        <li>Make cheques payable to <strong>The London Fencing Club</strong></li>
    </ol>
    <h3>Come dressed to move! All fencers are required to wear:</h3>
    <ol><li>Athletic shoes with non-marking soles</li><li>Track pants (no shorts, jeans, khakis)</li></ol>
    <p>All other fencing equipment will be provided by the Club</p>
    <p>&nbsp;</p>
    <p>You will receive an email at the address you provided along with the information you submitted and a link to the printable version of this form</p>
    <p><a href="/print-reg/I/<?php echo $regKey;?>" class="btnStyle" target="_blank">Print Form</a></p>
</div>
