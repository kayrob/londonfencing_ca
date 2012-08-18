<?php
require_once dirname(dirname(__DIR__))."/registration.php";

$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require $root . '/inc/init.php';
require $root . '/admin/classes/Editor.php';

$meta['title'] = 'Club Seasons Manager';
$meta['title_append'] = ' &bull; Quipp CMS';

$hasPermission = false;
if ($auth->has_permission("canEditReg")){
    $hasPermission = true;
}

if ($hasPermission) {
    
    $quipp->js['footer'][] = "/src/LondonFencing/registration/assets/js/adminRegistration.js";
    
    $canApprove = $auth->has_permission('approvepage');
    
    if (!isset($_GET['id'])) { $_GET['id'] = null; }
    $te = new Editor();
    
    //set the primary table name
    $primaryTableName = "tblSeasons";

    //editable fields
    
    $fields[] = array(
        'label'   => "Start Date",
        'dbColName'  => 'seasonStart',
        'tooltip'   => "Start Date for Fencing Season",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform datepicker\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalDATE",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
    $fields[] = array(
        'label'   => "End Date",
        'dbColName'  => 'seasonEnd',
        'tooltip'   => "End Date for Fencing Season",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform datepicker\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalDATE",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
    $fields[] = array(
        'label'   => "Annual Fee",
        'dbColName'  => "annualFee",
        'tooltip'   => "eg: 420",
        'writeOnce'  => "The fee for fencers who pay in full at the beginning of the season",
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalMONE",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
    $fields[] = array(
        'label'   => "Quarterly Fee",
        'dbColName'  => "quarterlyFee",
        'tooltip'   => "eg: 120",
        'writeOnce'  => "The fee for fencers who pay in 4 installments every three months",
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalMONE",
        'dbValue'   => false,
        'stripTags'  => true
    );
    
    $fields[] = array(
        'label'   => "Monthly Fee",
        'dbColName'  => "monthlyFee",
        'tooltip'   => "eg: 50",
        'writeOnce'  => "The fee for fencers who pay month-to-month",
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "RQvalMONE",
        'dbValue'   => false,
        'stripTags'  => true
    );


    $fields[] = array(
        'label'   => "Coach",
        'dbColName'  => "headCoach",
        'tooltip'   => "If empty will be displayed as 'TBA'",
        'writeOnce'  => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode'   => "OPvalALPH",
        'dbValue'   => false,
        'stripTags'  => true
    );

    
    $fields[] = array(
        'label'   => "Active",
        'dbColName'  => "sysStatus",
        'tooltip'   => 'Only active seasons will be available for registration',
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
                    else if (strstr($dbField['dbColName'],"season") !== false){
                        $fieldColValues .= strtotime($_POST[$requestFieldID]).",";
                        $fieldColNames .= "" . $dbField['dbColName'] . ",";
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
            
            $qry = sprintf("INSERT INTO %s (%s, sysDateCreated, sysOpen) VALUES (%s, NOW(),  '1')",
                (string) $primaryTableName,
                (string) $fieldColNames,
                (string) $fieldColValues
            );
            $res = $db->query($qry);

            if ($db->affected_rows($res) == 1){
                header('Location:/admin/apps/registration/seasons?Insert=true'); 
            }
            else{
                echo "Insert did not work";
                echo $qry;
                echo $db->error();
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
                        else if (strstr($dbField['dbColName'],"season") !== false){
                            $fieldColValue = strtotime($_POST[$requestFieldID]).",";
                            $fieldColNames .= "" . $dbField['dbColName'] . "=" . $fieldColValue;
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
            if ($db->affected_rows($res) == 1){
                header('Location:/admin/apps/registration/seasons?Update=true'); 
            }
            else{
                echo "Update did not work";
            }
            
            break;

        case "delete":

            //this delete query will work for most single table interactions, you may need to cusomize your own

            $db->query(sprintf("UPDATE %s SET `sysOpen` = '0', `sysStatus` = 'inactive' WHERE itemID = '%s'", 
            (int)$_POST['id']));
            
            header('Location:/admin/apps/registration/seasons');
            break;
        }
    } else {
        $_GET['view'] = 'edit';
    }

include $root. "/admin/templates/header.php";

?>
<h1>Club Season Manager</h1>
<p>Use this tool to add details about the current/upcoming club season including start dates and fees.</p>

<div class="boxStyle">
	<div class="boxStyleContent">
		<div class="boxStyleHeading">
			<h2>Edit</h2>
			<div class="boxStyleHeadingRight">
				<?php print "<input class='btnStyle blue' type=\"button\" name=\"newItem\" id=\"newItem\" onclick=\"javascript:window.location.href='/admin/apps/registration/seasons?view=edit';\" value=\"New\" />"; ?>
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


            $qry = sprintf("SELECT * FROM $primaryTableName WHERE `itemID` = '%d' AND `sysOpen` = '1'",
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

        $formBuffer = "<form enctype=\"multipart/form-data\" name=\"tableEditorForm\" id=\"tableEditorForm\" method=\"post\" action=\"/admin/apps/registration/seasons?" . $_SERVER['QUERY_STRING'] .  "\">
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
                else if (stristr($field['dbColName'],"season") !== false){
                    if (isset($_POST[$newFieldID]) && $message != '') {
                        $field['dbValue'] = strtotime($_POST[$newFieldID]);
                    }
                    $val = ((int)$field['dbValue'] > 0) ? date('Y-m-d',$field['dbValue']) : '';
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

        if ($dbaction == "update") { //add in the id to pass back for queries if this is an edit/update form
            $formBuffer .= "<input type=\"hidden\" name=\"id\" id=\"id\" value=\"".$_GET['id']."\" />";
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
        
        $listqry = sprintf("SELECT `itemID`, `seasonStart`, `seasonEnd`, `sysStatus` FROM $primaryTableName WHERE cast(sysOpen as UNSIGNED) = 1");
        
        $resqry = $db->query($listqry);
        if (is_object($resqry) && $resqry->num_rows > 0){
            //list table field titles
            $titles[0] = "Season";
            $titles[1] = "Season Start";
            $titles[2] = "Season End";
            $titles[3] = "Status";

            echo '<table id="adminTableList" class="adminTableList tablesorter" width="100%" cellpadding="5" cellspacing="0" border="1">';
            echo '<thead><tr><th>Season</th><th>Season Start</th><th>Season End</th><th>Status</th><th>&nbsp;</th><th>&nbsp;</th></tr></thead>';
            echo '<tbody>';
            while ($rs = $db->fetch_assoc($resqry)){
                echo '<tr><td>'.date('Y',$rs['seasonStart'])."-".date('Y',$rs['seasonEnd']).'</td><td>'.date('Y-m-d',$rs['seasonStart']).'</td><td>'.date('Y-m-d',$rs['seasonEnd']).'</td>';
                echo '<td style="width:125px;">'.$rs['sysStatus'].'</td>';
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