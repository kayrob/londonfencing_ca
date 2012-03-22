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
    <h2><?php echo $navTitle; ?></h2>
    <ul>
    <li class="striped">Start Date: <span><?php echo date('l M j, Y',$sessionNfo['eventStart']); ?></span></li>
    <li>End Date: <span><?php echo date('l M j, Y',$sessionNfo['endDate']); ?></span></li>
    <li class="striped">Time: <span><?php echo date('g:i a',$sessionNfo['eventStart']); ?> - <?php echo date('g:i a',$sessionNfo['eventEnd']); ?></span></li>
    <li>Location: <span><?php echo $sessionNfo['location']; ?></span></li>
    <li class="striped">Coach: <span><?php echo (trim($sessionNfo['coach']) == '' ? 'TBA' : $sessionNfo['coach']); ?></span></li>
    <li>Fee: <span><?php echo sprintf('$%001.2f',trim($sessionNfo['fee'])); ?></span></li>
    <li class="striped"><?php echo ($sessionNfo['isOpen'] == 1 ? "Register By: ": "Registration:" );?> 
        <span><?php echo ($sessionNfo['isOpen'] == 1 ? date('M j, Y',$sessionNfo['regClose']): date('M j, Y',$sessionNfo['regOpen']) );?></span></li>
    </ul>
</div>
<?php
        
    }
    
}
