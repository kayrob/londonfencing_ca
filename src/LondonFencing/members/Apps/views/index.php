<?php
require_once dirname(dirname(dirname(__DIR__))) . "/registration/Apps/AdminRegister.php";
require_once dirname(dirname(__DIR__))."/members.php";
require_once dirname(dirname(dirname(__DIR__))) . "/registration/registration.php";

use LondonFencing\registration\Apps as AReg;
use LondonFencing\members as MEMB;

   
$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require $root . '/inc/init.php';
require $root . '/admin/classes/Editor.php';

$meta['title'] = 'Registration: Club Members';
$meta['title_append'] = ' &bull; Quipp CMS';

$hasPermission = false;

if ($auth->has_permission('canEditReg')){
    $hasPermission = true;
}

if ($hasPermission) {
    
    if (!isset($_GET['id'])) { $_GET['id'] = null; }
    if (!isset($_GET['uid'])) { $_GET['uid'] = null; }
    if (!isset($_GET['season'])) { $_GET['season'] = null; }
    $te = new Editor();
    
    $sRes = $db->query("SELECT `itemID`, `seasonStart`, `seasonEnd` FROM `tblSeasons` WHERE `sysStatus` = 'active' AND `sysOpen` = '1' ORDER BY `seasonEnd` desc");
    $seasons = array();
    $currentSeasonID = 0;
    if ($sRes->num_rows > 0){
        while ($sRow = $db->fetch_assoc($sRes)){
            $seasons[trim($sRow["itemID"])] = date('Y', trim($sRow["seasonStart"]))."-".date('Y', trim($sRow["seasonEnd"]));
            if ($currentSeasonID == 0){
                $currentSeasonID = (int)$sRow["itemID"];
            }
        }
    }

    $mem = new MEMB\members($db);
    
    //set the primary table name
    $primaryTableName = "tblMembersRegistration";
    $feeTypes = array("annually" => "Annual (one-time)", "quarterly" => "Quarterly (4-times)", "monthly" => "Monthly");
    $membershipTypes = array("Excellence", "Foundation","Transition","Competitive","Recreation");
    //editable fields
    $fields[] = array(
        'label'   => "Member Name",
        'dbColName'  => "userID",
        'writeOnce'  => false,
        'widgetHTML' => "FIELD_VALUE",
        'valCode'   => "",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
    $fields[] = array(
        'label'   => "Season",
        'dbColName'  => "seasonID",
        'tooltip'   => "The current fencing season",
        'writeOnce'  => false,
        'widgetHTML' => "FIELD_VALUE",
        'valCode'   => "",
        'dbValue'   => false,
        'stripTags'  => true
    );
    $fields[] = array(
        'label'   => "Membership Type",
        'dbColName'  => "membershipType",
        'writeOnce'  => false,
        'widgetHTML' => "<select class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\">FIELD_VALUE</select>",
        'valCode'   => "RQvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
    $fields[] = array(
        'label'   => "Fee Type",
        'dbColName'  => "feeType",
        'tooltip'   => "Annually, Quarterly, Monthly",
        'writeOnce'  => false,
        'widgetHTML' => "<select class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\">FIELD_VALUE</select>",
        'valCode'   => "RQvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );
    $fields[] = array(
        'label' => "Form Submitted",
        'dbColName' => "formDate",
        'tooltip' => "Date consent form was submitted",
        'writeOnce' => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform datepicker\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode' => "OPvalDATE",
        'dbValue' => false,
        'stripTags' => true
    );
    $fields[] = array(
        'label'   => "Active",
        'dbColName'  => "sysStatus",
        'tooltip'   => 'Member is currently active at the Club',
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

    if (!empty($_POST) && (validate_form($_POST) || $_POST['dbaction'] == 'delete')) {

        switch ($_POST['dbaction']) {
        case "insert":

            //this insert query will work for most single table interactions, you may need to cusomize your own
            
            $fieldColNames  = '';
            $fieldColValues = '';
            foreach ($fields as $dbField) {
                if ($dbField['dbColName'] != false) {
                    $requestFieldID = $dbField['valCode'] . str_replace(" ", "_", $dbField['label']);
                    if ($dbField['dbColName'] == 'sysStatus') {
                    
                            if (isset($_POST[$requestFieldID])) {
                                    $fieldColValues .= "'active',";
                            } else {
                                    $fieldColValues .= "'inactive',";
                            }

                            $fieldColNames .= "" . $dbField['dbColName'] .",";
                    }
                    else if ($dbField['dbColName'] == 'formDate'){
                        if(trim($_POST[$requestFieldID]) != ''){
                            $fieldColValues .= strtotime($_POST[$requestFieldID]).",";
                            $fieldColNames .= "" . $dbField['dbColName'] . ",";
                        }
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

            $qry = sprintf("INSERT INTO %s (%s, sysUserLastMod, sysDateLastMod, sysDateCreated, sysOpen, seasonID, userID) VALUES (%s, '%d', %s, %s, '1', '%d', '%d')",
                (string) $primaryTableName,
                (string) $fieldColNames,
                (string) $fieldColValues,
                $user->id,
                $db->now,
                $db->now,
                (int)$_POST['season'],
                (int)$_POST['uid']
            );
            //yell($qry);
            //print $te->commit_a_modify_action($qry, "Insert", true);
            $res = $db->query($qry);
            $insID = $db->insert_id();
            if ($db->affected_rows($res) == 1){
                if (AReg\AdminRegister::validatePayments($_POST["payment"][0], $_POST["payment"][1],"cash") === true){
                        $mem->createMemberPayment($_POST["payment"], $insID, $user->id);
                }
                header('Location:' . dirname($_SERVER['REQUEST_URI']) . '/index?Insert=true');
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
                    else if ($dbField['dbColName'] == 'formDate'){
                        if (trim($_POST[$requestFieldID]) != ''){
                            $fieldColNames .= "" . $dbField['dbColName'] . " = " . strtotime($_POST[$requestFieldID])  . ",";
                        }
                    }
                    else if (isset($_POST[$requestFieldID])){
                        $fieldColValue = "'" . $db->escape($_POST[$requestFieldID], $dbField['stripTags']) . "',";
                        $fieldColNames .= "" . $dbField['dbColName'] . " = " . $fieldColValue;
                    }
                }
            }

            //trim the extra comma off the end of the above var
            $fieldColNames = substr($fieldColNames, 0, strlen($fieldColNames) - 1);

            $qry = sprintf("UPDATE %s SET %s, sysUserLastMod='%d', sysDateLastMod=NOW() WHERE itemID = '%s'", 
            (string) $primaryTableName, 
            (string) $fieldColNames, 
            $user->id, 
            (int)$_POST['id']);

            
            $res = $db->query($qry);
            if ($db->affected_rows($res) == 1|| $db->error() == 0){
                
                if (AReg\AdminRegister::validatePayments($_POST["payment"][0], $_POST["payment"][1], "cash") === true){
                    $mem->createMemberPayment($_POST["payment"], (int)$_POST['id'], $user->id);
                }
                if (isset($_POST['radioPayment'])){
                        $editPayments = (isset($_POST["editPayment"])) ? $_POST["editPayment"] : array();
                        $mem->editMemberPayment($_POST['radioPayment'], $editPayments, (int)$_POST['id']);
                }
                header('Location:' . dirname($_SERVER['REQUEST_URI']) . '/index?Update=true');
            }
            else{
                echo "Update did not work";
            }
            break;

        case "delete":

            //this delete query will work for most single table interactions, you may need to cusomize your own
            
            $qry = sprintf("UPDATE %s SET sysOpen = '0', `sysStatus` = 'inactive' WHERE itemID = '%d'",
                (string) $primaryTableName,
                (int) intval($_GET['id'], 10));

            echo $qry;
            print $te->commit_a_modify_action($qry, "Delete");
            header('Location:' . dirname($_SERVER['REQUEST_URI']) . '/index?delete=true');
            break;
        }
    } else {
        $_GET['view'] = 'edit';
    }

$quipp->js['footer'][] = "/src/LondonFencing/registration/assets/js/adminRegistration.js";
include $root. "/admin/templates/header.php";

?>
<h1>Members Manager</h1>
<p>This allows the ability to add regular member information. Add email addresses to select users via the mailer, and set membership types 
to generate reports</p>

<div class="boxStyle">
    <div class="boxStyleContent">
            <div class="boxStyleHeading">
                    <h2>Edit</h2>
                    <div class="boxStyleHeadingRight">
                            <?php print "<input class='btnStyle blue' type=\"button\" name=\"newItem\" id=\"newItem\" onclick=\"javascript:window.location.href='/admin/users.php?view=edit';\" value=\"New Single Member\" />"; ?>
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
        $_GET["uid"] = (int)$_GET['uid'];
        $_GET['season'] = (int)$_GET['season'];
        
        $payments = array();

        if (is_numeric($_GET['id'])) { //if an ID is provided, we assume this is an edit and try to fetch that row from the single table

            
            $qry = sprintf("SELECT * FROM $primaryTableName WHERE itemID = '%d' AND `sysOpen` = '1' ;",
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
                
                
                $qryP = sprintf("SELECT `paymentAmount`, `paymentDate`, `itemID` FROM `tblMembersPayments` 
                        WHERE `sysOpen` = '1' AND `registrationID` = '%d' ORDER BY `paymentDate` DESC",
                            (int)$_GET['id'] 
                     );
                    $resP = $db->query($qryP);
                    if ($db->valid($resP)){
                        while ($rowP = $db->fetch_assoc($resP)){
                            $payments[trim($rowP["itemID"])] = array(
                                'date'      => trim($rowP["paymentDate"]), 
                                'amount' => trim($rowP["paymentAmount"])
                                );
                        }
                    }
            }


        } else {
            //yell($_GET);
        }


        if ($message != '') {
            print $message;
        }

        $formBuffer = "
            <form enctype=\"multipart/form-data\" name=\"tableEditorForm\" id=\"tableEditorForm\" method=\"post\" action=\"" . $_SERVER['REQUEST_URI'] .  "\">
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
                else if ($field['dbColName'] == 'userID'){
                    $field['widgetHTML'] = str_replace("FIELD_VALUE", $user->get_meta("First Name", $_GET['uid'])." ".$user->get_meta("Last Name", $_GET['uid']), $field['widgetHTML']);
                }
                else if ($field['dbColName'] == 'seasonID'){
                    $field['widgetHTML'] = str_replace("FIELD_VALUE", $seasons[$currentSeasonID], $field['widgetHTML']);
                }
                else if ($field['dbColName'] == 'formDate'){
                    $field['dbValue'] = ($field['dbValue'] > 0)?date('Y-m-d', $field['dbValue']) : '';
                    $field['widgetHTML'] = str_replace("FIELD_VALUE", $field['dbValue'], $field['widgetHTML']);
                }
                else if ($field['dbColName'] == 'feeType'){
                    $opts = "";
                    foreach($feeTypes as $type => $fee){
                        $opts .= '<option value="'.$type.'"'.($field['dbValue'] == $type ? ' selected="selected"':'').'>'.$fee.($field['dbValue'] == $type ? '*':'').'</option>';
                    }
                    $field['widgetHTML'] = str_replace("FIELD_VALUE", $opts, $field['widgetHTML']);
                }
                else if ($field['dbColName'] == 'membershipType'){
                    $opts = "";
                    foreach($membershipTypes as $mText){
                        $opts .= '<option value="'.$mText.'"'.($field['dbValue'] == $mText ? ' selected="selected"':'').'>'.$mText.($field['dbValue'] == $mText ? '*':'').'</option>';
                    }
                    $field['widgetHTML'] = str_replace("FIELD_VALUE", $opts, $field['widgetHTML']);
                }
                else {
                    if (isset($_POST[$newFieldID]) && $message != '') {
                        $field['dbValue'] = $_POST[$newFieldID];
                    }
                    $field['widgetHTML'] = str_replace("FIELD_VALUE", $field['dbValue'], $field['widgetHTML']);

                }

            }
            //write in the html
            $formBuffer .= "<td valign=\"top\"><label for=\"".$newFieldID."\">" . $field['label'] . "</label></td><td>" . $field['widgetHTML'] . (isset($field['tooltip']) ? " <p>" . $field['tooltip'] . "</p>" : '')."</td>";
            $formBuffer .= "</tr>";
        }

        //temp
        $id = null;
        $formAction = null;
        //end temp

        $formBuffer .= "<tr><td colspan='2'>
	<input type=\"hidden\" name=\"nonce\" value=\"".Quipp()->config('security.nonce')."\" />
	<input type=\"hidden\" name=\"dbaction\" id=\"dbaction\" value=\"$dbaction\" />
        <input type=\"hidden\" name=\"season\" id=\"season\" value=\"".$_GET['season']."\" />
        <input type=\"hidden\" name=\"uid\" id=\"uid\" value=\"".$_GET['uid']."\" />";

        if ($dbaction == "update") { //add in the id to pass back for queries if this is an edit/update form
            $formBuffer .= "<input type=\"hidden\" name=\"id\" id=\"id\" value=\"".$_GET['id']."\" />";
        }

        $formBuffer .= "</td></tr>";
        $formBuffer .= "</table>";
        //payments
        $formBuffer .= "<p>&nbsp;</p><h3>Payments</h3><p>&nbsp;</p>";
            $formBuffer .=  '<table id="payments">';
            $formBuffer .= '<tr><td width="30%"><label for="paymentDate">New Payment Date</label></td><td><input type="text" name="payment[]" id="paymentDate" class="uniform datepicker" style="width:300px" /></td></tr>';
            $formBuffer .= '<tr><td><label for="paymentAmount">New Payment Amount</label></td><td><input type="text" name="payment[]" id="paymentAmount" class="uniform" style="width:300px" /></td></tr>';
            $formBuffer .= '</table><p>&nbsp;</p><table id="history"><tr><td colspan="2"><strong>Payment History</strong></td></tr>';
            if (!empty($payments)){
                foreach($payments as $pID => $nfo){
                    $formBuffer .= '<tr>';
                    $formBuffer .= '<td width="30%"><input type="radio" name="radioPayment['.$pID.']" value="edit" id="editPayment_'.$pID.'" /><label for="editPayment_'.$pID.'">Edit</label>';
                    $formBuffer .= '<input type="radio" name="radioPayment['.$pID.']" value="delete" id="deletePayment_'.$pID.'" /><label for="deletePayment_'.$pID.'">Delete</label>';
                    $formBuffer .= '<input type="radio" name="resetPayment_'.$pID.'" id="resetPayment_'.$pID.'" /><label for="resetPayment_'.$pID.'">Reset</label></td>';
                    $formBuffer .= '<td><label for="editDate_'.$pID.'">Date</label>&nbsp;<input type="text" name="editPayment['.$pID.'][]" id="editDate_'.$pID.'" class="uniform datepicker" style="width:150px" disabled="disabled" value="'.date("Y-m-d",$nfo["date"]).'"/>';
                    $formBuffer .= '&nbsp;<label for="editAmount_'.$pID.'">Amount</label>&nbsp;<input type="text" name="editPayment['.$pID.'][]" id="editAmount_'.$pID.'" class="uniform" style="width:150px" disabled="disabled" value="'.number_format($nfo['amount'],2).'"/></td></tr>';
                }
            }
            else{
                $formBuffer .= '<tr><td colspan="2">No Payment History</td></tr>';
            }
            $formBuffer .= '</table>';
        
        $formBuffer .= "<div class=\"clearfix\" style=\"margin-top: 10px; height:10px; border-top: 1px dotted #B1B1B1;\">&nbsp;</div>";
        $formBuffer .= "<input class='btnStyle grey' type=\"button\" name=\"cancelUserForm\" id=\"cancelUserForm\" onclick=\"javascript:window.location.href='" . dirname($_SERVER['REQUEST_URI']) . "/index';\" value=\"Cancel\" />
		<input class='btnStyle green' type=\"submit\" name=\"submitUserForm\" id=\"submitUserForm\" value=\"Save Changes\" />";
        $formBuffer .= "</form>";
        //print the form
        print $formBuffer;
        break;
    default: //(list)

        $members = $mem->getMembersEmailList('all'); 
        
        if (!empty($members)){
            echo '<form name="frmMembers" action="/admin/apps/members/index" method="post" enctype="multipart/form-data">';
            echo '<table id="adminTableList_email" class="adminTableList tablesorter" width="100%" cellpadding="5" cellspacing="0" border="1">';
            echo '<thead><tr><th>Member Name</th><th>Email Address</th><th>Parent Name</th><th>Membership Type</th><th>Status</th><th>View/Edit</th><th>&nbsp;</th></tr></thead>';
            echo '<tbody>';

            foreach($members as $email => $aInfo){
                echo '<tr><td>'.$aInfo['name'].'</td><td>'.$email.'</td><td>'.$aInfo['parent'].'</td><td>'.$aInfo['membership'].'</td><td>'.$aInfo['status'].'</td>
                    <td><select id="selEdit_'.$aInfo['id'].'" name="selEdit_'.$aInfo['id'].'"><option value="">--</option>';
                foreach($seasons as $sID => $sYear){
                    if (0 != ($regID = $mem->getRegistered($aInfo['id'], $sID))){
                        echo '<option value="'.$sID.'-'.$regID.'">'.$sYear.'</option>';
                    }
                }
                echo '</select></td><td>';
                if (0 == ($regID = $mem->getRegistered($aInfo['id'], $currentSeasonID))){
                    echo '<input class="btnStyle green noPad" id="btnReg_'.$aInfo['id'].'" type="button" onclick="javascript:window.location=\'?view=edit&amp;uid='.$aInfo['id'].'&amp;season='.$currentSeasonID.'\';" value="Register">';
                }
                else{ echo '&nbsp;'; }
                
                echo '</td>
                    </tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</form>';
        }
        else{
            echo '<p>No members listed</p>'; 
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
global $quipp;
$quipp->js['footer'][] = '/src/LondonFencing/members/assets/js/members.js';
include $root. "/admin/templates/footer.php";

}
else{
    echo 'no permission';

}
?>