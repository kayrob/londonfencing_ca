<?php

/*
 * used to display a list of intermediates that can be added to the club list by copying their info
 * only if 1) they are not already assigned a userID number
 */
$listqry = sprintf("SELECT i.`itemID`, concat(i.`lastName`, ', ' ,i.`firstName`) AS name, i.`email`, i.`registrationKey`
    FROM `tblIntermediateRegistration` AS i 
    WHERE i.`userID` = 0 AND i.`sysStatus` = 'active' AND i.`sysOpen` = '1'
    ORDER BY name, i.`itemID` ASC"
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
    
    echo '<p><strong>Select Intermediates to Register as Regular Club Members</strong><br />&nbsp;</p>';
    
    echo '<form name="frmRollIntermediate" action="/admin/apps/registration/intermediate-registration?view=club" method="post" enctype="multipart/form-data">';
    echo '<table id="adminTableList_reg" class="adminTableList tablesorter" width="100%" cellpadding="5" cellspacing="0" border="1">';
    echo '<thead><tr><th>' . $titles[0] . '</th><th>' . $titles[1] . '</th><th>' . $titles[2] . '</th><th>Add</th></tr></thead>';
    echo '<tbody>';
    foreach ($registered as $dt) {
        echo '<tr><td>' . $dt['name'] . '</td><td>' . $dt["registrationKey"] . '</td><td>' . $dt["email"] . '</td>';
        echo '<td style="width:70px;"><input type="checkbox" name="clubList[]" id="clubList' . trim($dt['itemID']) . '" value="' . trim($dt['itemID']) . '" /></td></tr>';
    }
    echo '</tbody>
<tbody>
<tr><td colspan="10">
<input  style="float:right" class="btnStyle noPad" id="btnPrint" type="button" onclick="javascript:window.location=\'/admin/apps/registration/intermediate-registration\'" value="Cancel">
<input  style="float:right" class="btnStyle green noPad" id="btnSelect" type="submit" value="Register">
<input type="hidden" name="nonce" value="' . Quipp()->config('security.nonce') . '" />
    <input  type="hidden" name="dbaction" value="addClub">
</td>
    </tr>
</tbody>
</table>';
    echo '</form>';
} else {
    echo 'no data present';
}
