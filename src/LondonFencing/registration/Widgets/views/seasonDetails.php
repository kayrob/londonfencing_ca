<?php
require_once dirname(dirname(__DIR__)) . "/registration.php";

use LondonFencing\registration as Reg;

$reg = new Reg\registration($db);

$sessionNfo = $reg->getAdvancedSeason();
?>
<div id="regSideNav">
    <div class="blankMainHeader"><h2>Season: <?php echo date('Y', $sessionNfo['seasonStart']) . "-" . date('Y', $sessionNfo['seasonEnd']); ?></h2></div>
    <ul>
        <li><span>Start Date</span><br /><?php echo date('M j, Y', $sessionNfo['seasonStart']); ?></li>
        <li><span>End Date</span><br /><?php echo date('M j, Y', $sessionNfo['seasonEnd']); ?></li>
        <li><span>Coach</span><br /><?php echo (trim($sessionNfo['headCoach']) == '' ? 'TBA' : $sessionNfo['headCoach']); ?></li>
        <li><span>Fees:</span><br />Annually:&nbsp;&nbsp;<?php echo sprintf('$%001.2f', trim($sessionNfo['annualFee'])); ?><br />
            Quarterly:&nbsp;<?php echo sprintf('$%001.2f', trim($sessionNfo['quarterlyFee'])); ?><br />
            Monthly:&nbsp;&nbsp;&nbsp;&nbsp;<?php echo sprintf('$%001.2f', trim($sessionNfo['monthlyFee'])); ?><br />
        </li>
    </ul>
</div>
<p style="margin-top: 30px">
    <?php
    if ($_GET['p'] != "club-registration") {
        echo '<a href="/club-registration" class="btnStyle">Login/Register</a>';
    }
    else{
        echo '<a href="/logout" class="btnStyle">Logout</a>';
    }
?>
</p>