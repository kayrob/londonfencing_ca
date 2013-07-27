<?php
if (isset($_GET["eqt"]) && preg_match('%^[a-fA-F0-9]{40}$%', $_GET['eqt'], $matches)){
    require_once(dirname(dirname(dirname(__DIR__))) ."/inc/init.php");
    
    $updated = true;
    if (isset($_POST["status"]) && preg_match('%^[01]{1}$%', $_POST["status"], $matchStatus)){
        $db->query(sprintf(
                "UPDATE `tblEquipment` SET `functionStatus` = '%d', `dateLastUpdated` = %d, `lastUpdatedBy` = 'Web' 
                    WHERE qrLink = 'eqt-%s' AND `functionStatus` = '%d'", 
                    $matchStatus[0],
                    time(),
                    $db->escape($matches[0]),
                    ($matchStatus[0] == 0 ? 1 : 0)
        ));
        if ($db->error() > 0){
            print_r($db->error());
            $updated = false;
        }
    }
    
    $res = $db->query(sprintf(
            "SELECT * FROM `tblEquipment` WHERE `qrLink` = '%s'",
            'eqt-' . $db->escape($matches[0])
     ));
    $row = array();
    if ($db->valid($res) !== false){        
        $row = $db->fetch_assoc($res);
    }
    $showQuippBrand = " class=\"quippBranding\"";
    $statuses = array("Not Working", "Working", "Under Repair", "Retired");
?>
<!doctype html>
<html>
    <head>
        <title>Equipment Update</title>
        <link rel="stylesheet" href="/themes/LondonFencing/default.css" media="screen" type="text/css"/>
        <style type="text/css">
            .alertBoxFunctionBad{width: 280px;float: none;margin-top: 0px;}
            #loginBox{width: 90%; text-align:center}
            #loginBox img{margin-left: 105px;}
            td{padding-bottom: 20px;}
            td:nth-child(odd){text-align:right;padding-right: 20px}
            td:nth-child(even){text-align:left;padding-left: 20px}
            body{font-size: 28px}
            .btnStyle {font-size: 28px}
        </style>
    </head>
<body>
        <div id="loginBox" <?php print $showQuippBrand; ?>>
            <div class="loginBoxHead"><img src="/themes/LondonFencing/img/logo.png" alt="Logo" /></div>
<?php
    if (empty($row)){
        echo '<p>This code is not valid. Login to the admin to update equipment status</p>';
    }
    else{
        $newStatus = ($row["functionStatus"] == "1") ? "0" : "1";
?>
           <form action="<?php print $_SERVER['REQUEST_URI']; ?>" id="loginBoxForm" method="post">
<?php
                if ($updated == false){
                    echo '<p><strong>There was an error updating the status of this equipment</strong></p>';
                }
?>
                <table width="100%">
                    <tr><td width="50%">Equipment ID:</td><td><strong><?php echo $row["itemID"];?></strong></td></tr>
                    <tr><td>Status:</td><td><strong><?php echo $statuses[$row["functionStatus"]];?></strong></td></tr>
                    <tr><td>Equipment Type:</td><td><?php echo $row["type"];?></td></tr>
                    <tr><td>Company:</td><td><?php echo $row["company"];?></td></tr>
                    <tr><td>Last Updated:</td><td><?php echo date("Y-m-d g:i a", $row["dateLastUpdated"]);?></td></tr>
                    <tr><td>Last Updated By:</td><td><?php echo $row["lastUpdatedBy"];?></td></tr>
                </table>
                <input type="hidden" name="status" value="<?php echo $newStatus;?>" />
                <input type="hidden" name="nonce" value="<?php echo Quipp()->config('security.nonce'); ?>" />
   <?php
                if ((int) $row["functionStatus"] < 2){
   ?>
                <div id="loginBoxButtons">
                    <input type="submit"  value="Mark as <?php echo $statuses[$newStatus];?>" class="btnStyle" />
                </div>
   <?php
                }
                else{
                    echo '<p><em>This item is listed as ' .$statuses[$row["functionStatus"]]. '. Check with the armorer or login to update equipment.</em></p>';
                }
   ?>
            </form>
        </div>
 <?php
    }
 ?>
</body>
</html>
<?php
    }
    else{
        header("location: /");
    }