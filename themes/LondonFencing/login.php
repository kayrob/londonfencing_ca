<?php

    $qcore = Quipp();

    if (isset($_POST['username'], $_POST['password'])) {
        $qcore->auth()->login($_POST['username'], $_POST['password']);
    }

//    require 'header.php';

   $username = (isset($_POST['username'])) ? $_POST['username'] : '';

    $directoryTag = "";
    if ($qcore->auth()->type == "ad") {
        $directoryTag = "<span style=\"color:#CCCCCC; font-style:italic; font-size:10px;\"> (Active Directory - " . $qcore->auth()->ad->domain_controllers[0] . ")</span>";
    }

    $showQuippBrand = " class=\"quippBranding\"";
?>
<!doctype html>
<html>
    <head>
        <title>Login</title>
        <link rel="stylesheet" href="/themes/LondonFencing/default.css" media="screen" type="text/css"/>
        <style type="text/css">.alertBoxFunctionBad{width: 280px;float: none;margin-top: 0px;</style>
    </head>
<body>
        <div id="loginBox" <?php print $showQuippBrand; ?>>
            <div class="loginBoxHead"><img src="/themes/LondonFencing/img/logo.png" alt="Logo" /></div>
            <form action="<?php print $_SERVER['REQUEST_URI']; ?>?login<?php print $qs; ?>" id="loginBoxForm" method="post">
<?php
             if (isset($_GET['t'])) {
                print $qcore->auth()->fail_type($_GET['t']);
            }
?>
                <input type="hidden" name="nonce" value="<?php echo $qcore->config('security.nonce'); ?>" />
                
                        <div id="loginBoxUsername">
                            <label for="username">Username</label>
                            <input type="text" class="loginText" style="width:160px;" id="username" name="username" autofocus="autofocus" value="<?php print $username; ?>" />
                        </div>
                        <div id="loginBoxPassword">
                            <label for="password">Password</label>
                            <input type="password" class="loginText" style="width:160px;" id="password" name="password" value="" />
                        </div>
                        <div id="loginBoxButtons">
                            <label>&nbsp;</label><input type="submit"  value="Sign-in" class="btnStyle" />
                        </div>

            </form>
        </div>
</body>
</html>