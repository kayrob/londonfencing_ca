<?php
require_once dirname(dirname(dirname(__DIR__))) ."/calendar/calendar.php";
require_once dirname(dirname(__DIR__))."/registration.php";

use LondonFencing\calendar\Apps as aCal;
use LondonFencing\registration\Apps as AReg;

$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require $root . '/inc/init.php';
require $root . '/admin/classes/Editor.php';

$meta['title'] = 'Beginner Registration Manager';
$meta['title_append'] = ' &bull; Quipp CMS';

$hasPermission = false;
if ($auth->has_permission("canEditReg")){
    $hasPermission = true;
}

if ($hasPermission) {
    
    $cal = new aCal\adminCalendar($db);
    $aReg = new AReg\AdminRegister($cal,$db);
    
    $quipp->js['footer'][] = "/src/LondonFencing/registration/assets/js/adminRegistration.js";
    
    $canApprove = $auth->has_permission('approvepage');
    
    if (!isset($_GET['id'])) { $_GET['id'] = null; }
    $te = new Editor();
    
    //set the primary table name
    $primaryTableName = "tblClasses";

    //editable fields
    $fields[] = array(
        'label'   => "Session Name",
        'dbColName'  => "sessionName",
        'tooltip'   => "A Unique Name for this Session ",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
    $fields[] = array(
        'label'   => "Start Date",
        'dbColName'  => 'startDate',
        'tooltip'   => "Date of the first class in the session",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalDATE",
        'dbValue'   => false,
        'stripTags'  => true
    );
    $fields[] = array(
        'label'   => "Start Time",
        'dbColName'  => 'startTime',
        'tooltip'   => "Time a single class starts",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalTIME",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
    $fields[] = array(
        'label'   => "End Date",
        'dbColName'  => 'endDate',
        'tooltip'   => "Date of the last class in the session",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalDATE",
        'dbValue'   => false,
        'stripTags'  => true
    );
    $fields[] = array(
        'label'   => "End Time",
        'dbColName'  => 'endTime',
        'tooltip'   => "Time a single class ends",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalTIME",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
    $fields[] = array(
        'label'   => "Fee",
        'dbColName'  => "fee",
        'tooltip'   => "eg: 50",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"number\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalNUMB",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
    $fields[] = array(
        'label'   => "Class Size Limit",
        'dbColName'  => "regMax",
        'tooltip'   => "The maximum number of students allowed",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"number\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" min=\"1\" />",
        'valCode'   => "RQvalNUMB",
        'dbValue'   => false,
        'stripTags'  => true
    );
    $fields[] = array(
        'label'   => "Minimum Age",
        'dbColName'  => "ageMin",
        'tooltip'   => false,
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"number\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" min=\"1\" />",
        'valCode'   => "RQvalNUMB",
        'dbValue'   => false,
        'stripTags'  => true
    );

    $fields[] = array(
        'label'   => "Coach",
        'dbColName'  => "coach",
        'tooltip'   => "If empty will be displayed as 'TBA'",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "OPvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
    $fields[] = array(
        'label'   => "Location",
        'dbColName'  => "location",
        'tooltip'   => "Where the classes will be held",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "OPvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );

    $fields[] = array(
        'label'   => "Registration Open",
        'dbColName'  => "regOpen",
        'tooltip'   => "The first date participants can register",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalDATE",
        'dbValue'   => false,
        'stripTags'  => true
    );
   $fields[] = array(
        'label'   => "Registration Close",
        'dbColName'  => "regClose",
        'tooltip'   => "The last date participants can register",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalDATE",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
    $fields[] = array(
        'label'   => "Active",
        'dbColName'  => "sysStatus",
        'tooltip'   => 'Only active sessions that are open for registration will be available for sign-up',
        'writeOnce'  => false,
        'widgetHTML' => '<input type="checkbox" id="FIELD_ID" name="FIELD_ID" value="active" FIELD_VALUE />',
        'valCode'   => "",
        'dbValue'   => false,
        'stripTags'  => true
    );

    //dbaction = database interactivity, these standard queries will do for most single table interactions, you may need to replace with your own
    if (!isset($_POST['dbaction'])) {
        $_POST['dbaction'] = null;

        if (isset($_GET['action'])) {
            $_POST['dbaction'] = $_GET['action'];
        }
    }
    if ((!empty($_POST) && validate_form($_POST)) || $_POST['dbaction'] == 'delete') {

        //yell($_POST);

        switch ($_POST['dbaction']) {
        case "insert":

            //this insert query will work for most single table interactions, you may need to cusomize your own

            //the following loop populates 2 strings with name value pairs
            //eg.  $fieldColNames = 'articleTitle','contentBody',
            //eg.  $fieldColValues = 'Test Article Title', 'This is my test article body copy',
            //yell($_GET);
            //yell($fields);
            $fieldColNames  = '';
            $fieldColValues = '';
            $eventStart = "";
            $eventEnd = "";
            $eventStartTime = '';
            $eventEndTime = '';
            $calendarID = $db->escape($_POST["calendarID"]);
            foreach ($fields as $dbField) {
                if ($dbField['dbColName'] != false){
                    $requestFieldID = $dbField['valCode'] . str_replace(" ", "_", $dbField['label']);
                    if (strstr($dbField['dbColName'],'start') === false && strstr($dbField['dbColName'],'end') === false) {

                        if ($dbField['dbColName'] == 'sysStatus') {

                            if (isset($_POST[$requestFieldID])) {
                                $fieldColValues .= "'active',";
                            } else {
                                $fieldColValues .= "'inactive',";
                            }
                            $fieldColNames .= "" . $dbField['dbColName'] .",";
                        }
                        else if ($dbField['dbColName'] == 'regOpen' || $dbField['dbColName'] == 'regClose'){
                            $fieldColNames .= "" . $dbField['dbColName'] .",";
                            $fieldColValues .= strtotime($_POST[$requestFieldID]).",";
                        }
                        else if (isset($_POST[$requestFieldID])){
                                $fieldColValues .= "'" . $db->escape($_POST[$requestFieldID], $dbField['stripTags']) . "',";
                                $fieldColNames .= "" . $dbField['dbColName'] . ",";
                        }
                    }
                    else if ($dbField['dbColName'] == 'startDate'){
                            $eventStart = $db->escape($_POST[$requestFieldID], $dbField['stripTags']);
                    }
                   else if ($dbField['dbColName'] == "startTime"){
                            $eventStartTime = $db->escape($_POST[$requestFieldID], $dbField['stripTags']);
                    }
                    else if ($dbField['dbColName'] == 'endDate'){
                            $eventEnd = $db->escape($_POST[$requestFieldID], $dbField['stripTags']);
                    }
                    else if ($dbField['dbColName'] == 'endTime'){
                            $eventEndTime = $db->escape($_POST[$requestFieldID], $dbField['stripTags']);
                    }   
              }
          }

            //trim the extra comma off the end of both of the above vars
            $fieldColNames = rtrim($fieldColNames,",");
            $fieldColValues = rtrim($fieldColValues,",");
            
            if ($eventStart != "" && $eventEnd != "" && isset($_POST["calendarID"])){
                $eventID = $aReg->create_new_session_event($calendarID, $eventStart, $eventEnd, $eventStartTime, $eventEndTime, $_POST['RQvalALPHSession_Name'],$_POST['OPvalALPHLocation']);
            }
            
            if ($eventID !== false){
                $fieldColNames .= ",eventID";
                $fieldColValues .= ','.$eventID;
                $qry = sprintf("INSERT INTO %s (%s, sysDateCreated, sysOpen, level) VALUES (%s, NOW(),  '1', 'beginner')",
                    (string) $primaryTableName,
                    (string) $fieldColNames,
                    (string) $fieldColValues
                );
                $res = $db->query($qry);
            
                if ($db->affected_rows($res) == 1){
                    header('Location:/admin/apps/registration/beginner-registration?Insert=true'); 
                }
                else{
                    echo "Insert did not work";
                }
            }
            else{
                echo 'Inset did not work: could not add dates';
            }
            
            break;


        case "update":


            //this default update query will work for most single table interactions, you may need to cusomize your own
            $fieldColNames  = '';
            $fieldColValues = '';
            $eventStart = "";
            $eventEnd = "";
            $eventStartTime = '';
            $eventEndTime = '';
            $calendarID = $db->escape($_POST["calendarID"]);
            $eventID = $db->escape($_POST["eventID"]);
            foreach ($fields as $dbField) {
                if ($dbField['dbColName'] != false) {
                    $requestFieldID = $dbField['valCode'] . str_replace(" ", "_", $dbField['label']);
                    if (strstr($dbField['dbColName'],'start') === false && strstr($dbField['dbColName'],'end') === false) {
                        if ($dbField['dbColName'] == 'sysStatus') {

                            if (isset($_POST[$requestFieldID])) {
                                    $fieldColValue = "'active',";
                            } else {
                                    $fieldColValue = "'inactive',";
                            }

                            $fieldColNames .= "" . $dbField['dbColName'] . " = " . $fieldColValue;
                        }
                        else if ($dbField['dbColName'] == 'regOpen' || $dbField['dbColName'] == 'regClose'){
                                $fieldColValue = strtotime($_POST[$requestFieldID]).",";
                                $fieldColNames .= "" . $dbField['dbColName'] ." = ". $fieldColValue;
                        }
                        else if (isset($_POST[$requestFieldID])){
                                $fieldColValue = "'" . $db->escape($_POST[$requestFieldID], $dbField['stripTags']) . "',";
                                $fieldColNames .= "" . $dbField['dbColName'] . " = " . $fieldColValue;
                        }
                    }
                    else if ($dbField['dbColName'] == 'startDate'){
                            $eventStart = $db->escape($_POST[$requestFieldID], $dbField['stripTags']);
                    }
                   else if ($dbField['dbColName'] == "startTime"){
                            $eventStartTime = $db->escape($_POST[$requestFieldID], $dbField['stripTags']);
                    }
                    else if ($dbField['dbColName'] == 'endDate'){
                            $eventEnd = $db->escape($_POST[$requestFieldID], $dbField['stripTags']);
                    }
                    else if ($dbField['dbColName'] == 'endTime'){
                            $eventEndTime = $db->escape($_POST[$requestFieldID], $dbField['stripTags']);
                    }
                }
            }
            
            if ($aReg->update_session_event($calendarID, $eventStart, $eventEnd, $eventStartTime, $eventEndTime, $_POST['RQvalALPHSession_Name'],$_POST['OPvalALPHLocation'],$eventID) == 'true'){
            //trim the extra comma off the end of the above var
            $fieldColNames = substr($fieldColNames, 0, strlen($fieldColNames) - 1);

                $qry = sprintf("UPDATE %s SET %s WHERE itemID = '%s'", 
                (string) $primaryTableName, 
                (string) $fieldColNames,
                (int)$_POST['id']);
                
                $res = $db->query($qry);
                if ($db->affected_rows($res) == 1){
                    header('Location:/admin/apps/registration/beginner-registration?Update=true'); 
                }
                else{
                    echo "Update did not work";
                }
            }
            else{
                echo 'Event data could not be updated';
            }
            break;

        case "delete":

            //this delete query will work for most single table interactions, you may need to cusomize your own

            $aReg->delete_session((int)$_GET['id']);
            
            header('Location:/admin/apps/registration/beginner-registration');
            break;
        }
    } else {
        $_GET['view'] = 'edit';
    }

include $root. "/admin/templates/header.php";

?>
<h1>Beginner Registration Manager</h1>
<p>This allows the ability to setup beginner sessions, review submissions and contact registrants.</p>

<div class="boxStyle">
	<div class="boxStyleContent">
		<div class="boxStyleHeading">
			<h2>Edit</h2>
			<div class="boxStyleHeadingRight">
				<?php print "<input class='btnStyle blue' type=\"button\" name=\"newItem\" id=\"newItem\" onclick=\"javascript:window.location.href='/admin/apps/registration/beginner-registration?view=edit';\" value=\"New\" />"; ?>
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
        
        $eventID = null;

        if (is_numeric($_GET['id'])) { //if an ID is provided, we assume this is an edit and try to fetch that row from the single table


            $qry = sprintf("SELECT tp.*, UNIX_TIMESTAMP(ce.`eventStartDate`) as startDate, UNIX_TIMESTAMP(ce.`recurrenceEnd`) as endDate, 
                    UNIX_TIMESTAMP(ce.`eventStartDate`) as startTime, UNIX_TIMESTAMP(ce.`eventEndDate`) as endTime
                    FROM $primaryTableName AS tp INNER JOIN `tblCalendarEvents` AS ce ON tp.`eventID` = ce.`itemID`
                    WHERE tp.`itemID` = '%d' AND tp.`sysOpen` = '1'",
	(int)$_GET['id']
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
                $eventID = $fieldValue['eventID'];
            }


        } else {
            //yell($_GET);
        }


        if ($message != '') {
            print $message;
        }

        $formBuffer = "<form enctype=\"multipart/form-data\" name=\"tableEditorForm\" id=\"tableEditorForm\" method=\"post\" action=\"/admin/apps/registration/beginner-registration?" . $_SERVER['QUERY_STRING'] .  "\">
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
                else if ($field['dbColName'] == 'endDate' || $field['dbColName'] == 'startDate' || $field['dbColName'] == 'regOpen' || $field['dbColName'] == 'regClose'){
                    if (isset($_POST[$newFieldID]) && $message != '') {
                        $field['dbValue'] = $_POST[$newFieldID];
                    }
                    $val = ((int)$field['dbValue'] > 0)?date('Y-m-d',$field['dbValue']):'';
                    $field['widgetHTML'] = str_replace("FIELD_VALUE", $val, $field['widgetHTML']);
                }
                else if ($field['dbColName'] == 'endTime' || $field['dbColName'] == 'startTime'){
                    if (isset($_POST[$newFieldID]) && $message != '') {
                        $field['dbValue'] = $_POST[$newFieldID];
                    }
                    $val = ((int)$field['dbValue'] > 0)?date('h:i A',$field['dbValue']):'';
                    $field['widgetHTML'] = str_replace("FIELD_VALUE", $val, $field['widgetHTML']);
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
        $formBuffer .= "<input type=\"hidden\" name=\"calendarID\" id=\"calendarID\" value=\"2\" />";

        if ($dbaction == "update") { //add in the id to pass back for queries if this is an edit/update form
            $formBuffer .= "<input type=\"hidden\" name=\"id\" id=\"id\" value=\"".$_GET['id']."\" />";
            $formBuffer .= "<input type=\"hidden\" name=\"eventID\" id=\"eventID\" value=\"".$eventID."\" />";
        }

        $formBuffer .= "</td></tr>";
        $formBuffer .= "</table>";
        $formBuffer .= "<div class=\"clearfix\" style=\"margin-top: 10px; height:10px; border-top: 1px dotted #B1B1B1;\">&nbsp;</div>";
        $formBuffer .= "<input class='btnStyle grey' type=\"button\" name=\"cancelUserForm\" id=\"cancelUserForm\" onclick=\"javascript:window.location.href='" . $_SERVER['PHP_SELF'] . "';\" value=\"Cancel\" />
		<input class='btnStyle green' type=\"submit\" name=\"submitUserForm\" id=\"submitUserForm\" value=\"Save Changes\" />";
        $formBuffer .= "</form>";
        //print the form
        print $formBuffer;
        break;
    default: //(list)
       
        //list table query:
        
        $listqry = sprintf("SELECT cl.`itemID`, cl.`sessionName`, cl.`regOpen`, cl.`regClose` , (SELECT count(cr.`itemID`) FROM `tblClassesRegistration` AS cr WHERE cr.`sessionID` = cl.`itemID` AND cr.`sysOpen` = '1') AS `submissions` 
                FROM $primaryTableName AS cl WHERE cast(sysOpen as UNSIGNED) > 0 AND `level` = 'beginner'");
        
        $resqry = $db->query($listqry);
        if (is_object($resqry) && $resqry->num_rows > 0){
            //list table field titles
            $titles[0] = "Session";
            $titles[1] = "Registration Open";
            $titles[2] = "Registration Closed";

            echo '<table id="adminTableList" class="adminTableList tablesorter" width="100%" cellpadding="5" cellspacing="0" border="1">';
            echo '<thead><tr><th>Session Name</th><th>Registration Open</th><th>Registration Close</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th></tr></thead>';
            echo '<tbody>';
            while ($rs = $db->fetch_assoc($resqry)){
                $subs = 'No Registrations';
                if ((int)$rs['submissions'] > 0){
                    $subs = '<input class="btnStyle green noPad" id="btnReg_'.$rs['itemID'].'" type="button" onclick="javascript:window.location=\'/admin/apps/registration/view-registration?sid='.$rs['itemID'].'\';" value="Registrations">';
                }
                echo '<tr><td>'.$rs['sessionName'].'</td><td>'.date('Y-m-d',$rs['regOpen']).'</td><td>'.date('Y-m-d',$rs['regClose']).'</td>';
                echo '<td style="width:125px;">'.$subs.'</td>';
                echo '<td style="width:50px;"><input class="btnStyle red noPad" id="btnDelete_'.$rs['itemID'].'" type="button" onclick="javascript:confirmDelete(\'?action=delete&amp;id='.$rs['itemID'].'\');" value="Delete"></td>';
                echo '<td style="width:50px;"><input class="btnStyle blue noPad" id="btnEdit_'.$rs['itemID'].'" type="button" onclick="javascript:window.location=\'?view=edit&amp;id='.$rs['itemID'].'\';" value="Edit"></td></tr>';
            }
            echo '</tbody></table>';
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
    $auth->boot_em_out(1);

}

