<?php

    $qcore = Quipp();

    if (isset($_POST['username'], $_POST['password'])) {
        $qcore->auth()->login($_POST['username'], $_POST['password']);
    }

//    require 'header.php';

    if (isset($_GET['t'])) {
        print $qcore->auth()->fail_type($_GET['t']);
    }

    $username = (isset($_POST['username'])) ? $_POST['username'] : '';

    $directoryTag = "";
    if ($qcore->auth()->type == "ad") {
        $directoryTag = "<span style=\"color:#CCCCCC; font-style:italic; font-size:10px;\"> (Active Directory - " . $qcore->auth()->ad->domain_controllers[0] . ")</span>";
    }

    $showQuippBrand = " class=\"quippBranding\"";
    ?>
        <div id="loginBox" <?php print $showQuippBrand; ?>>
 
            <form action="<?php print $_SERVER['REQUEST_URI']; ?>?login<?php print $qs; ?>" id="loginBoxForm" method="post">
            	<h2>Sign-In</h2>
                <input type="hidden" name="nonce" value="<?php echo $qcore->config('security.nonce'); ?>" />
                
                        <div id="loginBoxUsername">
                            <label for="username">Username</label>
                            <input type="text" class="loginText" style="width:160px;" id="username" name="username" autofocus="autofocus" value="<?php print $username; ?>" />
                        </div>
                        <div id="loginBoxPassword">
                            <label for="password">Password</label>
                            <input type="password" class="loginText" style="width:160px;" id="password" name="password" value="" />
                        </div>
                        <input type="checkbox" id="keepSignedIn">
                        <label for="keepSignedIn">Keep me signed in</label>
                        <div id="loginBoxButtons">
                            <input type="submit"  value="Sign-in" class="btnStyleGreen" />
                        </div>
                        
                        <div class="loginTips"><a href="/forgot-password">Password Recovery</a></div>
                        
           
                <div class="clearBox">&nbsp;</div>
            </form>
        </div>
    <?php

//    require 'footer.php';