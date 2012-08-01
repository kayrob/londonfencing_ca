<?php

/*
 * used to display a list of beginners that can be added to the intermediate class by copying their info
 * only if 1) beginner class is over 2) not already registered in intermediate class
 */
$listqry = sprintf("SELECT cr.`itemID`, concat(cr.`lastName`, ', ' ,cr.`firstName`) AS name, cr.`email`, cr.`registrationKey`,  i.`beginnerID` AS bID
    FROM `tblClassesRegistration` AS cr 
    INNER JOIN `tblClasses` AS  c ON cr.`sessionID` = c.`itemID`
    INNER JOIN `tblCalendarEvents` AS ce ON c.`eventID` = ce.`itemID`
    LEFT JOIN `tblIntermediateRegistration` AS i ON cr.`itemID` = i.`beginnerID`
    WHERE c.`level` = 'beginner' AND UNIX_TIMESTAMP(ce.`eventEndDate`) < UNIX_TIMESTAMP() AND cr.`sysOpen` = '1' 
    AND i.`beginnerID` IS NULL
    ORDER BY name, cr.`itemID` ASC"
);

$resqry = $db->query($listqry);
if (is_object($resqry) && $resqry->num_rows > 0) {
    //list table field titles
    $titles[0] = "Name";
    $titles[1] = "Registration Number";
    $titles[2] = "Email Address";

    while ($rs = $db->fetch_assoc($resqry)) {
        $registered[] = $rs;
    }
    
    echo '<p><strong>Select Beginners to Register into the Intermediate Class</strong><br />&nbsp;</p>';
    
    echo '<form name="frmRollBeginner" action="/admin/apps/registration/intermediate-registration?view=beg" method="post" enctype="multipart/form-data">';
    echo '<table id="adminTableList_reg" class="adminTableList tablesorter" width="100%" cellpadding="5" cellspacing="0" border="1">';
    echo '<thead><tr><th>' . $titles[0] . '</th><th>' . $titles[1] . '</th><th>' . $titles[2] . '</th><th>Add</th></tr></thead>';
    echo '<tbody>';
    foreach ($registered as $dt) {
        echo '<tr><td>' . $dt['name'] . '</td><td>' . $dt["registrationKey"] . '</td><td>' . $dt["email"] . '</td>';
        echo '<td style="width:70px;"><input type="checkbox" name="begList[]" id="begList_' . trim($dt['itemID']) . '" value="' . trim($dt['itemID']) . '" /></td></tr>';
    }
    echo '</tbody>
<tbody>
<tr><td colspan="10">
<input  style="float:right" class="btnStyle noPad" id="btnPrint" type="button" onclick="javascript:window.location=\'/admin/apps/registration/intermediate-registration\'" value="Cancel">
<input  style="float:right" class="btnStyle green noPad" id="btnSelect" type="submit" value="Register">
<input type="hidden" name="nonce" value="' . Quipp()->config('security.nonce') . '" />
    <input  type="hidden" name="dbaction" value="addBeg">
</td>
    </tr>
</tbody>
</table>';
    echo '</form>';
} else {
    echo 'no data present';
}
