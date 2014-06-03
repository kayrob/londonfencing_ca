<?php Quipp()->secure()->stopTokenRefresh(); 

$themeDir = __DIR__ . "/themes/" . Quipp()->config('theme');

$meta['title'] =  $e->getMessage(); 
$meta['title_append'] = " &#8226; ". Quipp()->config('site_name');
array_push($meta['body_classes'], 'error');

include $themeDir ."/header.php";
?>    
    <section class="main">    
        <div class="colG">
            <h2 class="error-label">Halte!</h2>
            <p>Sorry, but the page you were trying to view does not exist.</p> 
            <p>It looks like this was the result of either:</p> 
            <ul> 
                <li>a mistyped address</li> 
                <li>an out-of-date link</li> 
            </ul> 
        <script> 
        var GOOG_FIXURL_LANG = (navigator.language || '').slice(0,2),
            GOOG_FIXURL_SITE = location.host;
        </script> 
        <script src="http://linkhelp.clients.google.com/tbproxy/lh/wm/fixurl.js"></script> 
        </div>
     </section> 
<?php
include $themeDir ."/footer.php";