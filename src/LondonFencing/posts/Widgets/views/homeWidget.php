<?php
$row = array();
if (isset($db) && $this INSTANCEOF Quipp){
    $qry = "SELECT `slug`, `lead_in`, `type`, `title` , UNIX_TIMESTAMP(`displayDate`) as displayDate, `author` FROM `tblNews` WHERE `approvalStatus` = '1' AND `sysStatus` = 'active' AND `sysOpen` = '1' AND `type` IN ('news') AND UNIX_TIMESTAMP(`displayDate`) <= UNIX_TIMESTAMP() ORDER BY `displayDate` DESC, `itemID` DESC LIMIT 0,2 ";
    $res = $db->query($qry);
    if ($res->num_rows > 0){
        $row[] = $db->fetch_assoc($res);
        
    }
}
?>
<section class="callout" id="latestNews">
    <h2>Latest News</h2>
<?php
    if (!empty($row)){
        foreach($row as $data){
            $end = (strlen(strip_tags(trim($data["lead_in"]))) > 300)?strpos(strip_tags(trim($data["lead_in"]))," ", 295):strlen(strip_tags(trim($data["lead_in"])));
            $lead = substr(strip_tags(trim($data["lead_in"])),0,$end);
            if ($end < strlen(strip_tags(trim($data["lead_in"])))){ $lead .= "&hellip;"; }
            echo '<h3>'.trim($data['title']).'</h3>';
            echo '<h4> '.date('M j, Y',trim($data['displayDate'])).' | <span class="lowlight">Posted By: '.trim($data['author']).'</span></h4>';
            echo '<p>'.$lead.'</p>';
            echo '<p><a href="/news/'.trim($data['slug']).'" class="readMore">read more</a></p>';
        }
    }
?>
</section>