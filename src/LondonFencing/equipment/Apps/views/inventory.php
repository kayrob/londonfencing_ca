<?php
 
$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require $root . '/inc/init.php';
require $root . '/admin/classes/Editor.php';
require $root . '/vendors/phpqrcode/qrlib.php';

$meta['title'] = 'Equipment Manager';
$meta['title_append'] = ' &bull; Quipp CMS';

$hasPermission = false;
if ($auth->has_permission("canEditEquipment")){
    $hasPermission = true;
}

if ($hasPermission) {
    
    if (!isset($_GET['id'])) { $_GET['id'] = null; }
    $te = new Editor();
    
    //set the primary table name
    $primaryTableName = "tblEquipment";

    //dbaction = database interactivity, these standard queries will do for most single table interactions, you may need to replace with your own
    if (!isset($_POST['dbaction'])) {
        $_POST['dbaction'] = null;

        if (isset($_GET['action'])) {
            $_POST['dbaction'] = $_GET['action'];
        }
    }
    if (isset($_POST["dbaction"]) && isset($_POST["RQvalALPHtype"]) && is_array($_POST["RQvalALPHtype"])){
        $updates = array();
        $insertData = array();
        foreach($_POST["RQvalALPHtype"] as $itemID => $inventory){
            if (is_numeric($itemID) && (int)$itemID > 0 && !empty($inventory)){
                if (isset($_POST["RQvalALPHcompany"][$itemID]) && isset($_POST["RQvalNUMBfunctionStatus"][$itemID]) && !empty($_POST["RQvalALPHcompany"][$itemID]) && preg_match('%^[0123]{1}$%', $_POST["RQvalNUMBfunctionStatus"][$itemID])){
                    $updates[$itemID] = array(
                        "type"          => $db->escape($inventory),
                        "company"       => $db->escape($_POST["RQvalALPHcompany"][$itemID]),
                        "functionStatus"=> (int) $_POST["RQvalNUMBfunctionStatus"][$itemID]
                    );
                }
            }
            else if (isset($_POST["RQvalALPHcompany"][$itemID]) && isset($_POST["RQvalNUMBfunctionStatus"][$itemID]) && !empty($_POST["RQvalALPHcompany"][$itemID]) && preg_match('%^[0123]{1}$%', $_POST["RQvalNUMBfunctionStatus"][$itemID])){
                    $insertData = array(
                        "type"              => $db->escape($inventory),
                        "company"           => $db->escape($_POST["RQvalALPHcompany"][$itemID]),
                        "functionStatus"    => $_POST["RQvalNUMBfunctionStatus"][$itemID],
                        "qrLink"            => "",
                        "qrCode"            => "",
                        "dateLastUpdated"   => time(),
                        "lastUpdatedBy"     => $user->get_meta("First Name")." ".$user->get_meta("Last Name")
                    );
            }
        }
        $error = false;
        switch ($_POST['dbaction']) {
        case "update":
            if (!empty($updates)){
                foreach ($updates as $item => $update){
                    $qry = sprintf("UPDATE %s SET `type` = '%s', `company` = '%s', `functionStatus` = %d, `dateLastUpdated` = %d, `lastUpdatedBy` = '%s', `sysStatus` = '%s' 
                        WHERE itemID = '%d'", 
                    (string) $primaryTableName, 
                    $update["type"], 
                    $update["company"],
                    $update["functionStatus"],
                    time(),
                    $user->get_meta("First Name")." ".$user->get_meta("Last Name"),
                    ($update["funtionStatus"] < 3 ? 'active' : 'inactive'),
                    (int)$item);
                    $res = $db->query($qry);
                    if ((bool) $db->error() === true){
                        $error = true;
                    }
                }
            }
            if (!empty($insertData)){
                $db->query(sprintf("INSERT INTO %s  (`type`, `company`,`functionStatus`, `dateLastUpdated`, `lastUpdatedBy`, `qrLink`, `qrCode`, `sysStatus`, `sysActive`, `sysDateCreated`) 
                        VALUES ('%s', '%s',  %d, %d, '%s', '', '', 'active', '1', NOW())", 
                    (string) $primaryTableName, 
                    $insertData["type"], 
                    $insertData["company"],
                    $insertData["functionStatus"],
                    time(),
                    $user->get_meta("First Name")." ".$user->get_meta("Last Name")
                    ));
                $newID = $db->insert_id();
                if ($newID > 0){
                    try{
                        $qrLink = sha1($newID . "epeeFTW50");
                        $qrURL = "http://".$_SERVER["SERVER_NAME"]."/eqt-" . $qrLink;
                        $qrCode = Quipp()->config('upload_dir').'/equipment/qrCode_'.$newID.'.png';
                        QRcode::png($qrURL, $qrCode, 'M', 48, 2);
                        if (file_exists($qrCode)){
                            $res = $db->query(sprintf("UPDATE %s SET `qrLink` = '%s', `qrCode` = '%s' WHERE itemID = '%d'", 
                            (string) $primaryTableName, 
                            basename($qrURL), 
                            basename($qrCode),
                            (int)$newID));
                        }
                    }
                    catch(Exception $e){
                        $error = true;
                    }
                }
            }
            break;
         
        }
    } 
include $root. "/admin/templates/header.php";

?>
<h1>Equipment Manager</h1>
<p>This allows the ability to manage the status of club equipment.</p>
<?php
if (!empty($_POST) && isset($error) && $error === true){
    echo '<p>One or more items could not be updated</p>';
}
?>
<div class="boxStyle">
	<div class="boxStyleContent">
		<div class="boxStyleHeading">
			<h2>Edit</h2>
			<div class="boxStyleHeadingRight"></div>
		</div>
		<div class="clearfix">&nbsp;</div>
		<div id="template">

	<?php
    //display logic

    //view = view state, these standard views will do for most single table interactions, you may need to replace with your own
        
    $listqry = sprintf("SELECT `itemID`, `type`, `company`, `qrcode`, `functionStatus`, `dateLastUpdated`, `lastUpdatedBy` FROM $primaryTableName WHERE cast(`sysActive` as UNSIGNED) > 0"
    );
    $resQry = $db->query($listqry);
    
    //list table field titles
    $titles[0] = "Equipment Type";
    $titles[1] = "Company";
    $titles[2] = "QR Code";
    $titles[3] = "Status";
    $titles[4] = "Last Updated";
    $titles[5] = "Last Updated By";

    //print an editor with basic controls
    echo '<form name="frmUpdateEquipment" method = "post" action="/admin/apps/equipment/inventory">';
    echo '<table id="adminTableList" class="adminTableList tablesorter" width="100%" cellpadding="5" cellspacing="0" border="1">';
    echo '<thead><tr><th>Item</th><th>'.$titles[0].'</th><th>'.$titles[1].'</th><th>'.$titles[2].'</th><th>'.$titles[3].'</th><th>'.$titles[4].'</th><th>'.$titles[5].'</th></tr></thead><tbody>';
    if ($db->valid($resQry) !== false){
        while ($row = $db->fetch_assoc($resQry)){
            echo '<tr>
            <td>'.trim($row["itemID"]).'</td>
            <td><input type="text" name="RQvalALPHtype['.$row["itemID"].']" value="'.trim($row["type"]) .'" class="uniform" /></td>
            <td><input type="text" name="RQvalALPHcompany['.trim($row["itemID"]).']" value="'.trim($row["company"]).'" id="RQvalALPHcompany'.trim($row["itemID"]).'" class="uniform"/></td>
            <td><a target="_blank" href="print?id='.$row["itemID"].'"><img src="/uploads/equipment/'.trim($row["qrcode"]) .'" width="48px" height="48px" title="Print" /></a></td>
            <td><select name="RQvalNUMBfunctionStatus['.$row["itemID"].']">
                <option value="0" '.(trim($row["functionStatus"]) == "0" ? "selected=\"selected\"" : "").'>Not Working'.(trim($row["functionStatus"]) == "0" ? "*" : "").'</option>
                <option value="1" '.(trim($row["functionStatus"]) == "1" ? "selected=\"selected\"" : "").'>Working'.(trim($row["functionStatus"]) == "1" ? "*" : "").'</option>
                <option value="2" '.(trim($row["functionStatus"]) == "2" ? "selected=\"selected\"" : "").'>Under Repair'.(trim($row["functionStatus"]) == "2" ? "*" : "").'</option>
                <option value="3" '.(trim($row["functionStatus"]) == "3" ? "selected=\"selected\"" : "").'>Retired'.(trim($row["functionStatus"]) == "3" ? "*" : "").'</option>
                </select></td>
            <td>'.date("Y-m-d g:i a", $row["dateLastUpdated"]).'</td>
            <td>'.trim($row["lastUpdatedBy"]).'</td>
            </tr>';
        }
    }
    echo '<tr><td colspan="7">Add New</td>';
    echo '<tr><td colspan="2"><input type="text" name="RQvalALPHtype[0]" value="" class="uniform" placeholder="Equipment Type" />*</td>
        <td colspan="2"><input type="text" name="RQvalALPHcompany[0]" value="" id="RQvalALPHcompany0" class="uniform" placeholder="Company/Make" />*</td>
        <td colspan="3"><select name="RQvalNUMBfunctionStatus[0]">
            <option value="0">Not Working</option>
            <option value="1">Working</option>
            <option value="2">Under Repair</option>
            <option value="2">Retired</option>
            </select>*</td>
        </tr>';
    echo '</tbody></table>';
    //to pass more advanced controls, you'll need to create your own $fields array and pass it directly to $te->display_editor_list($fields);
    echo '<p><br /><input class="btnStyle green" type="submit" name="submitUserForm" id="submitUserForm" value="Save Changes" />
    <input type="hidden" name="nonce" value="'.Quipp()->config('security.nonce').'" /><input type="hidden" name="dbaction" value="update" /></p>
        </form>';

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