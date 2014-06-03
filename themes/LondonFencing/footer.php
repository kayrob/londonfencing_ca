</div> <!--! end of #content -->

    <footer>
        <div class="colE">
        <?php print $nav->build_nav($nav->get_nav_items_under_bucket('footer'), $slug, true, false);  ?>
        </div>
        <div class="colF">
            <ul>
                <li>Copyright <?php echo date("Y");?> &#8226; London Fencing Club &#8226; London, Ontario, Canada</li>
                <li><a href="http://www.fencingontario.ca" target="_blank"><img src="/themes/LondonFencing/img/ofa-trans.png" alt="OFA" width="38px" height="38px"/></a><a href="http://www.fencing.ca" target="_blank"><img src="/themes/LondonFencing/img/cff.png" alt="CFF" width="24px" height="38px"/></a></li>
            </ul>
        </div>
    </footer>
    
</div> <!--! end of #container --> 
  <script src="http://code.jquery.com/jquery-latest.min.js"></script>
  <script>window.jQuery || document.write('<script src="/js/jquery-1.11.min.js"><\/script>')</script>
  <script src="/js/jquery-ui/jquery-ui-1.8.16.custom.min.js"></script>
  <script src="/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
  <script src="/js/jquery.easing-1.3.pack.js"></script>
  <script src="/js/jquery.hoverIntent.min.js"></script>
  <script src="/js/jpanelmenu.js"></script>
<?php
    if(isset($quipp->js['footer']) && is_array($quipp->js['footer'])) {
        foreach($quipp->js['footer'] as $val) {
            if ($val != '') { print '<script src="' . $val . '"></script>'; }
        }
    }
?>
  <script src="/themes/LondonFencing/site.js"></script>
  <script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-37613468-2']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
  <!--[if lt IE 7 ]>
    <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
    <script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
  <![endif]-->  
</body>
</html>
