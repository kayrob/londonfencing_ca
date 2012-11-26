<?php
require_once dirname(dirname(__DIR__)) . "/registration.php";

use LondonFencing\registration\Apps as AReg;

$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require $root . '/inc/init.php';
require $root . '/admin/classes/Editor.php';

$meta['title'] = 'Intermediate Registration';
$meta['title_append'] = ' &bull; Quipp CMS';

$hasPermission = false;
if ($auth->has_permission("canEditReg")) {
    $hasPermission = true;
}

$filter = 'active';
  
if (isset($_GET['filter']) && (stristr($_GET['filter'],'active') !== false || $_GET['filter'] == 'all')){
      $filter = $db->escape($_GET['filter'],true);
}

if ($hasPermission) {
    $aReg = new AReg\AdminRegister(false, $db);
    $quipp->js['footer'][] = "/src/LondonFencing/registration/assets/js/adminRegistration.js";

    $te = new Editor();

    //set the primary table name
    $primaryTableName = "tblIntermediateRegistration";

    $provs = array("AB", "BC", "MB", "NB", "NL", "NS", "NT", "NU", "ON", "PE", "QC", "SK", "YT");
    $gender = array("F" => "Female", "M" => "Male");
    $payOpts = array("monthly" => "Monthly", "drop-in" => "Drop-In", "card" => "Session Card");
    //editable fields
    $fields[] = array(
        'label' => "First Name",
        'dbColName' => "firstName",
        'tooltip' => "Participant First Name",
        'writeOnce' => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode' => "RQvalALPH",
        'dbValue' => false,
        'stripTags' => true
    );

    $fields[] = array(
        'label' => "Last Name",
        'dbColName' => 'lastName',
        'tooltip' => "Participant Last Name",
        'writeOnce' => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode' => "RQvalALPH",
        'dbValue' => false,
        'stripTags' => true
    );
    $fields[] = array(
        'label' => "Birth Date",
        'dbColName' => 'birthDate',
        'tooltip' => "Participant's Date of Birth",
        'writeOnce' => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform birthdatepicker\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode' => "OPvalDATE",
        'dbValue' => false,
        'stripTags' => true
    );
    $fields[] = array(
        'label' => "Gender",
        'dbColName' => "gender",
        'tooltip' => "",
        'writeOnce' => false,
        'widgetHTML' => "",
        'valCode' => "RQvalALPH",
        'dbValue' => false,
        'stripTags' => true
    );
    $fields[] = array(
        'label' => "Address",
        'dbColName' => 'address',
        'tooltip' => "Participant's Home Address",
        'writeOnce' => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode' => "OPvalALPH",
        'dbValue' => false,
        'stripTags' => true
    );
    $fields[] = array(
        'label' => "Apt/Unit",
        'dbColName' => 'address2',
        'tooltip' => "",
        'writeOnce' => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode' => "OPvalALPH",
        'dbValue' => false,
        'stripTags' => true
    );

    $fields[] = array(
        'label' => "City",
        'dbColName' => "city",
        'tooltip' => "",
        'writeOnce' => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode' => "OPvalALPH",
        'dbValue' => false,
        'stripTags' => true
    );

    $fields[] = array(
        'label' => "Province",
        'dbColName' => "province",
        'tooltip' => "",
        'writeOnce' => false,
        'widgetHTML' => "",
        'valCode' => "OPvalALPH",
        'dbValue' => false,
        'stripTags' => true
    );
    $fields[] = array(
        'label' => "Postal Code",
        'dbColName' => "postalCode",
        'tooltip' => false,
        'writeOnce' => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" min=\"1\" />",
        'valCode' => "OPvalPOST",
        'dbValue' => false,
        'stripTags' => true
    );

    $fields[] = array(
        'label' => "Phone Number",
        'dbColName' => "phoneNumber",
        'tooltip' => "eg. 519-555-1212",
        'writeOnce' => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode' => "OPvalPHON",
        'dbValue' => false,
        'stripTags' => true
    );

    $fields[] = array(
        'label' => "Parent/Guardian",
        'dbColName' => "parentName",
        'tooltip' => "Parent or Legal Guardian for participants under the age of 18",
        'writeOnce' => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode' => "OPvalALPH",
        'dbValue' => false,
        'stripTags' => true
    );

    $fields[] = array(
        'label' => "Email Address",
        'dbColName' => "email",
        'tooltip' => "Contact email address e.g user@domain.com",
        'writeOnce' => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode' => "RQvalMAIL",
        'dbValue' => false,
        'stripTags' => true
    );
    $fields[] = array(
        'label' => "Emergency Contact",
        'dbColName' => "emergencyContact",
        'tooltip' => "The name of the emergency contact person",
        'writeOnce' => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode' => "OPvalALPH",
        'dbValue' => false,
        'stripTags' => true
    );
    $fields[] = array(
        'label' => "Emergency Phone",
        'dbColName' => "emergencyPhone",
        'tooltip' => "The name of the emergency contact phone number e.g 519-555-2323",
        'writeOnce' => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode' => "OPvalPHON",
        'dbValue' => false,
        'stripTags' => true
    );
     $fields[] = array(
        'label' => "Allergies or Medical Concerns",
        'dbColName' => "notes",
        'tooltip' => "",
        'writeOnce' => false,
        'widgetHTML' => "<textarea id=\"FIELD_ID\" name=\"FIELD_ID\" cols=\"50\" rows=\"2\">FIELD_VALUE</textarea>",
        'valCode' => "OPvalALPH",
        'dbValue' => false,
        'stripTags' => true
    );
   /* $fields[] = array(
        'label' => "Payment Date",
        'dbColName' => "paymentDate",
        'tooltip' => "",
        'writeOnce' => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode' => "OPvalDATE",
        'dbValue' => false,
        'stripTags' => true
    );*/
    $fields[] = array(
        'label' => "Form Submitted",
        'dbColName' => "formDate",
        'tooltip' => "",
        'writeOnce' => false,
        'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform datepicker\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
        'valCode' => "OPvalDATE",
        'dbValue' => false,
        'stripTags' => true
    );
    $fields[] = array(
        'label' => "Active",
        'dbColName' => "sysStatus",
        'tooltip' => "If the fencer is still attending classes",
        'writeOnce' => false,
        'widgetHTML' => "<input type=\"checkbox\" name=\"FIELD_ID\" id=\"FIELD_ID\" value=\"active\" FIELD_VALUE />",
        'valCode' => "OPvalALPH",
        'dbValue' => false,
        'stripTags' => true
    );

    if (!isset($_POST['dbaction'])) {
        $_POST['dbaction'] = null;

        if (isset($_GET['action'])) {
            $_POST['dbaction'] = $_GET['action'];
        }
    }
    if ((!empty($_POST) && validate_form($_POST)) || $_POST['dbaction'] == 'delete') {

        //yell($_POST);

        switch ($_POST['dbaction']) {
            case "addBeg":
                //add beginners to intermediate class automatically
                $valid = true;
                if (isset($_POST['begList'])){
                    foreach($_POST['begList'] as $begID){
                        if ((int)$begID > 0){
                           $isValid = $aReg->createBeginnerToIntermediate($begID);
                           if ($valid === true && $isValid === false){
                               $valid = false;
                           }
                        }
                    }
                }
                if ($valid === true){
                    header("location: /admin/apps/registration/intermediate-registration");
                }
                else{
                    echo '<p>All Inserts Did Not Work</p>';
                }
                break;
                
            case "addClub":
                //add beginners to intermediate class automatically
                $valid = true;
                if (isset($_POST['clubList'])){
                    foreach($_POST['clubList'] as $intID){
                        if ((int)$intID > 0){
                           $isValid = $aReg->intermediateToClub($intID, $user);
                           if ($valid === true && $isValid === false){
                               $valid = false;
                           }
                        }
                    }
                }
                if ($valid === true){
                    header("location: /admin/apps/registration/intermediate-registration");
                }
                else{
                    echo '<p>All Inserts Did Not Work</p>';
                }
                break;
            
            case "insert":

                //$countQry = $db->fetch_assoc($db->query("SELECT (COUNT(itemID) + 1) AS rk FROM tblIntermediateRegistration"));
                //$newCount = (isset($countQry['rk'])) ? $countQry['rk'] : "1";

                //this insert query will work for most single table interactions, you may need to cusomize your own

                $fieldColNames = '';
                $fieldColValues = '';

                foreach ($fields as $dbField) {
                    if ($dbField['dbColName'] != false) {
                        
                        $requestFieldID = $dbField['valCode'] . str_replace(" ", "_", $dbField['label']);
                        if ($dbField['dbColName'] == 'sysStatus') {
                            $fieldColValues .= "'active',"; //by default
                            /*if (isset($_POST[$requestFieldID])) {
                                $fieldColValues .= "'active',";
                            } else {
                                $fieldColValues .= "'inactive',";
                            }*/
                            $fieldColNames .= "" . $dbField['dbColName'] . ",";
                        } else if ($dbField['dbColName'] == 'birthDate' && false !== strtotime(trim($_POST[$requestFieldID]))) {
                            $fieldColValues .= strtotime($_POST[$requestFieldID]) . ",";
                            $fieldColNames .= "" . $dbField['dbColName'] . ", ";
                        } else if ($dbField['dbColName'] == 'paymentDate' || $dbField['dbColName'] == 'formDate') {
                            if (trim($_POST[$requestFieldID]) != "") {
                                $fieldColValues .= strtotime(trim($_POST[$requestFieldID])) . ",";
                                $fieldColNames .= "" . $dbField['dbColName'] . ",";
                            }
                        } else if (isset($_POST[$requestFieldID])) {
                            $fieldColValues .= "'" . $db->escape($_POST[$requestFieldID], $dbField['stripTags']) . "',";
                            $fieldColNames .= "" . $dbField['dbColName'] . ",";
                        }
                    }
                }

                //trim the extra comma off the end of both of the above vars
                $fieldColNames = rtrim($fieldColNames, ",");
                $fieldColValues = rtrim($fieldColValues, ",");

                //$regKey = strtoupper(substr(str_replace("'", "", $_POST["RQvalALPHLast_Name"]), 0, 2)) . "-000I-" . $newCount;
                $regKey = $aReg->createIntermediateRegKey($_POST["RQvalALPHLast_Name"]);
                $qry = sprintf("INSERT INTO %s (%s, sysDateCreated, sysOpen, membershipType, registrationKey, sessionID, beginnerID) VALUES (%s, NOW(),  '1', 'foundation','%s', 'I', 0)", (string) $primaryTableName, (string) $fieldColNames, (string) $fieldColValues, $regKey);
                
                $res = $db->query($qry);

                if ($db->affected_rows($res) == 1) {
                    $insID = $db->insert_id();
                    if ($aReg->validatePayments($_POST["payment"][0], $_POST["payment"][1], $_POST['payment'][2]) === true){
                        $aReg->createIntermediatePayment($_POST["payment"], $insID, $user->id);
                    }
                   header('Location:/admin/apps/registration/intermediate-registration?Insert=true');
                } else {
                    echo "Insert did not work";
                }

                break;


            case "update":

                //this default update query will work for most single table interactions, you may need to cusomize your own
                $fieldColNames = '';
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
                        } else if ($dbField['dbColName'] == 'birthDate' && !empty($_POST[$requestFieldID])) {
                            $fieldColValue = strtotime($_POST[$requestFieldID]) . ",";
                            $fieldColNames .= "" . $dbField['dbColName'] . " = " . $fieldColValue;
                        } else if ($dbField['dbColName'] == 'paymentDate' && trim($_POST[$requestFieldID]) != "") {
                            $fieldColValue = strtotime(trim($_POST[$requestFieldID])) . ",";
                            $fieldColNames .= "" . $dbField['dbColName'] . " = " . $fieldColValue;
                        } else if ($dbField['dbColName'] == 'formDate' && trim($_POST[$requestFieldID]) != "") {
                            $fieldColValue = strtotime(trim($_POST[$requestFieldID])) . ",";
                            $fieldColNames .= "" . $dbField['dbColName'] . " = " . $fieldColValue;
                        } else if ($dbField['dbColName'] == 'province') {
                            $fieldColValue = "'" . $_POST[$requestFieldID] . "',";
                            $fieldColNames .= "" . $dbField['dbColName'] . " = " . $fieldColValue;
                        } else if (isset($_POST[$requestFieldID])) {
                            $fieldColValue = "'" . $db->escape($_POST[$requestFieldID], $dbField['stripTags']) . "',";
                            $fieldColNames .= "" . $dbField['dbColName'] . " = " . $fieldColValue;
                        }
                    }
                }

                //trim the extra comma off the end of the above var
                $fieldColNames = substr($fieldColNames, 0, strlen($fieldColNames) - 1);

                $qry = sprintf("UPDATE %s SET %s WHERE itemID = '%s'", (string) $primaryTableName, (string) $fieldColNames, (int)$_POST['id']);

                $res = $db->query($qry);

                if ($db->affected_rows($res) == 1 || $db->error() === false) {
                    if ($aReg->validatePayments($_POST["payment"][0], $_POST["payment"][1], $_POST['payment'][2]) === true){
                        $aReg->createIntermediatePayment($_POST["payment"], (int)$_POST['id'], $user->id);
                    }
                    if (isset($_POST['radioPayment'])){
                            $editPayments = (isset($_POST["editPayment"])) ? $_POST["editPayment"] : array();
                            $aReg->editIntermediatePayment($_POST['radioPayment'], $editPayments, (int)$_POST['id']);
                    }
                   header('Location:/admin/apps/registration/intermediate-registration?Update=true');
                } else {
                    echo "Update did not work";
                }

                break;

            case "delete":

                //this delete query will work for most single table interactions, you may need to cusomize your own

                $db->query(sprintf("UPDATE %s SET `sysStatus` = 'inactive', `sysOpen` = '0' WHERE itemID = %d", (string) $primaryTableName, (int) $db->escape($_GET['id'], true)
                        ));

                header('Location:/admin/apps/registration/intermediate-registration');
                break;
        }
    } else {
        $_GET['view'] = 'edit';
    }

    include $root . "/admin/templates/header.php";
    ?>
    <h1>Intermediate Registration</h1>
    <p>This allows the ability to register/edit fencers for the intermediate class.</p>

    <div class="boxStyle">
        <div class="boxStyleContent">
            <div class="boxStyleHeading">
                <h2>Edit</h2>
                <div class="boxStyleHeadingRight">
    <?php print "<input class='btnStyle blue' type=\"button\" name=\"newItem\" id=\"newItem\" onclick=\"javascript:window.location.href='/admin/apps/registration/intermediate-registration?view=edit';\" value=\"New Member\" title=\"Add a New Intermediate Member\"/>"; 
            print "<input class='btnStyle blue' type=\"button\" name=\"begItem\" id=\"begItem\" onclick=\"javascript:window.location.href='/admin/apps/registration/intermediate-registration?view=beg';\" value=\"Add Beginner\" title=\"Register Beginners to the Intermediate Class\"/>";
            print "<input class='btnStyle blue' type=\"button\" name=\"clubItem\" id=\"begItem\" onclick=\"javascript:window.location.href='/admin/apps/registration/intermediate-registration?view=club';\" value=\"Advance Members\" title=\"Register Intermediates as Club Members\"/>";
    ?>
                </div>
            </div>
            <div class="clearfix">&nbsp;</div>
            <div id="template">
    <?php
    //display logic
    //view = view state, these standard views will do for most single table interactions, you may need to replace with your own
    if (!isset($_GET['view'])) {
        $_GET['view'] = null;
    }

    switch ($_GET['view']) {
        case "beg":
            //include new file to show beginner list
            //class is over and beginnerID not in  `tblIntermediateRegistrations`
            include_once(__DIR__."/beginnerToIntermediate.php");
            break;
        case "club":
            //include new file to show beginner list
            //class is over and beginnerID not in  `tblIntermediateRegistrations`
            include_once(__DIR__."/intermediateToClub.php");
            break;
        case "edit": //show an editor for a row (existing or new)
            //determine if we are editing an existing record, otherwise this will be a 'new'

            $dbaction = "insert";

            $_GET['id'] = intval($_GET['id'], 10);
            
            $payments = array();

            if (is_numeric($_GET['id'])) { //if an ID is provided, we assume this is an edit and try to fetch that row from the single table
                $qry = sprintf("SELECT tp.* FROM $primaryTableName AS tp WHERE tp.`itemID` = '%d' AND tp.`sysOpen` = '1'", (int)$_GET['id']);
                $res = $db->query($qry);

                if ($db->valid($res)) {
                    $fieldValue = $db->fetch_assoc($res);
                    foreach ($fields as &$itemField) {
                        //if (is_string($itemField['dbColName'])) {
                        $itemField['dbValue'] = $fieldValue[$itemField['dbColName']];
                        //}
                    }

                    $dbaction = "update";
                    
                    $qryP = sprintf("SELECT `paymentAmount`, `paymentDate`, `paymentType`, `itemID` FROM `tblIntermediatePayments` 
                        WHERE `sysOpen` = '1' AND `registrationID` = '%d' ORDER BY `paymentDate` DESC",
                            (int)$_GET['id'] 
                     );
                    $resP = $db->query($qryP);
                    if ($db->valid($resP)){
                        while ($rowP = $db->fetch_assoc($resP)){
                            $payments[trim($rowP["itemID"])] = array(
                                'date'      => trim($rowP["paymentDate"]), 
                                'amount' => trim($rowP["paymentAmount"]),
                                'type'      => trim($rowP["paymentType"])
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

            $formBuffer = "<form enctype=\"multipart/form-data\" name=\"tableEditorForm\" id=\"tableEditorForm\" method=\"post\" action=\"/admin/apps/registration/intermediate-registration?" . $_SERVER['QUERY_STRING'] . "\">
            <table>";

            //print the base fields
            $f = 0;

            foreach ($fields as $field) {

                $formBuffer .= "<tr>";
                //prepare an ID and Name string with a validation string in it

                if ($field['dbColName'] != false) {

                    $newFieldIDSeed = str_replace(" ", "_", $field['label']);
                    
                    /*for insert only, change it so only ln, fn and email are required. this allows admin to create a record from email 
                     * and get the user to fill out the remainder of the form online using registration form previously created
                     * */
                    $newFieldID = $field['valCode'] . $newFieldIDSeed;

                    $field['widgetHTML'] = str_replace("FIELD_ID", $newFieldID, $field['widgetHTML']);

                    //set value if one exists
                    if ($field['dbColName'] == 'sysStatus') {
                        if ($field['dbValue'] == 'active') {
                            $field['widgetHTML'] = str_replace("FIELD_VALUE", 'checked="checked"', $field['widgetHTML']);
                        } else {
                            $field['widgetHTML'] = str_replace("FIELD_VALUE", '', $field['widgetHTML']);
                        }
                    } else if ($field['dbColName'] == "province") {
                        $field['dbValue'] = (isset($_POST[$newFieldID]) && $message != '') ? $_POST[$newFieldID] : 'ON';
                        $field['widgetHTML'] = '<select name="' . $newFieldID . '" id="' . $newFieldID . '">';
                        foreach ($provs as $prov) {
                            $field['widgetHTML'] .= '<option value="' . $prov . '"' . ($field['dbValue'] == $prov ? 'selected="selected"' : '') . '>' . $prov . ($field['dbValue'] == $prov ? '*' : '') . '</option>';
                        }
                        $field['widgetHTML'] .= '</select>';
                    } else if ($field['dbColName'] == "gender") {
                        $field['dbValue'] = (isset($_POST[$newFieldID]) && $message != '') ? $_POST[$newFieldID] : $field['dbValue'];
                        $field['widgetHTML'] = '<select name="' . $newFieldID . '" id="' . $newFieldID . '">';
                        foreach ($gender as $gAbbr => $sex) {
                            $field['widgetHTML'] .= '<option value="' . $gAbbr . '"' . ($field['dbValue'] == $gAbbr ? 'selected="selected"' : '') . '>' . $sex . ($field['dbValue'] == $gAbbr ? '*' : '') . '</option>';
                        }
                        $field['widgetHTML'] .= '</select>';
                    } else if (stristr($field['dbColName'], "date") !== false) {
                        if (isset($_POST[$newFieldID]) && $message != '') {
                            $field['dbValue'] = $_POST[$newFieldID];
                        }
                        $field['dbValue'] = ($field['dbValue'] != "" && $field['dbValue'] != 0) ? date('Y-m-d', $field['dbValue']) : '';
                        $field['widgetHTML'] = str_replace("FIELD_VALUE", $field['dbValue'], $field['widgetHTML']);
                    } else {
                        if (isset($_POST[$newFieldID]) && $message != '') {
                            $field['dbValue'] = $_POST[$newFieldID];
                        }
                        $field['widgetHTML'] = str_replace("FIELD_VALUE", $field['dbValue'], $field['widgetHTML']);
                    }
                }

                //write in the html
                $formBuffer .= "<td valign=\"top\" width=\"30%\"><label for=\"" . $newFieldID . "\">" . $field['label'] . "</label></td><td>" . $field['widgetHTML'] . " <p>" . $field['tooltip'] . "</p></td>";
                $formBuffer .= "</tr>";
            }

            //temp
            $id = null;
            $formAction = null;
            //end temp

            $formBuffer .= "<tr><td colspan='2'>
            <input type=\"hidden\" name=\"nonce\" value=\"" . Quipp()->config('security.nonce') . "\" />
                <input type=\"hidden\" name=\"dbaction\" id=\"dbaction\" value=\"$dbaction\" />";

            if ($dbaction == "update") { //add in the id to pass back for queries if this is an edit/update form
                $formBuffer .= "<input type=\"hidden\" name=\"id\" id=\"id\" value=\"" . $_GET['id'] . "\" />";
            }

            $formBuffer .= "</td></tr>";
            $formBuffer .= "</table>";
            $formBuffer .= "<p>&nbsp;</p><h3>Payments</h3><p>&nbsp;</p>";
            $formBuffer .=  '<table id="payments">';
            $formBuffer .= '<tr><td width="30%"><label for="paymentDate">New Payment Date</label></td><td><input type="text" name="payment[]" id="paymentDate" class="uniform datepicker" style="width:300px" /></td></tr>';
            $formBuffer .= '<tr><td><label for="paymentAmount">New Payment Amount</label></td><td><input type="text" name="payment[]" id="paymentAmount" class="uniform" style="width:300px" /></td></tr>';
            $formBuffer .= '<tr><td><label for="paymentType">New Payment Type</label></td><td><select name="payment[]" id="paymentType"><option value="monthly">Monthly</option><option value="card">Session Card</option><option value="drop-in">Drop-In</option></select></td></tr>';
            $formBuffer .= '</table><p>&nbsp;</p><table id="history"><tr><td colspan="2"><strong>Payment History</strong></td></tr>';
            if (!empty($payments)){
                foreach($payments as $pID => $nfo){
                    $formBuffer .= '<tr>';
                    $formBuffer .= '<td width="30%"><input type="radio" name="radioPayment['.$pID.']" value="edit" id="editPayment_'.$pID.'" /><label for="editPayment_'.$pID.'">Edit</label>';
                    $formBuffer .= '<input type="radio" name="radioPayment['.$pID.']" value="delete" id="deletePayment_'.$pID.'" /><label for="deletePayment_'.$pID.'">Delete</label>';
                    $formBuffer .= '<input type="radio" name="resetPayment_'.$pID.'" id="resetPayment_'.$pID.'" /><label for="resetPayment_'.$pID.'">Reset</label></td>';
                    $formBuffer .= '<td><label for="editDate_'.$pID.'">Date</label>&nbsp;<input type="text" name="editPayment['.$pID.'][]" id="editDate_'.$pID.'" class="uniform datepicker" style="width:150px" disabled="disabled" value="'.date("Y-m-d",$nfo["date"]).'"/>';
                    $formBuffer .= '&nbsp;<label for="editAmount_'.$pID.'">Amount</label>&nbsp;<input type="text" name="editPayment['.$pID.'][]" id="editAmount_'.$pID.'" class="uniform" style="width:150px" disabled="disabled" value="'.number_format($nfo['amount'],2).'"/>';
                    $formBuffer .= '<label for="editType_'.$pID.'">Type</label><select name="editPayment['.$pID.'][]" id="editType_'.$pID.'" disabled="disabled">';
                    foreach($payOpts as $pType => $pLabel){
                        $formBuffer .= '<option value="'.$pType.'"'.($pType == $nfo['type'] ? 'selected="selected"' : '').'>'.$pLabel.($pType == $nfo['type'] ? '*' : '').'</option>';
                    }
                    $formBuffer .= '</select></td></tr>';
                }
            }
            else{
                $formBuffer .= '<tr><td colspan="2">No Payment History</td></tr>';
            }
            $formBuffer .= '</table>';
            $formBuffer .= "<div class=\"clearfix\" style=\"margin-top: 10px; height:10px; border-top: 1px dotted #B1B1B1;\">&nbsp;</div>";
            $formBuffer .= "<input class='btnStyle grey' type=\"button\" name=\"cancelUserForm\" id=\"cancelUserForm\" onclick=\"javascript:window.location.href='/admin/apps/registration/intermediate-registration'\" value=\"Cancel\" />
		<input class='btnStyle green' type=\"submit\" name=\"submitUserForm\" id=\"submitUserForm\" value=\"Save Changes\" />";
            $formBuffer .= "</form>";
            //print the form
            print $formBuffer;
            break;
        default: //(list)
            //list table query:
?>
               <p><strong>To send a link to the registration form via the emailer:</strong><br />http://londonfencing.ca/intermediate-registration/%REGKEY%<br />&nbsp;</p>
               <p><strong>To send a link to the printable consent form via emailer:</strong><br />http://londonfencing.ca/print-reg/I/%REGKEY%</p>
               <p>&nbsp;</p>
               <form action="<?php echo $_SERVER["REQUEST_URI"];?>">
                      <select name="filter" >
                      <option value="">Choose Filter</option>
                      <option value="inactive"<?php echo ($filter == 'inactive' ? 'selected="selected"' : '');?>>Inactive Members<?php echo ($filter == 'inactive' ? '*' : '');?></option>
                      <option value="all"<?php echo ($filter == 'all' ? 'selected="selected"' : '');?>>All Members<?php echo ($filter == 'all' ? '*' : '');?></option>
                      </select>
                      <input type="button" name="goFilter" value="Filter" class="btnStyle blue" style="float:none"/>
                     <input type="button" name="rmFilter" value="Clear" class="btnStyle" onclick="javascript:window.location='<?php echo preg_replace('%\?filter=(inactive|all)?%','',$_SERVER["REQUEST_URI"]);?>'" style="float:none"/>
                </form>
                <p>&nbsp;</p>
<?php

            $where = ($filter == 'active' || $filter == "inactive")? " AND cr.`sysStatus` = '".$filter."'" : "";
            $listqry = sprintf("SELECT cr.`itemID`, concat(cr.`lastName`, ', ' ,cr.`firstName`) AS name, cr.`email`, cr.`formDate`, 
                UNIX_TIMESTAMP(cr.`sysDateCreated`) as dateReg, 
                    (SELECT MAX(p.`paymentDate`) FROM `tblIntermediatePayments` AS p WHERE p.`registrationID` = cr.`itemID`) AS paymentDate, 
                    cr.`registrationKey`, 
                    cr.`sysStatus`,
                    cr.`parentName`
                FROM $primaryTableName AS cr 
                WHERE cr.`sysOpen` = '1' %s
                ORDER BY cr.`sysStatus`, cr.`itemID` ASC", 
                    $where
            );

            $resqry = $db->query($listqry);
            if (is_object($resqry) && $resqry->num_rows > 0) {
                //list table field titles
                $titles[0] = "Name";
                $titles[1] = "Registration Number";
                $titles[2] = "Email Address";
                $titles[3] = "Parent/Guardian";
                $titles[4] = "Date Registered"; //temp removed <td>' . date('Y-m-d', $dt["dateReg"]) . '</td><td>' . date('Y-m-d', $dt["dateReg"]) . '</td>
                $titles[5] = "Last Payment Date";
                $titles[6] = "Form Submitted";

                while ($rs = $db->fetch_assoc($resqry)) {
                        $registered[] = $rs;
                }
                echo '<form name="frmSendEmail" action="/admin/apps/notificationManager/emailer" method="post" enctype="multipart/form-data">';
                echo '<table id="adminTableList_reg" class="adminTableList tablesorter" width="100%" cellpadding="5" cellspacing="0" border="1">';
                echo '<thead><tr><th>' . $titles[0] . '</th><th>' . $titles[1] . '</th><th>' . $titles[2] . '</th><th>' . $titles[3] . '</th><th>' . $titles[5] . '</th><th>' . $titles[6] . '</th><th>Status</th>
                <th>Email<input type="checkbox" id="emailAll" name="emailAll" value="all" /></th><th>&nbsp;</th><th>&nbsp;</th></tr></thead>';
                echo '<tbody>';
                foreach ($registered as $dt) {
                    $paymentDate = (trim($dt["paymentDate"]) != '' && $dt["paymentDate"] > 0) ? date('Y-m-d', $dt["paymentDate"]) : "Due";
                    $formDate = (trim($dt["formDate"]) != '' && $dt["formDate"] > 0) ? date('Y-m-d', $dt["formDate"]) : "Due";
                    echo '<tr><td>' . $dt['name'] . '</td><td>' . $dt["registrationKey"] . '</td><td>' . $dt["email"] . '</td><td>'.$dt["parentName"].'</td><td>' . $paymentDate . '</td><td>' . $formDate . '</td><td>'.$dt["sysStatus"].'</td>';
                    echo '<td style="width:70px;"><input type="checkbox" name="eList[]" id="eList_' . trim($dt['itemID']) . '" value="' . trim($dt['itemID']) . '" /></td>';
                    echo '<td style="width:40px;"><input class="btnStyle red noPad" id="btnDelete_' . $dt['itemID'] . '" type="button" onclick="javascript:confirmDelete(\'?action=delete&amp;id=' . $dt['itemID'] . '\');" value="Delete"></td>';
                    echo '<td style="width:40px;"><input class="btnStyle blue noPad" id="btnEdit_' . $dt['itemID'] . '" type="button" onclick="javascript:window.location=\'?view=edit&amp;id=' . $dt['itemID'] . '\';" value="Edit"></td></tr>';
                }
                echo '</tbody>
            <tbody>
            <tr><td colspan="10">
            <input  style="float:right" class="btnStyle green noPad" id="btnPrint" type="button" onclick="javascript:window.open(\'/admin/apps/registration/print-reg-list?sid=I\');" value="Print">
            <input  style="float:right" class="btnStyle blue noPad" id="btnSelect" type="submit" value="Send Email">
            <input type="hidden" name="nonce" value="' . Quipp()->config('security.nonce') . '" />
            <input type="hidden" name="etype" value="int-reg" />
            </td>
                </tr>
            </tbody>
            </table>';
                echo '</form>';
            } else {
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


include $root . "/admin/templates/footer.php";
} else {
$auth->boot_em_out();
}