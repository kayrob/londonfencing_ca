<?php 
if ($this INSTANCEOF Quipp){

$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
global $message;
$sent = 0;

if (!empty($_POST) && isset($_POST["sub-sign-up"]) && validate_form($_POST)) {
	require_once($root."/vendors/mailchimp/MCAPI.class.php");
    require_once(dirname(dirname(__DIR__)) . "/assets/MCconfig.php");
    $mc = new MCAPI($mcAPI);
    $lists = $mc->lists(array("list_name"=>"Royal York Ortho Newsletter"));
    
    if (isset($lists["total"]) && $lists["total"] == 1){
        $mcLID = $lists["data"][0]["id"];
    }
    if (isset($mcLID)){
        if ($mc->listSubscribe($mcLID, $db->escape(trim($_POST["RQvalMAILEmail_Address"]),true), array(
            'FNAME'     => $db->escape(trim($_POST["RQvalALPHFirstName"]),true),
            'LNAME'     => $db->escape(trim($_POST["RQvalALPHLastName"]),true)
        
        ), 'html', false)){
            $sent = 1;
        }
        else{
            $message = "Error: Unable to register at this time";
            if ($mc->errorCode == 214){
                $message = "You are already subscribed to this newsletter";
            }
        }
    }
    else{
        $message = "Error: Unable to subscribe at this time";
    }
}

$post = array(
   "RQvalALPHFirstName"     => "",
   "RQvalALPHLastName"      => "",
   "RQvalMAILEmail_Address" => ""
);
if($sent == 1){
	print alert_box("<strong>Success!</strong> You are now subscribed to the newsletter.", 1);
}else if (isset($message) && $message != '') {
	print alert_box($message, 2);
	foreach ($_POST as $key => $value){
	     $post[$key] = $value;
	}
}
	
?>
<form action="<?php print $_SERVER['REQUEST_URI']; ?>" method="post">
	<div>
			<label for="RQvalALPHFirstName" class="req">First Name</label>
			<input type="text" name="RQvalALPHFirstName" id="RQvalALPHFirstName"  value = "<?php echo $post["RQvalALPHFirstName"];?>" />
	</div>
    <div>
			<label for="RQvalALPHLastName" class="req">Last Name</label>
			<input type="text" name="RQvalALPHLastName" id="RQvalALPHLastName"  value = "<?php echo $post["RQvalALPHLastName"];?>" />
	</div>
    <div>
			<label for="RQvalMAILEmail_Address" class="req">Email Address</label>
			<input type="email" name="RQvalMAILEmail_Address" id="RQvalMAILEmail_Address" value = "<?php echo $post["RQvalMAILEmail_Address"];?>"/>
	</div>
    <div class="submitWrap">
        <input type="submit" value="Sign Up" name="sub-sign-up" class="btnStyle blue" />
        <input type="hidden" name="nonce" value="<?php echo Quipp()->config('security.nonce');?>" />
    </div>
</form>
<?php
}