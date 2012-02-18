<div class="clearfix"></div>
	<div class="footImg"><!-- Decrative image for the bottom of the container --></div>
</div> <!--! end of #content -->

    <footer>
        <!-- <?php print $nav->build_nav($nav->get_nav_items_under_bucket('primary'), $slug, true, false);  ?> -->
        <ul>
        	<li><img src="/themes/RoyalYork/img/twitter.png" alt="twitter" width="25" height="25" /></li>
        	<li><img src="/themes/RoyalYork/img/facebook.png" alt="facebook" width="25" height="25" /></li>
        	<li><a href="/feed/blog"><img src="/themes/RoyalYork/img/rss.png" alt="rss" width="25" height="25" /></a></li>
        </ul>
        <br />Copyright 2012 - Royal York Orthodontics<br /><br />
        3029 Bloor Street West &bull; Toronto Ontario &bull; M8X 1C5 &bull; 416-207-0885
    	<div class="clearfix"></div>
    </footer>
    
    <div class="clearfix"></div>
    
</div> <!--! end of #container -->



    
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="/js/jquery-1.6.4.min.js"><\/script>')</script>
  <script src="/js/jquery-ui-1.8.16.custom.min.js"></script>
  <script src="/js/jquery.fancybox-1.3.4.pack.js"></script>
  <script src="/js/jquery.easing-1.3.pack.js"></script>
  <script src="/js/jquery.hoverIntent.min.js"></script>

<?php
    if(isset($quipp->js['footer']) && is_array($quipp->js['footer'])) {
        foreach($quipp->js['footer'] as $val) {
            if ($val != '') { print '<script src="' . $val . '"></script>'; }
        }
    }
?>

  <script src="/themes/RoyalYork/site.js"></script>

  <script>
    var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview'],['_trackPageLoadTime']];
    (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.async=1;
    g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
    s.parentNode.insertBefore(g,s)}(document,'script'));
  </script>
  <!--[if lt IE 7 ]>
    <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
    <script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
  <![endif]-->  
</body>
</html>
