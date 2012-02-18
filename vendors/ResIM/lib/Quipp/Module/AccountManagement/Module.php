<?php
namespace Quipp\Module\AccountManagement;
use Quipp\Core;
use Quipp\Module\ModuleInterface;
use PHPMailer\PHPMailer;

/**
 * The controller for all users/groups
 */
class Module implements ModuleInterface {
    /**
     * @var Quipp\Core
     */
    protected $_core;

    public function __construct(Core $quipp) {
        $this->_core = $quipp;
    }


    /**
     * {@inheritdoc}
     */
    public function install() {
    }


    /**
     * {@inheritdoc}
     */
    public function uninstall() {
    }

    public function getAppsList() {
        return new \ArrayIterator(array());
    }

    /**
     * {@inheritdoc}
     * @todo Implement
     */
    public function getWidgetList() {
    }


    /**
     * {@inheritdoc}
     * @todo Implementation
     */
    public function getWidget($name) {
    }

    public function isTokenLive($token) {
        list($expiry, $token) = explode('|', $token);
        return (boolean)  ((time() - (int)$expiry) < 0);
    }

    /**
     * @param string
     * @param string (regHash|fpHash)
     * @throws Exception
     * @return User
     */
    public function verifyToken($token, $type) {
        if (!$this->isTokenLive($token)) {
            throw new Exception("Expired token");
        }

        $db = $this->_core->db();

        $qry = sprintf("SELECT itemID FROM sysUsers WHERE `%s` = '%s'",
            $db->escape($type),
            $db->escape($token));
            
        $res = $db->query($qry);

        if (is_object($res) && $db->num_rows($res) == 1) {
            list ($id) = $db->fetch_array($res);
            return new User($this->_core, $id);
        }
        
        throw new Exception("Invalid token");
    }

    /**
     * @param string
     * @return bool
     */
    public function isUsernameInUse($username) {
        $check = $this->_core->db()->query(sprintf("SELECT COUNT(*) FROM `sysUsers` WHERE `userIDField` = '%s'", $this->_core->db()->escape_string($username)));
        list($num_same) = $check->fetch_row();

        return (boolean)$num_same;
    }

    /**
     * Create a new user account
     * @param string
     * @param string
     * @param string
     * @throws Exception
     * @return User
     * @todo Chris use `regHash` for verification
     */
    public function createAccount($username, $password, $status = 'active') {
        $sts_opts = array('active', 'inactive', 'disabled', 'public');
        if (!in_array($status, $sts_opts)) {
            throw new Exception("{$status} is not a valid account status");
        }

        if (true !== $this->_core->validate($username, 'user')) {
            throw new Exception("{$username} is not a valid username");
        }

        $db = $this->_core->db();

        if ($this->isUsernameInUse($username)) {
            throw new Exception("{$username} is already in use");
        }

        $hashed = $this->_core->secure()->hashPassword($password);

        $stmt = $db->query(sprintf("INSERT INTO `sysUsers` (`userIDField`, `userIDPassword`, `lastLoginDate`, `regDate`, `sysStatus`) VALUES ('%s', '%s', NOW(), NOW(), 'inactive')"
          , $db->escape_string($username)
          , $db->escape_string($hashed)
        ));

        // check validity, throw exception

        return new User($this->_core, $db->insert_id());
    }

    /**
     * @param User
     */
    public function deleteAccount(User $account)
    {
        $id = $account->getID();
        $db = $this->_core->db();

        return false === $db->query(sprintf("UPDATE `sysUsers` SET `sysOpen` = 0 WHERE `itemID` = %d", $id)) ? false : true;
    }


    public function getAccount($id)
    {
        $result = $this->_core->db()->query(sprintf("SELECT `itemID` FROM `sysUsers` WHERE `itemID` = %d AND `sysOpen` = '1'", $id));
        if ($result->num_rows !== 1) {
            throw new Exception("Invalid user specified");
        }

        list($confirm) = $result->fetch_row();
        return new User($this->_core, $confirm);
    }


    /**
     * @param string
     * @throw Exception
     * @return Group
     */
    public function getGroup($group_slug)
    {
        $stmt = $this->_core->db()->query(sprintf("SELECT `itemID` FROM `sysUGroups` WHERE `nameSystem` = '%s'", $this->_core->db()->escape_string($group_slug)));
        if ($stmt->num_rows !== 1) {
            throw new Exception("No group found matching that slug");
        }
        list($group_id) = $stmt->fetch_row();

        $this->_core->debug()->add("Returning group object of group id {$group_id}");

        return new Group($this->_core, $group_id);
    }


    /**
     * @return string
     */
    public function generateToken() {
        return uniqid(strtotime('+3hours') . '|1', true);
    }


    /**
     * Send an email to this user to
     * @param string A valid email address
     * @todo Fetch email from database
     */
    public function sendReset($email) {

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $db = $this->_core->db();


        // check to see if the email exists
        $qry = sprintf("SELECT userID FROM sysUGFValues WHERE sysStatus='active' AND sysOpen='1' AND fieldID='3' AND value='%s'",
            $db->escape($email));
        $res = $db->query($qry);

        if (is_object($res) && $db->num_rows($res) > 0) {

            list($userID) = $db->fetch_array($res);

            // insert a unique hash into the DB to check on later
            $hash = $this->generateToken();
            $qry = sprintf("UPDATE sysUsers SET fpHash='%s' WHERE itemID='%d'",
                $hash,
                $userID);
            $db->query($qry);

            $resetLink = 'http://ownersbox.com/reset-password?token=' . $hash;


            $mail = new \stdClass();
            $mail->Subject = "Ownersbox.com password reset";
            $mail->Email   = $email;


            $mail->AltBody = "Hi there,

There was recently a request to change the password on your account.

If you requested this password change, please set a new password by following the link below:

{$resetLink}

If you don't want to change your password, just ignore this message.

Thanks,
- Ownersbox.com";

            $body = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/emails/reset-email.html');
            $body = str_replace("%RESET_LINK%", $resetLink, $body);
            $mail->Body = $body;
            
            print_r($mail);
            if ($this->_core->sendEmail($mail->Email, $mail->Subject, $mail->Body , $mail->AltBody)) {

                return true;
            }
        }

        return false;
    }
}