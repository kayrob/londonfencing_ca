<?php
if (isset($_POST["PHPSESSID"])) {
	session_id($_POST["PHPSESSID"]);
}
$postUser = (isset($_POST["USER"]) && (int)$_POST["USER"] > 0)?(int)$_POST["USER"]:1;

session_start();
$_SESSION["userID"] = $postUser;
if (!isset($_SESSION["myKey"]) && isset($_POST["AKEY"]) && preg_match("%[A-Fa-f0-9]{32}%",$_POST["AKEY"],$matchA)){
    $_SESSION["myKey"] = trim($_POST["AKEY"]);
}

require '../../../../includes/init.php';

if (!isset($m)) {
	require_once "../../../apps/media/Media.php";
	$m = new Media();
}

//brendan@resolutionim.com (Jun 2011)

	/* Note: This thumbnail creation script requires the GD PHP Extension.  
		If GD is not installed correctly PHP does not render this page correctly
		and SWFUpload will get "stuck" never calling uploadSuccess or uploadError
	 */

	// Get the session Id passed from SWFUpload. We have to do this to work-around the Flash Player Cookie Bug
	/*

*/
	//ini_set("html_errors", "0");

	// Check the upload
	if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
		echo "ERROR:invalid upload";
		exit(0);
	}

/*
ob_start();
echo "\nHERE IS CHRIS' POST\n";
print_r($_POST);
echo "\nAttempting filter:\n";
//echo filter_input(INPUT_POST, 'tags', 
$contents = ob_get_contents();
ob_end_clean();

    yell($contents);
/**/

	
	$ALLOWED_MIME_TYPES = array(
			'application/octet-stream' => 'jpg',
			'image/jpeg'   => 'jpg',
			'image/pjpeg'  => 'jpg',
			'image/png'    => 'png',
			'image/x-png'  => 'png'
		);
		
		$thumbnails = array (
			'med' 	=> array(
				'l' 	   => 120,
				'w' 	   => 120,
				'adaptive' => true
			),
			'large' => array(
				'l' 	   => 800, 
				'w' 	   => 800,
				'adaptive' => false
			)
		);
		
	$_FILES['RQvalALPHFile'] = $_FILES["Filedata"];
	

	$photo['RQvalALPHFile'] = upload_file('RQvalALPHFile', QUIPP_UPLOADS . '/media/', $ALLOWED_MIME_TYPES, $thumbnails);
	

	$idOfNewMedia = $m->create_default_media_record($photo['RQvalALPHFile'], $postUser, json_decode($_POST['tags'], true));

	
	//$_SESSION["file_info"][$file_id] = $imagevariable;

	echo "FILEID:" . $idOfNewMedia;	// Return the file id to the script
	



$db->close();
?>