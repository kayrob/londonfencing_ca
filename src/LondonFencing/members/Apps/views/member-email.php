<?php
require_once dirname(dirname(__DIR__))."/members.php";

use LondonFencing\members as MEMB;

$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require $root . '/inc/init.php';
require $root . '/admin/classes/Editor.php';

$meta['title'] = 'Registration Submission Manager';
$meta['title_append'] = ' &bull; Quipp CMS';

$hasPermission = false;
if ($auth->has_permission("canEditReg")){//change this to members
    $hasPermission = true;
}
if ($hasPermission){
    
    $filter = 'active';
    
    if (isset($_GET['filter']) && (stristr($_GET['filter'],'active') !== false || $_GET['filter'] == 'all')){
        $filter = $db->escape($_GET['filter'],true);
    }
    echo  $filter;
    
    $mem= new MEMB\members($db);
    $advanced = $mem->getMembersEmailList($filter);
    $begInt = $mem->getClassesEmailList($filter);
    
    include $root. "/admin/templates/header.php";
?>
<h1>Members: Email List</h1>
<p>This allows the ability to send group or individual email to club members from any group.</p>
<p>If you want to send an email to a beginner or intermediate class, you should use either the: "Registration: Beginner" tool, or the "Registration: Intermediate" tool</p>
<div class="boxStyle">
    <div class="boxStyleContent">
            <div class="boxStyleHeading">
                    <h2>Select Members</h2>
                    <div class="boxStyleHeadingRight">
                        <form action="<?php echo $_SERVER["REQUEST_URI"];?>">
                            <select name="filter" >
                                <option value="">Choose Filter</option>
                                <option value="inactive"<?php echo ($filter == 'inactive' ? 'selected="selected"' : '');?>>Inactive Members<?php echo ($filter == 'inactive' ? '*' : '');?></option>
                                <option value="all"<?php echo ($filter == 'all' ? 'selected="selected"' : '');?>>All Members<?php echo ($filter == 'all' ? '*' : '');?></option>
                            </select>
                            <input type="button" name="goFilter" value="Filter" class="btnStyle blue" style="float:none"/>
                            <input type="button" name="rmFilter" value="Clear" class="btnStyle" onclick="javascript:window.location='<?php echo preg_replace('%\?filter=(inactive|all)?%','',$_SERVER["REQUEST_URI"]);?>'" style="float:none"/>
                        </form>
                    </div>
            </div>
            <div class="clearfix">&nbsp;</div>
            <div id="template">

<?php
    if (!empty($advanced) || !empty($begInt)){
        echo '<form name="frmSendEmail" action="/admin/apps/notificationManager/emailer" method="post" enctype="multipart/form-data">';
        echo '<table id="adminTableList_email" class="adminTableList tablesorter" width="100%" cellpadding="5" cellspacing="0" border="1">';
        echo '<thead><tr><th>Member Name</th><th>Email Address</th><th>Parent Name</th><th>Level</th><th>Status</th><th>Email<input type="checkbox" id="emailAll" name="emailAll" value="all" /></th></tr></thead>';
        echo '<tbody>';
        if (!empty($advanced)){
            foreach($advanced as $email => $aInfo){
                echo '<tr><td>'.$aInfo['name'].'</td><td>'.$email.'</td><td>'.$aInfo['parent'].'</td><td>'.$aInfo['level'].'</td><td>'.$aInfo['status'].'</td><td><input type="checkbox" id="'.str_replace('[]','_'.$aInfo['id'],$aInfo['inputName']).'" name="'.$aInfo['inputName'].'" value="'.$aInfo['id'].'" /></td></tr>';
            }
        }
        if (!empty($begInt)){
            foreach($begInt as $bEmail => $bInfo){
                if (!isset($advanced[$bEmail])){
                    echo '<tr><td>'.$bInfo['name'].'</td><td>'.$bEmail.'</td><td>'.$bInfo['parent'].'</td><td>'.$bInfo['level'].'</td><td>'.$bInfo['status'].'</td><td><input type="checkbox" id="'.str_replace('[]','_'.$bInfo['id'],$bInfo['inputName']).'" name="'.$bInfo['inputName'].'" value="'.$bInfo['id'].'" /></td></tr>';
                }
            }
        }
        echo '</tbody>';
        echo '<tbody>
            <tr><td colspan="6">
            <input  style="float:right" class="btnStyle blue noPad" id="btnSelect" type="submit" value="Send Email">
            <input type="hidden" name="nonce" value="'.Quipp()->config('security.nonce').'" />
            <input type="hidden" name="etype" value="all-reg" />
            </td>
                </tr>
            </tbody>
            </table>';
            echo '</form>';
        
    }
    else{
        echo 'No '.$filter.' members listed';
    }
?>
                </div>
        </div>
</div>
<?php
    global $quipp;
    $quipp->js['footer'][] = '/src/LondonFencing/members/assets/js/members.js';
    include $root. "/admin/templates/footer.php";
    
}
else{
    $auth->boot_em_out();
}