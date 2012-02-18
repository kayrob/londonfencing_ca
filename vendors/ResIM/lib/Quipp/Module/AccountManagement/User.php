<?php
namespace Quipp\Module\AccountManagement;
use Quipp\Core;

/**
 * Represents a single user to perform reads and updates on
 */
class User {
    /**
     * @type int
     * @db `sysUsers`.`itemID`
     */
    protected $_id;

    /**
     * @type DB
     */
    protected $db;

    /**
     * @type Quipp\Core
     */
    protected $_core;

    protected $_meta = array();

    /**
     * @param Quipp\Core
     * @param int The ID of the user
     */
    function __construct(Core $core, $user_id) {
        $this->_core = $core;
        $this->db    = $core->db();
        $this->_id   = (int)$user_id;
    }

    public function __get($name) {
        $this->fetchMeta();
        return (isset($this->_meta[$name]) ? $this->_meta[$name] : '');
    }

    /**
     * @return int
     */
    public function getID() {
        return $this->_id;
    }

    protected function fetchMeta() {
        if (count($this->_meta) == 0) {
            $this->_meta = $this->get_details();
        }
    }

    /**
     * @param Iterator|array Associative array of slug/value of each meta to insert/update
     * @return bool
     */
    public function setMeta($meta) {
        if (!($meta instanceof \Iterator) && !is_array($meta)) {
            throw new Exception("Parameter must be iterator key/value pairing to update meta");
        }

        $db  = $this->_core->db();

        $updates = array();
        $lookup  = $db->qFetch("SELECT `itemID`, `slug` FROM `sysUGFields`");
        foreach ($lookup as $entry) {
            if (isset($meta[$entry['slug']])) {
                $updates[$entry['itemID']] = $meta[$entry['slug']];
            }
        }
        unset($meta);

        $qry = "INSERT INTO `sysUGFValues` (`userID`, `fieldID`, `value`) VALUES ";
        foreach ($updates as $key => $val) {
            $qry .= sprintf(" (%d, %d, '%s'), ", $this->_id, $key, $db->escape_string($val));
        }
        $qry  = substr($qry, 0, -2);
        $qry .= " ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";

        return (false === $db->query($qry) ? false : true);
    }

    /**
     * @param string
     * @param string
     */
    function set_meta($fieldLabel, $value) {
        //determine the keyID of this field
        $fieldID = $this->db->return_specific_item(false, "sysUGFields", "itemID", "--", "fieldLabel = '".$fieldLabel."'");

        if(is_numeric($fieldID)) {
            if(array_key_exists($fieldLabel, $this->info)) {

                //field exists for this user, lets update it
                $qry = sprintf("UPDATE sysUGFValues SET value='%s' WHERE userID='%d' AND fieldID='%d';",
                    $this->db->escape($value),
                    (int) $this->id,
                    (int) $fieldID);
                $this->db->query($qry);

            } else {
                //user does not have this value, insert the link first

                $qry = sprintf("INSERT INTO sysUGFValues (userID, fieldID, value, sysStatus, sysOpen) VALUES ('%d', '%d', '%s', 'active', '1');",
                    (int) $userID,
                    (int) $fieldID,
                    $this->db->escape($value));
                $this->db->query($qry);


            }
            return true;
        } else {
            return "Could not find the field [" . $fieldLabel . "] in sysUGFields, check your spelling and make sure it matches exactly.";

        }
    }

    /**
     * @param string
     * @return bool
     */
    public function activate($key) {
        $stmt = $this->db->query(sprintf("UPDATE `sysUsers` SET `sysStatus` = 'active' WHERE `itemID` = %d AND `regHash` = '%s'", $this->getID(), $this->db->escape_string($key)));
        return (boolean)$this->db->affected_rows();
    }

    /**
     * Send an email to this user to 
     */
    public function sendActivation() {
        $email  = $this->{'E-Mail'};
        $userID = $this->_id;

        $hash = $this->_core->getModule('AccountManagement')->generateToken();
        $qry = sprintf("UPDATE sysUsers SET `regHash` = '%s' WHERE `itemID` = %d",
            $hash,
            $userID);
        $this->_core->db()->query($qry);

        $activationLink = 'http://ownersbox.com/activate?token=' . $hash . '';

        $mail = new \stdClass;
        $mail->Subject = "Ownersbox.com account activation";
        $mail->Email   = $email;

        $mail->AltBody = "Hi there,
    
Welcome to Ownersbox.com!

Please follow the link below to activate your account:

{$activationLink}

Thanks,
- Ownersbox.com";
            
        $body = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/emails/activate-email.html');
        $body = str_replace("%ACTIVATION_LINK%", $activationLink, $body);
        $mail->Body = $body;
    
        if ($this->_core->sendEmail($mail->Email, $mail->Subject, $mail->Body , $mail->AltBody)) {
             return true;
        }

        return false;      
    }

    /**
     * GET META FOR THIS USER
     * returns the value of a passed meta label for a specific user, currently only handles single value meta items
     * @return string|bool
     * @deprecated
     */
    public function get_meta($fieldLabel) {
        $userID = $this->_id;

        $qry = sprintf("SELECT v.value
            FROM sysUGFields f
            LEFT JOIN sysUGFValues v ON f.itemID=v.fieldID
            WHERE v.sysOpen='1'
            AND f.fieldLabel='%s'
            AND v.userID='%d'",
            $this->db->escape($fieldLabel),
            (int) $userID);
        $res = $this->db->query($qry);

        if($this->db->valid($res)) {
            $tmp = $this->db->fetch_assoc($res);
            return $tmp['value'];
        }
        return false;

    }

    /**
     * GET DETAILS (GET ALL META FOR THIS USER)
     * @return array|bool
     * @deprecated
     */
    public function get_details() {
        $userID = $this->_id;

        $qry = sprintf("SELECT f.fieldLabel, v.value
            FROM sysUGFields f
            LEFT JOIN sysUGFValues v ON f.itemID=v.fieldID
            WHERE v.sysOpen='1'
            AND v.userID='%d'",
            (int) $userID);
        $res = $this->db->query($qry);

        if($this->db->valid($res)) {
            $meta = array();
            while($tmp = $this->db->fetch_assoc($res)) {
                $meta[$tmp['fieldLabel']] = $tmp['value'];
            }
            return $meta;
        }
        return false;
    }
    
    
    
    /**
     * Change the user's password
     * @param string
     * @return bool
     */
     
    public function changePassword($password)
    {
        if (empty($password)) {
            throw new Exception('Password can not be empty');
        }
        $stmt = $this->db->query(sprintf("UPDATE sysUsers SET `userIDPassword`='%s' WHERE itemID='%d'", $this->_core->secure()->hashPassword($password), $this->getID()));
        return (boolean)$this->db->affected_rows();
        
    
    
    }
}