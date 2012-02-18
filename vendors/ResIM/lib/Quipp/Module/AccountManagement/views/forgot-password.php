<?php
    
    if(!function_exists('Quipp')) {
        // trollolololol
        require_once dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '/inc/init.php';
	}
    
	if (!empty($_POST)) {
        Quipp()->getModule('AccountManagement')->sendReset($_POST['email']);
	}
?>
<h2>Reset your password</h2>
<p>In order to reset your password, please supply us with your email address that you used to sign up to Ownersbox.com</p>
<form action="" method="post">
    <div>
        <label for="email">Your email address</label> <input type="email" name="email" id="email" placeholder="johndoe@example.com" required />
        <input type="hidden" name="nonce" value="<?php echo Quipp()->config('security.nonce'); ?>" />
        <input type="submit" value="Submit" />
    </div>
</form>
