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

$foundationLog = $rpt->getLastReportLog('foundation');
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
                        <input type="text" class="uniform" name="foundationStart" id="foundationStart" />
                        <label for="foundationEnd">Date Range End</label>
                        <input type="text" class="uniform" name="foundationEnd" id="foundationEnd" />
                        <input type="submit" name="submitFoundation" class="btnStyle blue" id="submitFoundation" value="Export .csv" style="float:none" />
                        <input type="hidden" name="nonce" value="<?php echo Quipp()->config('security.nonce');?>" />
                    </form>
                    
                </div>
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
