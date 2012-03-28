<?php
if (isset($sessionNfo) && isset($message) && isset($reg) && $reg instanceof LondonFencing\registration\registration){
    
    $sessionSaved = $reg->getSavedRegistration($sessionNfo["itemID"], $message);
    if ($sessionSaved !== false){
        $address = $sessionSaved['address'].", ".$sessionSaved["city"]." ".$sessionSaved["province"].", ".$sessionSaved["postalCode"];
        if (trim($sessionSaved['address2']) != ""){
            $address = trim($sessionSaved['address2'])." -  ".$address;
        }
        
        $emailTemplate = file_get_contents(dirname(dirname(dirname(__DIR__))).'/StaticPage/emailTemplate.html');
        
        $phone = str_format("(###) ###-####", str_replace("-","",$sessionSaved["phoneNumber"]));
        $ePhone = str_format("(###) ###-####", str_replace("-","",$sessionSaved["emergencyPhone"]));
        $title = "Registration to London Fencing Club";
        $body = '<p><label>Session: </label>&nbsp;'.$sessionNfo['sessionName'].'<br />';
        $body .= '<label>Level: </label>&nbsp;'.ucwords($sessionSaved['level']).'</p><p>';
        $body .= '<label>First Name: </label>&nbsp;'.$sessionSaved['firstName'].'<br />';
        $body .= '<label>Last Name: </label>&nbsp;'.$sessionSaved['lastName'].' <br />';
        $body .= '<label>Date of Birth: </label>&nbsp;'.date('Y-m-d',$sessionSaved['birthDate']).'<br />';
        $body .= '<label>Gender: </label>&nbsp;'.$sessionSaved['gender'].' <br />';
        $body .= '<label>Address: </label>&nbsp;'.$address.'<br />';
        $body .= '<label>Phone Number: </label>&nbsp;'.$phone.'<br />';
        $body .= '<label>Email Address: </label>&nbsp;'.$sessionSaved["email"].'<br />';
        $body .= '<label>Parent/Guardian: </label>&nbsp;'.(trim($sessionSaved["parentName"]) != "" ?$sessionSaved["parentName"]:"N/A").'<br />';
        $body .= '<label>Emergency Contact: </label>&nbsp;'.$sessionSaved["emergencyContact"].'<br />';
        $body .= '<label>Emergency Phone: </label>&nbsp;'.$ePhone.'<br />';
        $body .= '<label>Registration Status: </label>&nbsp;'.((int)$sessionSaved['isRegistered'] == 1?'Registered':'Wait List').'<br />';
        $body .= '<label>Confirmation Number: </label>&nbsp;'.$sessionSaved['registrationKey'].'<br />';
        $body .= '<label>Date Registered: </label>&nbsp;'.$sessionSaved['sysDateCreated'].'</p>';
        if ((int)$sessionSaved['isRegistered'] == 1){
        $body .= '<p>Print out your form to sign by clicking the link or copying it and pasting it into your browser: 
            <a href="http://'.$_SERVER["SERVER_NAME"].'/print-reg/'.$sessionNfo['itemID'].'/'.$sessionSaved['registrationKey'].'">http://'.$_SERVER["SERVER_NAME"].'/print-reg/'.$sessionNfo['itemID'].'/'.$sessionSaved['registrationKey'].'</a>';
        $body .= '<p>&nbsp;</p><p>Next Steps:</p>';
        $body .= '<ol>
        <li>Read the Terms and Conditions listed on the printable registration sheet</li>
        <li>Print this form out, sign it, and bring it with payment to the first class.<br />This form MUST be signed by the participant (if over the age of 18) 
            OR by the Parent/Guardian listed on the form (if the participant is under the age of 18)</li>
        <li>Make cheques payable to <strong>The London Fencing Club</strong> for the value of $'.number_format($sessionNfo['fee'],2).'</li>
    </ol>';
        }
        else{
            $body .= '<p>Thank you for your interest in the London Fencing Club. If a space becomes available, you will be notified by email. If not, we hope you try to register for the next session.</p>';
        }
        $emailBody = str_replace('%SERVERNAME%',$_SERVER['SERVER_NAME'],str_replace('%BODY%',$body,str_replace('%TITLE%',$title,$emailTemplate)));
        $subject = "London Fencing ".ucwords($sessionSaved['level'])." Session Registration";
        $from = "info@londonfencing.ca";
        $admEmail = $db->return_specific_item(false, 'sysStorageTable', 'value', '--', "application='".$sessionSaved['level']."-registration'");
        if ($admEmail == "--"){
            $admEmail = 'info@londonfencing.ca';
            //$admEmail = 'robertsonkaren@rogers.com';
        }
        $mail = new PHPMailer\PHPMailer();
        $mail->IsHTML(true);
        $mail->SetFrom($from);
        $mail->AddAddress($sessionSaved['email']);
        $mail->AddBCC($admEmail);
        $mail->Subject = $subject;
        $mail->Body = $emailBody;
        if ($mail->send()){
            if ((int)$sessionSaved['isRegistered'] == 1){
                print alert_box("Thank you!<br />Please follow the steps below in order to complete your registration", 1);
            }
            else{
                print alert_box("Thank you for your interest in the London Fencing Club. If a space becomes available, you will be notified by email<br />
                If not, we hope you try to register for the next session.", 1);
            }
        }
        else{
            print alert_box("An Email could not be sent.<br />Please use the print button below or contact <a href='mailto:\"info@londonfencing.ca\"'>info@londonfencing.ca</a>", 2);
        }
    }
    else{
        print alert_box("An Error Occured and your Registration information could not be displayed.<br />Please contact <a href='mailto:\"info@londonfencing.ca\"'>info@londonfencing.ca</a>", 2);
    }
}
?>
<div>
<?php
    if ((int)$sessionSaved['isRegistered'] == 1){
?>
    <h3>Next Steps:</h3>
    <ol>
        <li>Read the Terms and Conditions listed on the printable registration sheet</li>
        <li>Print this form out, sign it, and bring it with payment to the first class.<br />This form MUST be signed by the participant (if over the age of 18) 
            OR by the Parent/Guardian listed on the form (if the participant is under the age of 18)</li>
        <li>Make cheques payable to <strong>The London Fencing Club</strong> for the value of $<?php echo number_format($sessionNfo['fee'],2);?></li>
    </ol>
    <p>You will receive an email at the address you provided along with the information you submitted and a link to the printable version of this form</p>
    <p><a href="/print-reg/<?php echo $sessionNfo["itemID"];?>/<?php echo $message;?>" class="btnStyle" target="_blank">Print Form</a></p>
<?php
    }
?>
</div>
