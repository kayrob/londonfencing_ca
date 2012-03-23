<?php

require '../inc/init.php';

$meta['title'] = 'Administrative Panel';
$meta['title_append'] = ' &bull; Quipp CMS';

require 'templates/header.php';
?>

<style type="text/css">
	#dashboardReporting {
		
	}
	
	#dashboardReporting h2 {  
		font-size:18px;
		color:black;
		font-weight:bold;
	}
	
	#dashboardReporting .chartingSummary {  
		font-size:14px;
		color:white;
		font-weight:bold;
		border-top: 2px solid #999999;
		
		padding:0px;
		margin:10px 0px 0px 0px;
	}
	
	#dashboardReporting .chartingSummary tr td {
	
		padding:5px 0px 5px 0px;
	
	}
	
	#dashboardReporting .chartMain {  
		font-size:14px;
		color:#666666;
		font-weight:bold;
		display:block;
		padding:0px;
		margin:0px;
		
	}
	
	#dashboardReporting .chartSub {  
		font-size:12px;
		display:block;
		padding:0px;
		margin:0px;
		color:#999999;
		font-weight:bold;
	}
	
	#dashboardReporting .chartValue {  

		font-size:24px;
		color:black;
		font-weight:bold;
	}
	
	
</style>

<div id="dashboardReporting">
<h2>Dashboard</h2>
<p><?php print date("Y-m-d"); ?></p>
<?php

if (isset($applications)){
    echo '<ul class="dashboard">';
    foreach($applications as $appName => $appInfo){				
            echo('<li><div class="dashboard_item" style="background-image:url(/src/'.Quipp()->config('site_psr').'/'.$appInfo->icon.')")><h3>');

            if (strstr($appInfo->src, 'http') !== false) {
                echo "<a href=\"" . $appInfo->src . "/\" target=\"_blank\">".$appInfo->label."</a>";
            } 
            else {
               $src = basename(dirname(dirname(dirname($appInfo->src))));
                echo "<a href=\"/admin/apps/".$src."/".basename($appInfo->src,".php")."\">".$appInfo->label."</a>";
            }
            echo '</h3>';
            echo '<p>'.$appInfo->info.'</p>';
            echo '</div></li>';
    }
}
?>
</div>
<?php
require 'templates/footer.php';
?>