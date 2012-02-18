<?php
if ($this INSTANCEOF Quipp && isset($this->siteID)){

    $sysName =  (isset($_GET["p"]) && trim($_GET["p"]) != "")?$db->escape($_GET["p"],true):"home"; 
    $bQuery = sprintf("SELECT b.* FROM `tblBanners` AS b 
    INNER JOIN `sysPageDataLink` AS dl ON b.`itemID` = dl.`appItemID` INNER JOIN `tblBannerSiteLinks` bl ON b.`itemID` = bl.`bannerID` 
    WHERE dl.`appID` = 'banners' AND dl.`sysStatus` = 'active' AND dl.`sysOpen` = '1' AND dl.`pageSystemName` = '%s' AND b.`sysOpen` = '1' AND b.`sysStatus` = 'active' AND bl.`siteID` = %d",
        $sysName,
        (int)$this->siteID
    );
    $bRes = $db->query($bQuery);
    if ($db->valid($bRes)){
        $numRecords = $db->num_rows($bRes);
?>

<hgroup id="banners">
	<div id="bannerImg"><img src="/uploads/banners/smilingLady.png" alt="Banner Overlay" id="imgOverlay" width="275" height="444" /></div>
	<div id="bannerSlider">
	<?php
	$p = 0;
    while ($data = $db->fetch_assoc($bRes)){
       $overlay = (trim($data["overlay"]) != 'banner1.jpg')?trim($data["overlay"]):'smilingLady.png';
       $display = ($p == 0)?'':'style="display:"none"';
       echo '<img class="bannerImg '.$data['itemID'].'" data-title="'.stripslashes($data['title']).'" data-bodytext="'.stripslashes($data['body_text']).'" data-bannerlink="'.stripslashes($data['link']).'" data-overlay="'.$overlay.'" src="/uploads/banners/'.$data['photo'].'" alt="'.$data['photo'].'" '.$display.'/>';
       $p++;
    }
    ?>
    </div>
	<div id="bannerContent">
		<h4></h4>
		<h1></h1>
	</div>
	<div id="bannerNav">
		<div class="ribbonLeft"></div>
		<?php
		if ($p > 1){
		  echo '<a class="arrow" href="#" id="prev">Previous</a>';
		  echo '<a class="arrow" href="#" id="next">Next</a>';
		}
		?>
		<div class="ribbonRight"></div>	
	</div>
    <div class="clearfix"></div>
</hgroup>
<?php
        global $quipp;
        $quipp->js['footer'][] = "/js/jquery.cycle.min.js";
        $quipp->js['footer'][] = "/src/LondonFencing/banners/assets/js/banners.js";
    }
}