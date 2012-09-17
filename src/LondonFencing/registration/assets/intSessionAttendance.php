<?php
require_once('../../../../inc/init.php');
require_once dirname(__DIR__).'/Apps/AdminRegister.php';

use LondonFencing\registration\Apps AS REG;

if ($auth->has_permission("canCreateReports") && isset($_POST["sessionStart"]) && isset($_POST["sessionEnd"]) && strtotime($_POST["sessionEnd"]) > strtotime($_POST["sessionStart"])){
    
    $adm = new REG\AdminRegister(false, $db);
    $attendance = $adm->getSessionCardsInt($_POST["sessionStart"], $_POST["sessionEnd"]);

?>
<!doctype html>
<html>
    <head>
        <title>Quipp &bull; Session Card List</title>
        <meta charset="utf-8">
        <meta name="robots" content="noindex,nofollow">
        <link rel="stylesheet" href="/admin/css/reset.css">
        <link rel="stylesheet" href="/admin/css/admin.css">
        <style type="text/css" media="all">
            body{width:640px;margin:0px auto;background:none}
            div:first-child{margin:20px 0 20px 0}
            div.attendees{width:100%;padding:10px 5px 15px 5px;border:1px solid #bbb;height: 55px;}
            ul{list-style:none;vertical-align:middle;margin: 20px 0;}
            li:first-child{display:inline-block;font-size:16px;width:180px}
            li.stamps{display:inline;text-align:center;padding:20px;border:1px solid #ccc;color:#ccc;font-size:18px;margin-right:5px}
            .pgBrk{page-break-after: always;}
        </style>
    </head>
    <body>
        <div>
        <h2>Intermediate Session Cards<span style="text-align:right;margin-left:200px">
            <?php echo date('M j, Y', strtotime($_POST["sessionStart"])).' - '. date('M j, Y', strtotime($_POST["sessionEnd"])); ?></span></h2>
        </div>
        <?php
        for($a = 0; $a < count($attendance); $a++){
            $class = ($a == 9 || ($a > 9 && $a % 9 == 0)) ? " pgBrk":"" ;
            echo '<div class="attendees'.$class.'">';
            echo '<ul>';
            echo '<li>'.$attendance[$a].'</li>';
            echo '<li class="stamps">1</li>';
            for ($i = 2; $i < 9; $i++){
                echo '<li class="stamps">'.$i.'</li>';
            }
            echo '</div>';
            if ($a == 9 || ($a > 9 && $a % 9 == 0)){
?>
            <div>
        <h2>Intermediate Session Cards<span style="text-align:right;margin-left:200px">
            <?php echo date('M j, Y', strtotime($_POST["sessionStart"])).' - '. date('M j, Y', strtotime($_POST["sessionEnd"])); ?></span></h2>
        </div>
<?php
            }
        }
        ?>
    </body>
</html>
<?php
}
else{
    echo '<p>No permission</p>';
}