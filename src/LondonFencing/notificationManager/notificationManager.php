<?php
namespace LondonFencing\notificationManager;

use \Exception as Exception;
use \PHPMailer;

class notificationManager{
    protected $_db;
    public $mailer;
    public $errs = array();
    protected $_from = 'info@londonfencing.com';
    
    /** Create class properties for db connection and the php mailer
     * @access public
     * @param object $db
     * @see PHPMailer\PHPMailer::construct()
     * @throws Exception 
     */
    public function __construct($db){
        if (is_object($db)){
            $this->_db = $db;
            $this->mailer = new PHPMailer\PHPMailer();
        }
        else{
            throw new Exception("You are not connected to a database");
        }
    }
    /**
     * Send an email to a single recipient. Requires address and body content
     * @access protected
     * @param string $to
     * @param string $body
     */
    protected function sendMailSingle($to, $body){
        $this->mailer->addAddress($to);
        $this->mailer->Body = $body;
        if (!$this->mailer->Send()){
            $this->errs[] = $to;
        }
        $this->mailer->ClearAddresses();
    }
    /**
     * Send a single email to a batch of individuals. Uses addAddress() so email addresses are visible
     * @access protected
     * @param string $body 
     */
    protected function sendMailBatch($body){
        $this->mailer->Body = $body;
        if (!$this->mailer->Send()){
            $this->errs[] = $this->mailer->ErrorInfo;
        }
        $this->mailer->ClearAddresses();
    }
    /**
     * Get email list for beginner or intermediate class members. This can be used for multiple mailing options
     * @access protected
     * @param string $emailID
     * @return object
     */
    protected function getEmailClassAddresses(string $emailID){
        $qry = sprintf("SELECT cr.`firstName`, cr.`lastName`, cr.`email`,cr.`parentName`, cr.`registrationKey`, c.`sessionName` 
                FROM `tblClassesRegistration` AS cr INNER JOIN `tblClasses` AS c ON cr.`sessionID` = c.`itemID` WHERE cr.`itemID` IN (%s)",
                    $this->_db->escape($emailID,true)
            );
        return $this->_db->query($qry);
    }
     /**
     * Get email list for advanced (regular) class members. This can be used for multiple mailing options
     * @access protected
     * @param string $emailID
     * @return object
     */
    protected function getEmailMemberAddresses(string $emailList){
        $qry = sprintf("SELECT m.`firstName`, m.`lastName`, m.`email`,m.`parentName`, m.`cffNumber` 
                FROM `tblMembers` AS m WHERE m.`itemID` IN (%s)",
                    $this->_db->escape($emailList,true)
            );
        return $this->_db->query($qry);
    }
    /**
     * Set initial email headers and body content.
     * @param string $subject
     * @param string $content
     * @param string $format
     * @return string 
     */
    protected function initEmailContent($subject, $content, $format){
        $this->mailer->SetFrom($this->_from);
        $this->mailer->Subject = $this->_db->escape($subject,true);
        $this->mailer->IsHTML(true);

        $content = $this->_db->escape($content,true);
        $content = str_replace('\r','',$content);
        $content = rtrim($content,"\n");
        $content = str_replace('\n',' <br />',$content);
        $content = preg_replace('%http://([^\s]+)%','<a href="http://$1">http://$1</a>',$content);

        if (strtolower($format) == "html"){
            $body = file_get_contents(dirname(__DIR__)."/StaticPage/emailTemplate.html");
            $body = str_replace('<h5>%TITLE%</h5>','',$body);
            $body = str_replace('%SERVERNAME%',$_SERVER['SERVER_NAME'],$body);
            $body = str_replace('%BODY%',$content,$body);
        }
        else{
            $body = $content;
        }
        return $body;
    }
    /**
     * Creates HTML email to send to participants of beginner and intermeidate classes
     * format = html uses the formatted emailTemplate. All emails are html for allowance of urls
     * An email is always sent to the administrator
     * @access public
     * @param string $subject
     * @param string $content
     * @param array $eList
     * @param string $batch
     * @param string $format
     * @see getEmailClassAddresses()
     * @see initEmailContent()
     * @see sendMailSingle()
     * @see sendMailBatch()
     * @return boolean 
     */
    public function emailClassParticipants($subject, $content, $eList, $batch, $format){
        $this->mailer->ClearAddresses();
        if (is_array($eList)){
            $emailID = implode(",",$eList);
            $res = $this->getEmailClassAddresses($emailID);
            
            if ($res->num_rows > 0){
                
                $body = $this->initEmailContent($subject, $content, $format);
                
                $body = "<p>".$body."</p>";
                while ($row = $this->_db->fetch_assoc($res)){
                    $body = str_replace('%SESSION%',trim($row["sessionName"]),$body);
                    if ($batch == "single"){
                         $send = (trim($row["parentName"]) != "") ? str_replace('%NAME%',trim($row["parentName"]),$body): str_replace('%NAME%',trim($row["firstName"]),$body);
                         $send = str_replace('%REGKEY%',trim($row["registrationKey"]),$send);
                         $this->sendMailSingle(trim($row['email']), stripslashes($send));
                    }
                    else{
                         $this->mailer->addAddress(trim($row["email"]));
                    }
                    $addr[] = trim($row['email']);
                }
                if ($batch == "batch"){
                    $send = str_ireplace('%NAME%','', $body);
                    $send = str_ireplace('%REGKEY%','', $send);
                    $this->sendMailBatch(stripslashes($send));
                }
            }
        }
        if (empty($this->errs)){
            $this->mailer->addAddress($this->_from);
            $this->mailer->Subject = "Email Sent:".$subject;
            $this->mailer->Body = '<p>You sent an email to the following recipients: <br /><br />'.implode('<br />',$addr).'<br /><br /></p>';
            $this->mailer->Body .= '<p>---START---</p>'.$send.'</p><p>---END---</p><p>&nbsp;<p>Note that if you selected the individual option that you 
                are viewing the last email sent. Content was personalized for each individual</p>';
            $this->mailer->Send();
        }
        return false;
    }
     /**
     * Creates HTML email to send to participants of any class (beginner, intermediate, or advanced)
     * format = html uses the formatted emailTemplate. All emails are html for allowance of urls
     * An email is always sent to the administrator
     * @access public
     * @param string $subject
     * @param string $content
     * @param array $eList
     * @param string $batch
     * @param string $format
     * @see getEmailClassAddresses()
     * @see getEmailMemberAddresses()
     * @see initEmailContent()
     * @see sendMailSingle()
     * @see sendMailBatch()
     * @return boolean 
     */
    public function emailAllMembers($subject, $content, $eList, $aList, $batch, $format){
        $this->mailer->ClearAddresses();
        if (is_array($eList)){
            $eListID = implode(",",$eList);
            $eRes = $this->getEmailClassAddresses($eListID);
        }
        if (is_array($aList)){
            $aListID = implode(",",$aList);
            $aRes = $this->getEmailMemberAddresses($aListID);
        }
        if ((isset($eRes) && $eRes->num_rows > 0) || (isset($aRes) && $aRes->num_rows > 0)){
            $body = $this->initEmailContent($subject, $content, $format);
            $body = "<p>".$body."</p>";
            if (isset($eRes) && $eRes->num_rows > 0){
                while ($row = $this->_db->fetch_assoc($eRes)){
                    if ($batch == "single"){
                         $send = (trim($row["parentName"]) != "") ? str_replace('%NAME%',trim($row["parentName"]),$body): str_replace('%NAME%',trim($row["firstName"]),$body);
                         $this->sendMailSingle(trim($row['email']), stripslashes($send));
                    }
                    else{
                         $this->mailer->addAddress(trim($row["email"]));
                    }
                    $addr[] = trim($row['email']);
                }
            }
            if (isset($aRes) && $aRes->num_rows > 0){
                while ($arow = $this->_db->fetch_assoc($aRes)){
                    if ($batch == "single"){
                         $send = (trim($arow["parentName"]) != "") ? str_replace('%NAME%',trim($arow["parentName"]),$body): str_replace('%NAME%',trim($arow["firstName"]),$body);
                         $this->sendMailSingle(trim($arow['email']), stripslashes($send));
                    }
                    else{
                         $this->mailer->addAddress(trim($arow["email"]));
                    }
                    $addr[] = trim($arow['email']);
                }
            }
            
            if ($batch == "batch"){
                $send = str_ireplace('%NAME%','', $body);
                $this->sendMailBatch(stripslashes($send));
            }
            if (empty($this->errs)){
                $this->mailer->addAddress($this->_from);
                $this->mailer->Subject = "Email Sent:".$subject;
                $this->mailer->Body = '<p>You sent an email to the following recipients: <br /><br />'.implode('<br />',$addr).'<br /><br /></p>';
                $this->mailer->Body .= '<p>---START---</p>'.$send.'</p><p>---END---</p><p>&nbsp;<p>Note that if you selected the individual option that you 
                    are viewing the last email sent. Content was personalized for each individual</p>';
                $this->mailer->Send();
                return true;
            }
         }
         return false;
    }
}
