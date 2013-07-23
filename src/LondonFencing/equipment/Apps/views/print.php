<?php
 
$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require $root . '/inc/init.php';
$hasPermission = false;
if ($auth->has_permission("canEditEquipment")){
    $hasPermission = true;
}

if ($hasPermission && isset($_GET['id']) && (int) $_GET['id'] > 0) {
?>    
<!DOCTYPE>   
<html>
    <head>
        <title>Quipp &bull; London Fencing Club &bull; Equipment</title>
        <style type="text/css">
            td {
                vertical-align: top;
                font-size: 96px;
                padding-right: 10px
            }
        </style>
    </head>
    <body>
<?php
    $res = $db->query(sprintf("SELECT `itemID`,`qrcode` FROM `tblEquipment` 
            WHERE `itemID` = %d AND `sysStatus` = 'active' AND `sysStatus` = 'active'", 
        (int) $_GET['id']));
   
    if ($db->valid($res) !== false){
        $row = $db->fetch_assoc($res);
        if (file_exists(Quipp()->config('upload_dir').'/equipment/'.$row["qrCode"])){
?>
        <table>
            <tr>
                <td><?php echo $row["itemID"];?></td>
                <td><img src="/uploads/equipment/<?php echo $row["qrcode"];?>" width="200px" height="200px" /></td></tr>
        </table>   
<?php
        }
    }
    else{
        echo '<p>This equipment is no longer in use</p>';
    }
?>
    </body>
</html>
<?php
}
else{
    $auth->boot_em_out(1);
}