<?php
if (isset($db) && $this INSTANCEOF Quipp){
$qry = sprintf("SELECT `handle` FROM `tblTwitter` WHERE `siteID` = %d AND `sysStatus` = 'active' AND `sysOpen` = '1' LIMIT 1",(int)$this->siteID);
$res = $db->query($qry);

if ($db->valid($res)){
    $row = $db->fetch_assoc($res);
    $tweets = @simplexml_load_file('http://twitter.com/statuses/user_timeline/'.trim($row["handle"]) .'.xml?count=10');
}
?>
<section id="twitter">
	<div class="blankMainHeader">
		<h2>Latest Tweets</h2>
	</div>
	<?php
	   if (isset($tweets) && is_object($tweets)){
	       $p = 0;
	       foreach ($tweets->status as $tweet){
	           $created = strtotime((string)$tweet->created_at);
	           $dateDiff = floor((date("U") - $created)/(60*60));
	           $tweetTime = "Today";
	           if ($dateDiff > 24){
	               $daysDiff = floor($dateDiff/24);
	               if ($daysDiff < 7){
	                   $tweetTime = $daysDiff . " days ago";
	               }
	               else{
	                   $weekDiff = $daysDiff/7;
	                   if ($weekDiff <= 4){
	                       $tweetTime = ($weekDiff == 1) ?$weekDiff. " week ago" : $weekDiff. " weeks ago";
	                   }
	                   else{
	                       $tweetTime = "< a month ago";
	                   }
	               }
	           }
	           echo '<p>' . (string)$tweet->text .'</p>';
	           echo '<div class="blankMainHeaderTwit"><h6>'.$tweetTime.' &bull; <a href="http://www.twitter.com/'.trim($row["handle"]).'" target="_blank">@'.trim($row["handle"]).'</a></h6></div>';
	           $p++;
	           if ($p == 2){
	               break;
	           }
	       }
	   }
	   else{
	       echo '<p>No tweets</p>';
	       echo '<div class="blankMainHeaderTwit"><h6>Twitter might be down</h6></div>';
	   }
	?>
</section>
<?php
}
