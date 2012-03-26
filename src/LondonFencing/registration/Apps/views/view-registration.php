<?php
require_once dirname(dirname(__DIR__))."/registration.php";

use LondonFencing\registration\Apps as AReg;

$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require $root . '/inc/init.php';
require $root . '/admin/classes/Editor.php';

$meta['title'] = 'Registration Submission Manager';
$meta['title_append'] = ' &bull; Quipp CMS';

$hasPermission = false;
if ($auth->has_permission("canEditReg")){
    $hasPermission = true;
}

if ($hasPermission && isset($_GET['sid']) && is_numeric($_GET['sid'])) {
    $aReg = new AReg\AdminRegister(false,$db);
    $quipp->js['footer'][] = "/src/LondonFencing/registration/assets/js/adminRegistration.js";
    if (!isset($_GET['id'])) { $_GET['id'] = null; }
    $te = new Editor();
    
    //set the primary table name
    $primaryTableName = "tblClassesRegistration";
    
    $provs = array("AB","BC","MB","NB","NL","NS","NT","NU","ON","PE","QC","SK","YT");
    $regStatus = array("1" => "Registered", "0" => "Wait Listed");
    $gender = array("F" => "Female", "M" => "Male");
    
        //editable fields
    $fields[] = array(
        'label'   => "First Name",
        'dbColName'  => "firstName",
        'tooltip'   => "Participant First Name",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
    $fields[] = array(
        'label'   => "Last Name",
        'dbColName'  => 'lastName',
        'tooltip'   => "Participant Last Name",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );
    $fields[] = array(
        'label'   => "Birth Date",
        'dbColName'  => 'birthDate',
        'tooltip'   => "Participant's Date of Birth",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalDATE",
        'dbValue'   => false,
        'stripTags'  => true
    );
    $fields[] = array(
        'label'   => "Gender",
        'dbColName'  => "gender",
        'tooltip'   => "",
        'writeOnce'  => false,
        'widgetHTML' => "",
        'valCode'   => "RQvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );
    $fields[] = array(
        'label'   => "Address",
        'dbColName'  => 'address',
        'tooltip'   => "Participant's Home Address",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );
    $fields[] = array(
        'label'   => "Apt/Unit",
        'dbColName'  => 'address2',
        'tooltip'   => "",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "OPvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
    $fields[] = array(
        'label'   => "City",
        'dbColName'  => "city",
        'tooltip'   => "",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
    $fields[] = array(
        'label'   => "Province",
        'dbColName'  => "province",
        'tooltip'   => "",
        'writeOnce'  => false,
        'widgetHTML' => "",
        'valCode'   => "RQvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );
    $fields[] = array(
        'label'   => "Postal Code",
        'dbColName'  => "postalCode",
        'tooltip'   => false,
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" min=\"1\" />",
        'valCode'   => "RQvalPOST",
        'dbValue'   => false,
        'stripTags'  => true
    );

    $fields[] = array(
        'label'   => "Phone Number",
        'dbColName'  => "phoneNumber",
        'tooltip'   => "eg. 519-555-1212",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalPHON",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
    $fields[] = array(
        'label'   => "Parent/Guardian",
        'dbColName'  => "parentName",
        'tooltip'   => "Parent or Legal Guardian for participants under the age of 18",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "OPvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );

    $fields[] = array(
        'label'   => "Email Address",
        'dbColName'  => "email",
        'tooltip'   => "Contact email address e.g user@domain.com",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalMAIL",
        'dbValue'   => false,
        'stripTags'  => true
    );
   $fields[] = array(
        'label'   => "Emergency Contact",
        'dbColName'  => "emergencyContact",
        'tooltip'   => "The name of the emergency contact person",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );
    $fields[] = array(
        'label'   => "Emergency Phone",
        'dbColName'  => "emergencyPhone",
        'tooltip'   => "The name of the emergency contact phone number e.g 519-555-2323",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalPHON",
        'dbValue'   => false,
        'stripTags'  => true
    );
    $fields[] = array(
        'label'   => "Registration Status",
        'dbColName'  => "isRegistered",
        'tooltip'   => "The name of the emergency contact phone number e.g 519-555-2323",
        'writeOnce'  => false,
        'widgetHTML' => "",
        'valCode'   => "RQvalNUMB",
        'dbValue'   => false,
        'stripTags'  => true
    );
    $fields[] = array(
        'label'   => "Payment Date",
        'dbColName'  => "paymentDate",
        'tooltip'   => "",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "OPvalDATE",
        'dbValue'   => false,
        'stripTags'  => true
    );
    $fields[] = array(
        'label'   => "Form Submitted",
        'dbColName'  => "formDate",
        'tooltip'   => "",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "OPvalDATE",
        'dbValue'   => false,
        'stripTags'  => true
    );
    if (!isset($_POST['dbaction'])) {
        $_POST['dbaction'] = null;

        if (isset($_GET['action'])) {
            $_POST['dbaction'] = $_GET['action'];
        }
    }
    if ((!empty($_POST) && validate_form($_POST)) || $_POST['dbaction'] == 'delete') {

        //yell($_POST);
        $maxWaitlist = $db->fetch_assoc($db->query("SELECT (MAX(waitlist) + 1) AS wl FROM tblClassesRegistration WHERE `sessionID` = ".$db->escape($_GET['sid'])));
        $newWL =  (isset($maxWaitlist['wl']))? (int)$maxWaitlist['wl']:1;
        switch ($_POST['dbaction']) {
        case "insert":
            
            $countQry = $db->fetch_assoc($db->query("SELECT (COUNT(itemID) + 1) AS rk FROM tblClassesRegistration WHERE `sessionID` = ".$db->escape($_GET['sid'])));
            $newCount = (isset($countQry['rk']))? $countQry['rk']:"1";

            //this insert query will work for most single table interactions, you may need to cusomize your own

            $fieldColNames  = '';
            $fieldColValues = '';

            foreach ($fields as $dbField) {
                if ($dbField['dbColName'] != false){
                    $requestFieldID = $dbField['valCode'] . str_replace(" ", "_", $dbField['label']);
                        if ($dbField['dbColName'] == 'sysStatus') {

                            if (isset($_POST[$requestFieldID])) {
                                $fieldColValues .= "'active',";
                            } else {
                                $fieldColValues .= "'inactive',";
                            }
                            $fieldColNames .= "" . $dbField['dbColName'] .",";
                        }
                        else if ($dbField['dbColName'] == 'birthDate') {
                            $fieldColValues .= strtotime($_POST[$requestFieldID]).",";
                            $fieldColNames .= "" . $dbField['dbColName'] . ", ";
                        }
                        else if ($dbField['dbColName'] == 'paymentDate' || $dbField['dbColName'] == 'formDate') {
                            if (trim($_POST[$requestFieldID]) != ""){
                                $fieldColValues .= strtotime(trim($_POST[$requestFieldID])).",";
                                $fieldColNames .= "" . $dbField['dbColName'] . ",";
                            }
                        }
                        else if ($dbField['dbColName'] == 'isRegistered') {
                            $fieldColValues .= $db->escape($_POST[$requestFieldID], $dbField['stripTags']).",";
                            $fieldColNames .= "" . $dbField['dbColName'] . ", ";
                            $fieldColValues .= (trim($_POST[$requestFieldID]) == "0") ?$newWL ."," :"0," ;
                            $fieldColNames .= "waitlist,";
                        }
                        else if (isset($_POST[$requestFieldID])){
                                $fieldColValues .= "'" . $db->escape($_POST[$requestFieldID], $dbField['stripTags']) . "',";
                                $fieldColNames .= "" . $dbField['dbColName'] . ",";
                        }
                    }
   
          }

            //trim the extra comma off the end of both of the above vars
            $fieldColNames = rtrim($fieldColNames,",");
            $fieldColValues = rtrim($fieldColValues,",");
            
            $regKey = strtoupper(substr(str_replace("'","",$_POST["RQvalALPHLast_Name"]),0,2))."-".str_pad($db->escape($_GET['sid'],true),4,'0',STR_PAD_LEFT)."-".$newCount;
             $qry = sprintf("INSERT INTO %s (%s, sysDateCreated, sysOpen, sysStatus, membershipType, registrationKey, sessionID) VALUES (%s, NOW(),  '1', 'active','basic','%s', '%d')",
                    (string) $primaryTableName,
                    (string) $fieldColNames,
                    (string) $fieldColValues,
                     $regKey,
                     $db->escape($_GET['sid'],true)
             );
             $res = $db->query($qry);
            
             if ($db->affected_rows($res) == 1){
                    header('Location:/admin/apps/registration/view-registration?sid='.(int)$_GET['sid'].'&Insert=true'); 
             }
             else{
                    echo "Insert did not work";
             }
            
            break;


        case "update":

            //this default update query will work for most single table interactions, you may need to cusomize your own
            $fieldColNames  = '';
            $fieldColValues = '';

            foreach ($fields as $dbField) {
                if ($dbField['dbColName'] != false) {
                    
                    $requestFieldID = $dbField['valCode'] . str_replace(" ", "_", $dbField['label']);
                        
                    if ($dbField['dbColName'] == 'sysStatus') {

                        if (isset($_POST[$requestFieldID])) {
                                $fieldColValue = "'active',";
                        } else {
                                $fieldColValue = "'inactive',";
                        }

                        $fieldColNames .= "" . $dbField['dbColName'] . " = " . $fieldColValue;
                    }
                    else if ($dbField['dbColName'] == 'birthDate') {
                        $fieldColValue = strtotime($_POST[$requestFieldID]).",";
                        $fieldColNames .= "" . $dbField['dbColName'] . " = " . $fieldColValue;
                    }
                    else if ($dbField['dbColName'] == 'paymentDate' && trim($_POST[$requestFieldID]) != "") {
                        $fieldColValue = strtotime(trim($_POST[$requestFieldID])).",";
                        $fieldColNames .= "" . $dbField['dbColName'] . " = " . $fieldColValue;
                    }
                    else if ($dbField['dbColName'] == 'formDate' && trim($_POST[$requestFieldID]) != "") {
                        $fieldColValue = strtotime(trim($_POST[$requestFieldID])).",";
                        $fieldColNames .= "" . $dbField['dbColName'] . " = " . $fieldColValue;
                    }
                    else if ($dbField['dbColName'] == 'isRegistered') {
                        $fieldColValue = $_POST[$requestFieldID].",";
                        $fieldColNames .= "" . $dbField['dbColName'] . " = " . $fieldColValue;
                        $fieldColValue2 = "0,";
                        if (trim($_POST[$requestFieldID]) == "0"){
                            $fieldColValue2 = $newWL .",";
                        }
                        $fieldColNames .= "waitlist = " . $fieldColValue2;
                    }
                    else if ($dbField['dbColName'] == 'province'){
                            $fieldColValue = "'".$_POST[$requestFieldID]."',";
                            $fieldColNames .= "" . $dbField['dbColName'] ." = ". $fieldColValue;
                    }
                    else if (isset($_POST[$requestFieldID])){                            
                            $fieldColValue = "'" . $db->escape($_POST[$requestFieldID], $dbField['stripTags']) . "',";
                            $fieldColNames .= "" . $dbField['dbColName'] . " = " . $fieldColValue;
                    }

                }
            }
            
            //trim the extra comma off the end of the above var
            $fieldColNames = substr($fieldColNames, 0, strlen($fieldColNames) - 1);

            $qry = sprintf("UPDATE %s SET %s WHERE itemID = '%s'", 
                (string) $primaryTableName, 
                (string) $fieldColNames,
                (int)$_POST['id']);
                  
            $res = $db->query($qry);

            if ($db->affected_rows($res) == 1 || $db->error() === false){
                header('Location:/admin/apps/registration/view-registration?sid='.$_GET['sid'].'&Update=true'); 
            }
            else{
                echo "Update did not work";
            }

            break;

        case "delete":

            //this delete query will work for most single table interactions, you may need to cusomize your own

            $db->query(sprintf("UPDATE %s SET `sysStatus` = 'inactive', `sysOpen` = '0' WHERE itemID = %d", 
                (string) $primaryTableName, 
                (int)$db->escape($_GET['id'], true)
            ));
            
            header('Location:/admin/apps/registration/view-registration?sid='.$_GET['sid']);
            break;
        }
    } else {
        $_GET['view'] = 'edit';
    }
    
    $sessionName = $db->return_specific_item((int)$_GET['sid'], "tblClasses", "sessionName"); 
    
    include $root. "/admin/templates/header.php";
?>
<h1>Registration Manager</h1>
<p>This allows the ability to review submissions for specific sessions, add/modify and notify specific participants.</p>

<div class="boxStyle">
	<div class="boxStyleContent">
		<div class="boxStyleHeading">
			<h2><?php echo $sessionName;?> - Edit</h2>
			<div class="boxStyleHeadingRight">
				<?php print "<input class='btnStyle blue' type=\"button\" name=\"newItem\" id=\"newItem\" onclick=\"javascript:window.location.href='/admin/apps/registration/view-registration?sid=".$_GET['sid']."&view=edit';\" value=\"New\" />"; ?>
			</div>
		</div>
		<div class="clearfix">&nbsp;</div>
		<div id="template">
<?php
    //display logic

    //view = view state, these standard views will do for most single table interactions, you may need to replace with your own
    if (!isset($_GET['view'])) { $_GET['view'] = null; }

    switch ($_GET['view']) {
    case "edit": //show an editor for a row (existing or new)

        //determine if we are editing an existing record, otherwise this will be a 'new'

        $dbaction = "insert";

        $_GET['id'] = intval($_GET['id'], 10);
        
        $sessionID = (int)$_GET['sid'];
        $isWaitlisted = false;

        if (is_numeric($_GET['id'])) { //if an ID is provided, we assume this is an edit and try to fetch that row from the single table


            $qry = sprintf("SELECT tp.* FROM $primaryTableName AS tp WHERE tp.`itemID` = '%d' AND tp.`sessionID` = '%d' AND tp.`sysOpen` = '1'",
	(int)$_GET['id'],
                    $sessionID
            );
            $res = $db->query($qry);

            if ($db->valid($res)) {
                $fieldValue = $db->fetch_assoc($res);
                foreach ($fields as &$itemField) {
                    //if (is_string($itemField['dbColName'])) {
                        $itemField['dbValue'] = $fieldValue[$itemField['dbColName']];
                    //}
                }

                $dbaction = "update";
                $isWaitlisted = ((int)$fieldValue['isRegistered'] === 0)?true:false;
            }


        } else {
            //yell($_GET);
        }


        if ($message != '') {
            print $message;
        }

        $formBuffer = "<form enctype=\"multipart/form-data\" name=\"tableEditorForm\" id=\"tableEditorForm\" method=\"post\" action=\"/admin/apps/registration/view-registration?" . $_SERVER['QUERY_STRING'] .  "\">
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
                else if ($field['dbColName'] == "province"){
                    $field['dbValue'] = (isset($_POST[$newFieldID]) && $message != '')? $_POST[$newFieldID]: 'ON';
                    $field['widgetHTML'] = '<select name="'.$newFieldID.'" id="'.$newFieldID.'">';
                    foreach($provs as $prov){
                        $field['widgetHTML'] .= '<option value="'.$prov.'"'.($field['dbValue'] == $prov ? 'selected="selected"':'').'>'.$prov.($field['dbValue'] == $prov ? '*':'').'</option>';
                    }
                    $field['widgetHTML'] .= '</select>';
                }
                else if ($field['dbColName'] == "gender"){
                    $field['dbValue'] = (isset($_POST[$newFieldID]) && $message != '')? $_POST[$newFieldID]: $field['dbValue'];
                    $field['widgetHTML'] = '<select name="'.$newFieldID.'" id="'.$newFieldID.'">';
                    foreach($gender as $gAbbr => $sex){
                        $field['widgetHTML'] .= '<option value="'.$gAbbr.'"'.($field['dbValue'] == $gAbbr ? 'selected="selected"':'').'>'.$sex.($field['dbValue'] == $gAbbr ? '*':'').'</option>';
                    }
                    $field['widgetHTML'] .= '</select>';
                }
                else if ($field['dbColName'] == 'isRegistered'){
                    $field['widgetHTML'] = '<select name="'.$newFieldID.'" id="'.$newFieldID.'">';
                    foreach($regStatus as $regOpt => $regVal){
                        $selected = ($regOpt == $field['dbValue'])? ' selected = "selected"':'';
                        $field['widgetHTML'] .= '<option value="'.$regOpt.'"'.$selected.'>'.$regVal.($field['dbValue'] == $regOpt ? '*':'').'</option>';
                    }
                    $field['widgetHTML'] .= '</select>';
                }
                else if (stristr($field['dbColName'], "date") !== false){
                    if (isset($_POST[$newFieldID]) && $message != '') {
                        $field['dbValue'] = $_POST[$newFieldID];
                    }
                    $field['dbValue'] = ($field['dbValue'] != "" && $field['dbValue'] != 0)? date('Y-m-d',$field['dbValue']):'';
                    $field['widgetHTML'] = str_replace("FIELD_VALUE", $field['dbValue'], $field['widgetHTML']);
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

        //temp
        $id = null;
        $formAction = null;
        //end temp

        $formBuffer .= "<tr><td colspan='2'>
            <input type=\"hidden\" name=\"nonce\" value=\"".Quipp()->config('security.nonce')."\" />
                <input type=\"hidden\" name=\"dbaction\" id=\"dbaction\" value=\"$dbaction\" />";

        if ($dbaction == "update") { //add in the id to pass back for queries if this is an edit/update form
            $formBuffer .= "<input type=\"hidden\" name=\"id\" id=\"id\" value=\"".$_GET['id']."\" />";
        }

        $formBuffer .= "</td></tr>";
        $formBuffer .= "</table>";
        $formBuffer .= "<div class=\"clearfix\" style=\"margin-top: 10px; height:10px; border-top: 1px dotted #B1B1B1;\">&nbsp;</div>";
        $formBuffer .= "<input class='btnStyle grey' type=\"button\" name=\"cancelUserForm\" id=\"cancelUserForm\" onclick=\"javascript:window.location.href='" . $_SERVER['PHP_SELF'] . "?sid=".$_GET['sid']."';\" value=\"Cancel\" />
		<input class='btnStyle green' type=\"submit\" name=\"submitUserForm\" id=\"submitUserForm\" value=\"Save Changes\" />";
        $formBuffer .= "</form>";
        //print the form
        print $formBuffer;
        break;
    default: //(list)
       
        //list table query:
        
        $listqry = sprintf("SELECT cr.`itemID`, concat(cr.`lastName`, ', ' ,cr.`firstName`) AS name, cr.`email`, cr.`formDate`, 
                UNIX_TIMESTAMP(cr.`sysDateCreated`) as dateReg, cr.`isRegistered`, cr.`waitlist`, cr.`paymentDate`, cr.`registrationKey` 
                FROM $primaryTableName AS cr 
                WHERE cr.`sessionID` = %d 
                AND (cr.`isRegistered` > 0 || cr.`waitlist` > 0) 
                AND cr.`sysStatus` = 'active' AND cr.`sysOpen` = '1'
                ORDER BY cr.`itemID` ASC, cr.`isRegistered` DESC, cr.`waitlist` ASC", 
          (int)$db->escape($_GET['sid'],true)
                );
        
        $resqry = $db->query($listqry);
        if (is_object($resqry) && $resqry->num_rows > 0){
            //list table field titles
            $titles[0] = "Name";
            $titles[1] = "Registration Number";
            $titles[2] = "Email Address";
            $titles[3] = "Date Registered";
            $titles[4] = "Payment Date";
            $titles[5] = "Form Submitted";
            
            while ($rs = $db->fetch_assoc($resqry)){
                 if ((int)$rs["isRegistered"] == 1){
                     $registered[] = $rs;
                 }
                 else if ((int)$rs["waitlist"] > 0){
                     $waitlist[(int)$rs["waitlist"]] = $rs;
                 }
                
            }
            echo '<form name="frmSendEmail" action="/admin/apps/notificationManager/emailer" method="post" enctype="multipart/form-data">';
            echo '<table id="adminTableList_reg" class="adminTableList tablesorter" width="100%" cellpadding="5" cellspacing="0" border="1">';
            echo '<thead><tr><th>'.$titles[0].'</th><th>'.$titles[1].'</th><th>'.$titles[2].'</th><th>'.$titles[3].'</th><th>'.$titles[4].'</th><th>'.$titles[5].'</th><th>Status</th>
                <th>Email<input type="checkbox" id="emailAll" name="emailAll" value="all" /></th><th>&nbsp;</th><th>&nbsp;</th></tr></thead>';
            echo '<tbody>';
            foreach($registered as $dt){
                $paymentDate = (trim($dt["paymentDate"]) != '' && $dt["paymentDate"] > 0)?date('Y-m-d',$dt["paymentDate"]):"Due";
                $formDate = (trim($dt["formDate"]) != '' && $dt["formDate"] > 0)?date('Y-m-d',$dt["formDate"]):"Due";
                echo '<tr><td>'.$dt['name'].'</td><td>'.$dt["registrationKey"].'</td><td>'.$dt["email"].'</td><td>'.date('Y-m-d',$dt["dateReg"]).'</td><td>'.$paymentDate.'</td><td>'.$formDate.'</td><td>Registered</td>';
                echo '<td style="width:70px;"><input type="checkbox" name="eList[]" id="eList_'.trim($dt['itemID']).'" value="'.trim($dt['itemID']).'" /></td>';
                echo '<td style="width:40px;"><input class="btnStyle red noPad" id="btnDelete_'.$dt['itemID'].'" type="button" onclick="javascript:confirmDelete(\'?sid='.$_GET['sid'].'&action=delete&amp;id='.$dt['itemID'].'\');" value="Delete"></td>';
                echo '<td style="width:40px;"><input class="btnStyle blue noPad" id="btnEdit_'.$dt['itemID'].'" type="button" onclick="javascript:window.location=\'?sid='.$_GET['sid'].'&view=edit&amp;id='.$dt['itemID'].'\';" value="Edit"></td></tr>';
            }
            if (isset($waitlist)){
                ksort($waitlist);
                echo '</tbody><tbody><tr><td colspan="10">&nbsp;</td></tr>';
                $w = 0;
                foreach ($waitlist as $dt){
                    echo '<tr><td>'.$dt['name'].'</td><td>'.$dt["registrationKey"].'</td><td>'.$dt["email"].'</td><td>'.date('Y-m-d',$dt["dateReg"]).'</td><td>N/A</td><td>Waitlist ('.(++$w).')</td>';
                    echo '<td style="width:50px;"><input type="checkbox" name="eList[]" id="eList_'.trim($dt['itemID']).'" value="'.trim($dt['itemID']).'" /></td>';
                    echo '<td style="width:50px;"><input class="btnStyle red noPad" id="btnDelete_'.$dt['itemID'].'" type="button" onclick="javascript:confirmDelete(\'?sid='.$_GET['sid'].'&action=delete&amp;id='.$dt['itemID'].'\');" value="Delete"></td>';
                    echo '<td style="width:50px;"><input class="btnStyle blue noPad" id="btnEdit_'.$dt['itemID'].'" type="button" onclick="javascript:window.location=\'?sid='.$_GET['sid'].'&view=edit&amp;id='.$dt['itemID'].'\';" value="Edit"></td></tr>';
                }
            }
            echo '</tbody>
            <tbody>
            <tr><td colspan="10">
            <input  style="float:right" class="btnStyle green noPad" id="btnPrint" type="button" onclick="javascript:window.open(\'/admin/apps/registration/print-reg-list?sid='.$_GET['sid'].'\');" value="Print">
            <input  style="float:right" class="btnStyle blue noPad" id="btnSelect" type="submit" value="Send Email">
            <input type="hidden" name="nonce" value="'.Quipp()->config('security.nonce').'" />
            <input type="hidden" name="etype" value="class-reg" />
            </td>
                </tr>
            </tbody>
            </table>';
            echo '</form>';
        }
        else{
            echo 'no data present';
        }
        //to pass more advanced controls, you'll need to create your own $fields array and pass it directly to $te->display_editor_list($fields);
        break;
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
    $auth->boot_em_out();
}