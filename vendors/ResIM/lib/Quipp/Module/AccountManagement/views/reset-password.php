<?php
    
    if(!function_exists('Quipp')) {
		require_once dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '/inc/init.php';
	}

    $quipp->js['footer'][]  = '/js/password_strength/password_strength_plugin.js';
?>
<h2>Reset your password</h2><p></p>
<?php
   
    if (isset($_GET['token'])) {
        
        
        
        
        try {
            $user = Quipp()->getModule('AccountManagement')->verifyToken($_GET['token'], 'fpHash');
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
        
        if (isset($_POST['password'], $_POST['conf_password'])) {
            
            if ($_POST['password'] != $_POST['conf_password']) {
                echo 'Password mismatch';
                
            } else {
        
            
                try {
                    $result = $user->changePassword($_POST['password']);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    die();
                }
                
                
                if ($result) {
                    $url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/login';
                    echo 'Your password was changed. Please <a href="' . $url . '">proceed to the login</a>';
                    die();
                } else {
                    echo "Unable to change account password";
                    die();
                
                }
            }
            
        }
    }
    
?>

<form action="?token=<?php echo $_GET['token']; ?>" method="post">
    <div>
        <label for="email">Your email</label> <input type="email" name="email" id="email" value="<?php echo $user->{'E-Mail'}; ?>" /><br>
        <label for="password">New password</label> <input type="password" name="password" id="password" required /><br>
        <label for="conf_password">Confirm password</label> <input type="password" name="conf_password" id="conf_password" required />

        <input type="hidden" name="nonce" value="<?php echo Quipp()->config('security.nonce'); ?>" />
        <input type="submit" value="Submit" />
    </div>
</form>
