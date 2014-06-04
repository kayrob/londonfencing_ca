<?php
require_once dirname(dirname(__DIR__))."/registration.php";
use LondonFencing\registration as Reg;

$reg = new Reg\registration($db);

if (isset($_GET['p']) && preg_match('%(intermediate|beginner)%', $_GET['p'],$matches)){
    
    $sessionNfo = $reg->getRegistrationSession($matches[1]);

    if (isset($sessionNfo['isOpen'])){
        
        $navTitle = ($sessionNfo['isOpen'] == 1) ? "Session Details" : "Next Session";
?>
<div id="regSideNav">
    <div class="blankMainHeader"><h2><?php echo $navTitle; ?></h2></div>
    <ul>
    <li><span>Start Date</span><br /><?php echo date('l M j, Y',$sessionNfo['eventStart']); ?></li>
    <li><span>End Date</span><br /><?php echo date('l M j, Y',$sessionNfo['endDate']); ?></li>
    <li><span>Time</span><br /><?php echo date('g:i a',$sessionNfo['eventStart']); ?> - <?php echo date('g:i a',$sessionNfo['eventEnd']); ?></li>
    <li><span>Location</span><br /><?php echo $sessionNfo['location']; ?></li>
    <li><span>Coach</span><br /><?php echo (trim($sessionNfo['coach']) == '' ? 'TBA' : $sessionNfo['coach']); ?></li>
    <li><span>Fee</span><br /><?php echo sprintf('$%001.2f',trim($sessionNfo['fee'])); ?></li>
    <li><span><?php echo ($sessionNfo['isOpen'] == 1 ? "Register By ": "Registration" );?></span>
        <br /><?php echo ($sessionNfo['isOpen'] == 1 ? date('M j, Y',$sessionNfo['regClose']): date('M j, Y',$sessionNfo['regOpen']) );?></li>
    </ul>
</div>
<?php
        
    }
    else{
        include_once(dirname(dirname(dirname(__DIR__))) ."/calendar/Widgets/views/practiceSideBar.php");
    }
}
