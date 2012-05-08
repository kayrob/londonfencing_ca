<?php
if (isset($news) && isset($slug) && strstr($slug,"archive-") !== false){
    $archive = str_replace("archive-","",$slug);
    $posts = ((bool)strtotime($archive) === false)?$news->getArchiveByCategory($archive):$news->getArchiveByDate(strtotime($archive));
    
    if ($posts !== false){
?>
        <div id="news-article-<?php echo $archive; ?>" class="news-archive">
        <h2>News: <?php echo ucwords($archive); ?></h2>
<?php
            foreach ($posts as $post){
                echo '<h3>'.trim($post["title"]).'</h3>';
                echo '<h4 style="font-style:italic">'.date("F j, Y",trim($post["displayDate"])).' | <span class="lowlight">Posted By: '.trim($post["author"]).'</span></h4>';
                echo '<p>'.  str_shorten(trim($post["lead_in"]),150).'</p>';
                echo '<p><a href="/news/'.trim($post["slug"]).'">read more</a></p>';
            }
     
?>   
        </div>
<?php  
    

    }
    
}