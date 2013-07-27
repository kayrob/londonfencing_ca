<?php
 
$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require $root . '/inc/init.php';
$hasPermission = false;
if ($auth->has_permission("canEditEquipment")){
    $hasPermission = true;
}

if ($hasPermission && isset($_GET['id']) && is_numeric($_GET['id'])) {
?>    
<!DOCTYPE>   
<html>
    <head>
        <title>Quipp &bull; London Fencing Club &bull; Equipment</title>
        <style type="text/css">
            body{
                width: 700px;
                margin: 0 auto;
            }
            td {
                vertical-align: top;
                font-size: 96px;
                padding-right: 10px;
                text-align: center;
            }
            table{
                width: 100%;
                margin-top: 40px;
            }
            .breaker{ page-break-after: always;}
        </style>
    </head>
    <body>
<?php
    $where = ((int) $_GET['id'] > 0) ? sprintf("`itemID` = %d AND", (int) $_GET["id"]): "";
    $res = $db->query(sprintf("SELECT `itemID`,`qrcode` FROM `tblEquipment` 
            WHERE %s `sysStatus` = 'active'", 
        $where));

    if ($db->valid($res) !== false){
?>
        <table>
            <tr>
<?php
        $j = 0;
        while ($row = $db->fetch_assoc($res)){
            if (file_exists(Quipp()->config('upload_dir').'/equipment/'.$row["qrCode"])){
                if ($j > 0 && $j % 14 == 0){
                    echo '</tr></table>';
                    echo '<div class="breaker"></div>';
                    echo '<table><tr>';
                }
                else if ($j % 2 == 0 && $j > 0){
                    echo "</tr><tr>";
                }
?>
                    <td width="50%"><?php echo $row["itemID"];?>
                    <img src="/uploads/equipment/<?php echo $row["qrcode"];?>" width="100px" height="100px" /></td>
<?php
                $j++;
            }
            
        }
        if ($j % 2 > 0){
            echo "<td>&nbsp;</td>";
        }
?>
        </tr>
     </table>   
<?php
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