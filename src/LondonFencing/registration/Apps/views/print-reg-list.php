<?php
$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require $root . '/inc/init.php';

$meta['title'] = 'Registration Submission Manager';
$meta['title_append'] = ' &bull; Quipp CMS';

$hasPermission = false;
if ($auth->has_permission("canEditReg")){
    $hasPermission = true;
}

if ($hasPermission && isset($_GET['sid'])) {
    if (is_numeric($_GET['sid'])){
    $begTable = (isset($_GET["app"]) && $_GET["app"] == "discover") ? "tblDiscover" : "tblClasses";
    $begTableReg = (isset($_GET["app"]) && $_GET["app"] == "discover") ? "tblDiscoverRegistration" : "tblClassesRegistration";
    $listqry = sprintf("SELECT cr.*, c.`level`, c.`sessionName` 
                FROM `%s` AS cr INNER JOIN `%s` AS c ON cr.`sessionID` = c.`itemID` 
                WHERE cr.`sessionID` = %d AND cr.`isRegistered` = 1 AND cr.`sysStatus` = 'active'
                ORDER BY cr.`lastName` ASC, cr.`firstName` ASC", 
            $begTableReg,
            $begTable,
          (int)$db->escape($_GET['sid'],true)
     );
    }
    else if ($_GET['sid'] == "I"){
        $listqry = sprintf("SELECT i.`lastName`, i.`firstName`, i.`formDate` , p.`paymentDate`
                FROM `tblIntermediateRegistration` AS i
                LEFT JOIN `tblIntermediatePayments` AS p ON i.`itemID` = p.`registrationID`
                WHERE i.`sysStatus` = 'active' AND (p.`paymentDate` IS NULL OR (p.`paymentDate` >= %d)) 
                ORDER BY i.`lastName` ASC, i.`firstName` ASC",
                mktime(0,0,0,date('n'),1,date('Y'))
            );
    }
    $res = $db->query($listqry);
    if ($res->num_rows > 0){
           while ($row = $db->fetch_assoc($res)){
               $rs[] = $row;
           }
    }
    if (isset($rs)){
?>
<!doctype html>
<html>
    <head>
    <title>London Fencing Club Class List</title>
    <style type="text/css" media="all">
        body{font-family: Arial,Verdana, Sans-serif; font-size: 12px;color:#000;width:740px;margin:0}
        p{font-size:16px}
        table{width:710px; margin:10px 15px 0 15px;border-collapse: collapse}
        td{padding:5px;border:1px solid #ccc;}
        h1{font-size:16px; font-weight:bold}
        .thEquip{background-color:#d6d6d6;text-align:center;text-transform: uppercase}
        p.aPrint{margin:20px 0 0 15px; font-size:16px}
    </style>
    <style type="text/css" media="print">p.aPrint{display:none;}</style>
    </head>
    <body>
        <h1><?php echo (!isset($rs[0]["sessionName"]) ? "Intermediate" :  $rs[0]["sessionName"]." - (".ucwords($rs[0]["level"]).")");?></h1>
        <table>
            <thead>
                <tr><th colspan="4">&nbsp;</th><th colspan="4" class="thEquip">Equipment</th></tr>
                <tr><th>Last Name</th><th>First Name</th><th>Payment Status</th><th>Form Submitted</th><th>Face Mask</th><th>Glove</th><th>Jacket</th><th>Plastron</th></tr>
            </thead>
            <tbody>
                <?php
                foreach ($rs as $student){
                    $paid = (trim($student["paymentDate"]) != "" && trim($student["paymentDate"]) != 0)?"Paid In Full":"";
                    $formSub = (trim($student["formDate"]) != "" && trim($student["formDate"]) != 0)?date('Y-m-d',trim($student["formDate"])):"";
                    echo '<tr>';
                    echo '<td>'.trim($student["lastName"]).'</td><td>'.trim($student["firstName"]).'</td><td>'.$paid.'</td>';
                    echo '<td>'.$formSub.'</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
        <p class="aPrint"><a href="javascript:window.print()" >Print</a></p>
    </body>
</html>
<?php
        
    }
}
else{
    $auth->boot_em_out(1);
}
?>