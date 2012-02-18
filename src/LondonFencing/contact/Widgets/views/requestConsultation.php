<?php 

global $message;
$sent = 0;
$body = '';

if (!empty($_POST) && isset($_POST["sub-req-consult"])) {

    $submitted = $_POST;
    unset($submitted["sub-req-consult"]);
        
    if (validate_form($submitted)){
	       
	    $body .= "A Request for Consultation was submitted for the following services:\n\n";
	    $body .= trim($_POST['RQvalALPHServices'])."\n\n";   
	       
    	if (isset($_POST['RQvalALPHMessage']) && !empty($_POST['RQvalALPHMessage'])) { 
    		$body .= "Additional Message:\n".$_POST['RQvalALPHMessage'] . "\n\n";
    	}
    	$body .= "Submitted By:\n".$_POST['RQvalALPHName']."\n";
    	$body .= "Email: ".$_POST['RQvalMAILEmail_Address']."\n";
    	$body .= "Phone: ".$_POST['RQvalPHONPhone_Number'];
    	if (isset($_POST['OPvalNUMBExtension']) && $_POST['OPvalNUMBExtension'] != ''){
    	   $body .= ' ext. ' . make_numeric($_POST['OPvalNUMBExtension']);
        }
        $body .= "\n\n-------\nSent from ".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    		
    	$from = ($_POST['RQvalMAILEmail_Address'] != "") ? $_POST['RQvalMAILEmail_Address'] : "no-reply@royalyorkortho.com";
    	$email = $db->return_specific_item(false, 'sysStorageTable', 'value', '--', "application='request-a-consult'");
    	
    	mail($email, 'Royal York Ortho Consultation Request from: ' . trim($_POST['RQvalALPHName']), $body, 'From: '. $from);
    	
    	$sent = 1;
	}
	
}

?>

	<?php
	    $post = array(
	       "RQvalALPHName"          => "",
	       "RQvalPHONPhone_Number"  => "",
	       "OPvalNUMBExtension"     => "",
	       "RQvalMAILEmail_Address" => "",
	       "RQvalALPHServices"      => "",
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
<form action="<?php print $_SERVER['REQUEST_URI']; ?>" method="post">
	<div>
			<label for="RQvalALPHName">Name</label>
			<input type="text" name="RQvalALPHName" id="RQvalALPHName"  value = "<?php echo $post["RQvalALPHName"];?>" />
	</div>

	<div>
			<label for="RQvalPHONPhone_Number">Phone Number</label>
			<input type="text" name="RQvalPHONPhone_Number" id="RQvalPHONPhone_Number" value = "<?php echo $post["RQvalPHONPhone_Number"];?>"/> ext. 
			<input type="text" name="OPvalNUMBExtension" id="OPvalNUMBExtension" style="width:40px;" value = "<?php echo $post["OPvalNUMBExtension"];?>"/>
	</div>

	<div>
			<label for="RQvalMAILEmail_Address">Email</label>
			<input type="email" name="RQvalMAILEmail_Address" id="RQvalMAILEmail_Address" value = "<?php echo $post["RQvalMAILEmail_Address"];?>"/>
	</div>

	<div>
			<label for="RQvalALPHServices">Services of Interest</label>
            <input type="text" name="RQvalALPHServices" id="RQvalALPHServices" value = "<?php echo $post["RQvalMAILServices"];?>" />
	</div>

	<div>
			<label for="RQvalALPHMessage">Message</label>
			<textarea name="RQvalALPHMessage" id="RQvalALPHMessage" cols="35" rows="10"><?php echo $post["RQvalALPHMessage"];?></textarea>
	</div>

	<div class="submitWrap">
	      <input type="submit" value="Submit" name="sub-req-consult" class="btnStyle blue" />
          <input type="hidden" name="nonce" value="<?php echo Quipp()->config('security.nonce');?>" />
    </div>
</form>