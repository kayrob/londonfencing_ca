<?php
require_once dirname(__DIR__)."/adminRegister.php";

use LondonFencing\Apps\Admin\Register as AReg;

$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require $root . '/inc/init.php';

$meta['title'] = 'Registration Submission Manager';
$meta['title_append'] = ' &bull; Quipp CMS';

$hasPermission = false;
if ($auth->has_permission("canEditReg")){
    $hasPermission = true;
}

if ($hasPermission && isset($_GET['sid']) && is_numeric($_GET['sid'])) {
    $listqry = sprintf("SELECT cr.*, c.`level`, c.`sessionName` 
                FROM `tblClassesRegistration` AS cr INNER JOIN `tblClasses` AS c ON cr.`sessionID` = c.`itemID` 
                WHERE cr.`sessionID` = %d AND cr.`isRegistered` = 1 
                ORDER BY cr.`lastName` ASC, cr.`firstName` ASC", 
          (int)$db->escape($_GET['sid'],true)
     );
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
        <h1><?php echo $rs[0]["sessionName"]." - (".ucwords($rs[0]["level"]).")";?></h1>
        <table>
            <thead>
                <tr><th colspan="3">&nbsp;</th><th colspan="4" class="thEquip">Equipment</th></tr>
                <tr><th>Last Name</th><th>First Name</th><th>Payment Status</th><th>Face Mask</th><th>Glove</th><th>Jacket</th><th>Plastron</th></tr>
            </thead>
            <tbody>
                <?php
                foreach ($rs as $student){
                    $paid = (trim($student["paymentDate"]) != "" && trim($student["paymentDate"]) != 0)?"Paid In Full":"Due";
                    echo '<tr>';
                    echo '<td>'.trim($student["lastName"]).'</td><td>'.trim($student["firstName"]).'</td><td>'.$paid.'</td>';
                    echo'<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
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