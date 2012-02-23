<section id="tournaments">
        <div class="blankMainHeader">
	<h2>Upcoming Tournaments</h2>
        </div>
<?php
$rss = @simplexml_load_file("http://www.airsetpublic.com/syndicate/public/16972/year.xml");

if (is_object($rss)){

        $p = 0;
        $feed = $rss->channel;
        foreach ($feed->item as $tourn){
            $tDate = strtotime((string)$tourn->pubDate);
            
            preg_match("%(http:\/\/[a-zA-z\.\?\-0-9_=\/]*)(<br \/>)*(.*)%",(string)$tourn->description,$dMatch);
            
            $description = (isset($dMatch[1]))?trim(str_replace($dMatch[1],"",(string)$tourn->description)):(string)$tourn->description;
            
            $description = ltrim(ltrim($description,"<br />"),"<br>");
            echo '<div><h4>'.date('M',$tDate).'</h4><h5>'.date('j',$tDate).'</h5>ICS';
            echo '<h6>'.(string)$tourn->title.'</h6>';
            echo '<p>'. $description.'</p>';
            
            if (isset($dMatch[1])){
                echo '<div class="blankMainHeaderTwit"><h6><a href="'.$dMatch[1].'" target="_blank">More Info</a></h6>';
            }
           
            $p++;
            if ($p == 10){
                break;
            }
        }
}
?>
</section>