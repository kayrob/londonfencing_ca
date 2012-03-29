<?php
require_once dirname(dirname(__DIR__))."/members.php";
use LondonFencing\members as MEMB;

   
$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require $root . '/inc/init.php';
require $root . '/admin/classes/Editor.php';

$meta['title'] = 'Members Manager';
$meta['title_append'] = ' &bull; Quipp CMS';

$hasPermission = false;

if ($auth->has_permission('canEditReg')){
    $hasPermission = true;
}

if ($hasPermission) {
    
    if (!isset($_GET['id'])) { $_GET['id'] = null; }
    $te = new Editor();

    $mem = new MEMB\members($db);
    
    //set the primary table name
    $primaryTableName = "tblMembers";

    //editable fields
    $fields[] = array(
        'label'   => "Last Name",
        'dbColName'  => "lastName",
        'tooltip'   => "Member Last Name",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
    $fields[] = array(
        'label'   => "First Name",
        'dbColName'  => "firstName",
        'tooltip'   => "Member First Name",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
    $fields[] = array(
        'label'   => "Email",
        'dbColName'  => "email",
        'tooltip'   => "Email Address eg user@domain.com",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalMAIL",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
    $fields[] = array(
        'label'   => "Birth Date",
        'dbColName'  => "birthDate",
        'tooltip'   => "Optional",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "OPvalDATE",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
$gender = array("F" => "Female", "M" => "Male");
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
        'dbColName'  => "address",
        'tooltip'   => "Member Mailing Address *Optional",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "OPvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );
    $fields[] = array(
        'label'   => "Unit/Apt",
        'dbColName'  => "address2",
        'tooltip'   => "Optional",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "OPvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );
    $fields[] = array(
        'label'   => "City",
        'dbColName'  => "city",
        'tooltip'   => "Optional",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "OPvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );
    $provs = array("ON","AB","BC","MB","NB","NL","NS","NT","NU","PE","QC","SK","YT");
    
    $fields[] = array(
        'label'   => "Province",
        'dbColName'  => "province",
        'tooltip'   => "Optional",
        'writeOnce'  => false,
        'widgetHTML' => "",
        'valCode'   => "OPvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );

    $fields[] = array(
        'label'   => "Postal Code",
        'dbColName'  => "postalCode",
        'tooltip'   => "N1N 1N1 *Optional",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"date\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "OPvalPOST",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
    $fields[] = array(
        'label'   => "Phone Number",
        'dbColName'  => "phone",
        'tooltip'   => "519-555-3333 *Optional",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"date\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "OPvalPHON",
        'dbValue'   => false,
        'stripTags'  => true
    );
   $fields[] = array(
        'label'   => "Parent/Guardian",
        'dbColName'  => "parentName",
        'tooltip'   => "Optional",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "OPvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );
   $memberTypes = array("foundation" => "Foundation", "transitional" => "Transitional", "excellence" => "Excellence");
   $fields[] = array(
        'label'   => "Membership Type",
        'dbColName'  => "membershipType",
        'tooltip'   => "Optional",
        'writeOnce'  => false,
        'widgetHTML' => "",
        'valCode'   => "OPvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );
   
   $fields[] = array(
        'label'   => "CFF Number",
        'dbColName'  => "cffNumber",
        'tooltip'   => "Optional",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "OPvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
   $fields[] = array(
        'label'   => "OFA Number",
        'dbColName'  => "ofaNumber",
        'tooltip'   => "Optional",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "OPvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
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
                    else if ($dbField['dbColName'] == 'birthDate'){
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

            $qry = sprintf("INSERT INTO %s (%s, sysUserLastMod, sysDateLastMod, sysDateCreated, sysOpen) VALUES (%s, '%d', %s, %s, '1')",
                (string) $primaryTableName,
                (string) $fieldColNames,
                (string) $fieldColValues,
                $user->id,
                $db->now,
                $db->now
            );
            //yell($qry);
            //print $te->commit_a_modify_action($qry, "Insert", true);
            $res = $db->query($qry);
            
            if ($db->affected_rows($res) == 1){

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
                    else if ($dbField['dbColName'] == 'birthDate'){
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
                            <?php print "<input class='btnStyle blue' type=\"button\" name=\"newItem\" id=\"newItem\" onclick=\"javascript:window.location.href='" . dirname($_SERVER['REQUEST_URI']) . "/index?view=edit';\" value=\"New Single Member\" />"; ?>
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
                else if ($field['dbColName'] == 'birthDate'){
                    $field['dbValue'] = ($field['dbValue'] > 0)?date('Y-m-d', $field['dbValue']) : '';
                    $field['widgetHTML'] = str_replace("FIELD_VALUE", $field['dbValue'], $field['widgetHTML']);
                }
                else if ($field['dbColName'] == 'gender'){
                    $field['widgetHTML'] = '<select name="'.$newFieldID.'" id="'.$newFieldID.'">';
                    foreach($gender as $gAbbr => $oText){
                        $field['widgetHTML'] .= '<option value="'.$gAbbr.'"'.($field['dbValue'] == $gAbbr ? ' selected="selected"':'').'>'.$oText.($field['dbValue'] == $gAbbr ? '*':'').'</option>';
                    }
                    $field['widgetHTML'] .= '</select>';
                }
                else if ($field['dbColName'] == 'province'){
                    $field['widgetHTML'] = '<select select name="'.$newFieldID.'" id="'.$newFieldID.'">';
                    foreach($provs as $pAbbr){
                        $field['widgetHTML'] .= '<option value="'.$pAbbr.'"'.($field['dbValue'] == $pAbbr ? ' selected="selected"':'').'>'.$pAbbr.($field['dbValue'] == $pAbbr ? '*':'').'</option>';
                    }
                    $field['widgetHTML'] .= '</select>';
                }
                else if ($field['dbColName'] == 'membershipType'){
                    $field['widgetHTML'] = '<select select name="'.$newFieldID.'" id="'.$newFieldID.'">';
                    foreach($memberTypes as $mAbbr => $mText){
                        $field['widgetHTML'] .= '<option value="'.$mAbbr.'"'.($field['dbValue'] == $mAbbr ? ' selected="selected"':'').'>'.$mText.($field['dbValue'] == $mAbbr ? '*':'').'</option>';
                    }
                    $field['widgetHTML'] .= '</select>';
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
            echo '<thead><tr><th>Member Name</th><th>Email Address</th><th>Parent Name</th><th>Membership Type</th><th>Status</th><th>&nbsp;</th><th>&nbsp;</th></tr></thead>';
            echo '<tbody>';
            if (!empty($members)){
                foreach($members as $email => $aInfo){
                    echo '<tr><td>'.$aInfo['name'].'</td><td>'.$email.'</td><td>'.$aInfo['parent'].'</td><td>'.$aInfo['membership'].'</td><td>'.$aInfo['status'].'</td>
                        <td><input class="btnStyle blue noPad" id="btnEdit_'.$aInfo['id'].'" type="button" onclick="javascript:window.location=\'?view=edit&amp;id='.$aInfo['id'].'\';" value="Edit" /></td>
                        <td><input class="btnStyle red noPad" id="btnDelete_'.$aInfo['id'].'" type="button" onclick="javascript:confirmDelete(\'?action=delete&amp;id='.$aInfo['id'].'\');" value="Delete"></td>
                        </tr>';
                }
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