<?php
$articles = array();
if (isset($db) && $this INSTANCEOF Quipp){
    $qry = "SELECT `slug`, `lead_in`, `type`, `title` , UNIX_TIMESTAMP(`displayDate`) as displayDate, `author` FROM `tblNews` WHERE `approvalStatus` = '1' AND `sysStatus` = 'active' AND `sysOpen` = '1' AND `type` IN ('news') AND UNIX_TIMESTAMP(`displayDate`) <= UNIX_TIMESTAMP() ORDER BY `displayDate` DESC, `itemID` DESC LIMIT 0,2 ";
    $res = $db->query($qry);
    if ($res->num_rows > 0){
        while ($row = $db->fetch_assoc($res)){
            $articles[] = $row;
        }
    }
}
?>
<section class="callout" id="latestNews">
    <h2>Latest News</h2>
<?php
    if (!empty($articles)){
        foreach($articles as $data){
            $anchors = array();
            $leadin = strip_tags($data["lead_in"],"<a><br>");
            preg_match_all('%\s(href=[^\s]*(\starget=[\'\"]_blank[\'\"])?)%', $leadin,$matches);
            if (!empty($matches) && isset($matches[1])){
                foreach ($matches[1] as $index => $href){
                    $leadin = str_replace('<a '.$href.'>','%AC'.$index.'%',$leadin);
                    $anchors[$index] = $href;
                }
                $leadin = str_replace('</a>','%AD%', $leadin);
            }
            $end = (strlen($leadin) > 300)?strpos($leadin," ", 295):strlen(trim($leadin));
            $lead = substr($leadin,0,$end);
            if ($end < strlen($lead)){ $lead .= "&hellip;"; }
            if (!empty($anchors)){
                foreach($anchors as $aInd => $href){
                    $lead = str_replace('%AC'.$aInd.'%', '<a '.$href.'>', $lead);
                }
                $lead = str_replace('%AD%', '</a>', $lead);
            }
            echo '<h3>'.trim($data['title']).'</h3>';
            echo '<h4> '.date('M j, Y',trim($data['displayDate'])).' | <span class="lowlight">Posted By: '.trim($data['author']).'</span></h4>';
            echo '<p>'.$lead.'</p>';
            echo '<p><a href="/news/'.trim($data['slug']).'" class="readMore">read more</a></p>';
        }
    }
?>
</section>