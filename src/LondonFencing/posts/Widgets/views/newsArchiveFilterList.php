<?php
require_once dirname(dirname(__DIR__))."/posts.php";
use LondonFencing\posts\Widgets as News;

if (isset($db) && $this INSTANCEOF Quipp ){

    if (!isset($news)){
        $news = new News\Blog($db, $this->siteID, "active");
        $news->type = "news";
    }
    if (isset($news) ){
    
        $filters = $news->getArchiveYears();
?>
    <section class="filtersNav">
    <div class="blankMainHeader"><h2>Archives</h2></div>
<?php
        if (!empty($filters)){
            echo '<ul>';
            foreach ($filters as $archiveYear){
                    echo '<li>
                    <a href="/'.$_GET['p'].'/'.$archiveYear.'">'.$archiveYear.'</a>
                    <a class="icons gray" href="/'.$_GET['p'].'/'.$archiveYear.'">
                        <i class="icon-arrow-right"></i></a>
                        </li>';
            }
            echo '</ul>';
        }
?>
    </section>
<?php
    }
}