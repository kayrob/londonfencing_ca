<?php 
global $message;
$sent = 0;
$body = '';

if (!empty($_POST) && isset($_POST["sub-contact-us"]) && validate_form($_POST)) {
	
	if (isset($_POST['RQvalALPHMessage']) && !empty($_POST['RQvalALPHMessage'])) { 
		$body .= $_POST['RQvalALPHMessage'] . "\n\n";
	}
	$body .= $_POST['RQvalALPHName']."\n";
	$body .= "Email: ".$_POST['RQvalMAILEmail_Address']."\n";
	$body .= "Phone: ".$_POST['OPvalPHONPhone_Number'];
	if (isset($_POST['OPvalNUMBExtension']) && $_POST['OPvalNUMBExtension'] != ''){
	   $body .= ' ext. ' . make_numeric($_POST['OPvalNUMBExtension']);
        }
        $body .= "\n\n-------\nSent from ".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		
	$from = ($_POST['RQvalMAILEmail_Address'] != "") ? $_POST['RQvalMAILEmail_Address'] : "info@londonfencing.ca";
	//$email = $db->return_specific_item(false, 'sysStorageTable', 'value', '--', "application='contact-us'");
	$email = Quipp()->config('mailer.from_email');
        $mail = new PHPMailer\PHPMailer();
        $mail->IsHTML(false);
        $mail->SetFrom($from);
        $mail->AddAddress($email);
        $mail->Subject = 'London Fencing Club contact from: ' . stripslashes(trim($_POST['RQvalALPHName']));
        $mail->Body = stripslashes($body);
	if ($mail->Send()){
            $sent = 1;
        }
	else{
            $message = $mail->ErrorInfo;
        }
}
$post = array(
   "RQvalALPHName"          => "",
   "OPvalPHONPhone_Number"  => "",
   "RQvalMAILEmail_Address" => "",
   "RQvalALPHMessage"       => "",
);
if($sent == 1){
    print alert_box("<strong>Thank You!</strong> Your message was sent.", 1);
}else if (isset($message) && $message != '') {
    print alert_box($message, 2);
    foreach ($_POST as $key => $value){
            $post[$key] = $value;
    }
}

?>
<h3>Please complete all fields marked with *</h3>
<form action="<?php print $_SERVER['REQUEST_URI']; ?>" method="post">
    <div>
	<label for="RQvalALPHName" class="req">Name*</label>
        <input type="text" name="RQvalALPHName" id="RQvalALPHName"  value = "<?php echo $post["RQvalALPHName"];?>" />
    </div>

    <div>
	<label for="OPvalPHONPhone_Number" class="req">Phone Number</label>
        <input type="text" name="OPvalPHONPhone_Number" id="OPvalPHONPhone_Number" value = "<?php echo $post["OPvalPHONPhone_Number"];?>"/>
    </div>

    <div>
        <label for="RQvalMAILEmail_Address" class="req">Email*</label>
        <input type="email" name="RQvalMAILEmail_Address" id="RQvalMAILEmail_Address" value = "<?php echo $post["RQvalMAILEmail_Address"];?>"/>
    </div>

    <div>
	<label for="RQvalALPHMessage" class="req">Message*</label>
        <textarea name="RQvalALPHMessage" id="RQvalALPHMessage" cols="35" rows="10"><?php echo $post["RQvalALPHMessage"];?></textarea>
    </div>

    <div class="submitWrap">
        <input type="submit" value="Submit" name="sub-contact-us" class="btnStyle" />
        <input type="hidden" name="nonce" value="<?php echo Quipp()->config('security.nonce');?>" />
    </div>
</form>
