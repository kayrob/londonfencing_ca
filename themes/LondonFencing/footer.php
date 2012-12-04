<div class="clearfix"></div>
</div> <!--! end of #content -->

    <footer>
        <?php print $nav->build_nav($nav->get_nav_items_under_bucket('footer'), $slug, true, false);  ?>
        <div>Copyright 2012 &bull; London Fencing Club &bull;
        London, Ontario, Canada&nbsp;
        <a href="http://www.fencingontario.ca" target="_blank"><img src="/themes/LondonFencing/img/ofa-trans.png" alt="OFA" width="38px" height="38px"/></a>&nbsp;
        <a href="http://www.fencing.ca" target="_blank"><img src="/themes/LondonFencing/img/cff.png" alt="CFF" width="24px" height="38px"/></a>
        </div>
    	<div class="clearfix"></div>
    </footer>
    
    <div class="clearfix"></div>
    
</div> <!--! end of #container -->



    
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="/js/jquery-1.6.4.min.js"><\/script>')</script>
  <script src="/js/jquery-ui/jquery-ui-1.8.16.custom.min.js"></script>
  <script src="/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
  <script src="/js/jquery.easing-1.3.pack.js"></script>
  <script src="/js/jquery.hoverIntent.min.js"></script>
<?php
    if(isset($quipp->js['footer']) && is_array($quipp->js['footer'])) {
        foreach($quipp->js['footer'] as $val) {
            if ($val != '') { print '<script src="' . $val . '"></script>'; }
        }
    }
?>

  <script src="/themes/LondonFencing/site.js"></script>

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
