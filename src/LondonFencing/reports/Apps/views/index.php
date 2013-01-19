<?php
$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require $root . '/inc/init.php';
require_once(dirname(dirname(__DIR__)).'/reports.php');
use LondonFencing\reports as RPT;

$meta['title'] = 'Reporting Tools';
$meta['title_append'] = ' &bull; Quipp CMS';

$hasPermission = false;
if ($auth->has_permission("canCreateReports")){
    $hasPermission = true;
}

if ($hasPermission) {
    $rpt = new RPT\reports($db);
    
include $root. "/admin/templates/header.php";   

$foundationLog = $rpt->getLastReportLog('foundation', 1);
$taxLog        = $rpt->getLastReportLog('taxReceipts', 10);
?>
<h1>Reporting Tools</h1>
<p>Allows the creation/download of reports using simple input options</p>
<div class="boxStyle">
        <div class="boxStyleContent">
                <div class="boxStyleHeading">
                        <h2>Foundation Membership List</h2>
                        <div class="boxStyleHeadingRight">
                            <?php
                            if (!empty($foundationLog)){
                                echo '<p style="text-align:right">&nbsp;<br />Last Report: '.date('F j, Y', $foundationLog['sysDateCreated']).' '.$foundationLog['options'].'</em>';
                            }
                            ?>
                        </div>
                </div>
                <div class="clearfix">&nbsp;</div>
                <div id="template">
                    <form name="frmFoundation" id="frmFoundation" action="/src/LondonFencing/reports/assets/foundationReport.php" method="post">
                        <label for="foundationStart">Date Range Start</label>
                        <input type="text" class="uniform datepicker" name="foundationStart" id="foundationStart" />
                        <label for="foundationEnd">Date Range End</label>
                        <input type="text" class="uniform datepicker" name="foundationEnd" id="foundationEnd" />
                        <input type="submit" name="submitFoundation" class="btnStyle blue" id="submitFoundation" value="Export .csv" style="float:none" />
                        <input type="hidden" name="nonce" value="<?php echo Quipp()->config('security.nonce');?>" />
                    </form>
                    
                </div>
        </div>
</div>
<div class="boxStyle">
        <div class="boxStyleContent">
                <div class="boxStyleHeading">
                        <h2>Intermediate Session Cards</h2>
                        <div class="boxStyleHeadingRight">
                            <input type="button" class="btnStyle blue" onclick="window.open('../../../registration/assets/sessioncards.pdf')" value="Print Cards"/>
                        </div>
                </div>
                <div class="clearfix">&nbsp;</div>
                <div id="template">
                    <form target="_blank" name="frmSessionCards" id="frmSessionCards" action="/src/LondonFencing/registration/assets/intSessionAttendance.php" method="post">
                        <label for="sessionStart">Date Range Start</label>
                        <input type="text" class="uniform datepicker" name="sessionStart" id="sessionStart" />
                        <label for="sessionEnd">Date Range End</label>
                        <input type="text" class="uniform datepicker" name="sessionEnd" id="sessionEnd" />
                        <input type="submit" name="submitSession" class="btnStyle blue" id="submitSession" value="Attendance List" style="float:none" />
                        <input type="hidden" name="nonce" value="<?php echo Quipp()->config('security.nonce');?>" />
                    </form>
                </div>
        </div>
</div>
<div class="boxStyle">
    <div class="boxStyleContent">
                <div class="boxStyleHeading">
                        <h2>Tax Receipts</h2>
                        <div class="boxStyleHeadingRight">
                            <?php
                            if (!empty($taxLog)){
                                if (!isset($taxLog[0])){
                                    $taxLog[0] = $taxLog;
                                }
                                echo '<p style="text-align:right">&nbsp;<br />Last Report: '.date('F j, Y', $taxLog[0]['sysDateCreated']).' '.$taxLog[0]['options'];
                            }
                            ?>
                        </div>
                </div>
                <div class="clearfix">&nbsp;</div>
                <div id="template">
                    <form name="frmReceipts" id="frmReceipts" action="/src/LondonFencing/reports/assets/receipts.php" method="post">
                        <label for="taxesGroup">Tax Group</label>
                        <select name="taxesGroup" id="taxesGroup">
                            <option value="beginner">Beginner</option>
                            <option value="club">Club</option>
                            <option value="intermediate">Intermediate</option>
                        </select>
                        <label for="taxesStart">Date Range Start</label>
                        <input type="text" class="uniform datepicker" name="taxesStart" id="taxesStart" />
                        <label for="taxesEnd">Date Range End</label>
                        <input type="text" class="uniform datepicker" name="taxesEnd" id="taxesEnd" />
                        <input type="submit" name="submitReceipts" class="btnStyle blue" id="submitReceipts" value="Send Receipts" style="float:none" />
                        <input type="hidden" name="nonce" value="<?php echo Quipp()->config('security.nonce');?>" />
                    </form>
<?php
                    if (isset($taxLog[1])){
                        echo '<p class="moreReports"><span id="moreTax">&#9660;More</span></p>';
                        echo '<div id="dvMoreTax" style="display:none">';
                        for ($t = 1; $t < count($taxLog); $t++){
                            echo 'Report Info: '.date('F j, Y', $taxLog[$t]['sysDateCreated']).' '.$taxLog[$t]['options'].'<br /><br />';
                        }
                        echo '</div>';
                    }
?>

                </div>
        </div>
</div>
<div class="boxStyle">
    <div class="boxStyleContent">
                <div class="boxStyleHeading">
                        <h2>Emergency Contact Sheet</h2>
                        <div class="boxStyleHeadingRight">
                            <input type="button" class="btnStyle blue" value="Print" id="btnEmergency"/>
                        </div>
                </div>
                <div class="clearfix">&nbsp;</div>
        </div>
</div>
<?php
//end of display logic

global $quipp;
$quipp->js['footer'][] = '/src/LondonFencing/reports/assets/js/reports.js';
include $root. "/admin/templates/footer.php";

}
else{
    $auth->boot_em_out();

}
