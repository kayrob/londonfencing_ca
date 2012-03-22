<?php
namespace LondonFencing\notificationManager;

use \Exception as Exception;
use \PHPMailer;

class notificationManager{
    protected $_db;
    public $mailer;
    public $errs = array();
    
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
     * Creates HTML email to send to participants of beginner and intermeidate classes
     * format = html uses the formatted emailTemplate. All emails are html for allowance of urls
     * An email is always sent to the administrator
     * @access public
     * @param string $subject
     * @param string $content
     * @param array $eList
     * @param string $batch
     * @param string $format
     * @see sendMailSingle()
     * @see sendMailBatch()
     * @return boolean 
     */
    public function emailClassParticipants($subject, $content, $eList, $batch, $format){
        $this->mailer->ClearAddresses();
        if (is_array($eList)){
            $emailID = implode(",",$eList);
            $qry = sprintf("SELECT cr.`firstName`, cr.`lastName`, cr.`email`,cr.`parentName`, cr.`registrationKey`, c.`sessionName` 
                FROM `tblClassesRegistration` AS cr INNER JOIN `tblClasses` AS c ON cr.`sessionID` = c.`itemID` WHERE cr.`itemID` IN (%s)",
                    $this->_db->escape($emailID,true)
            );
            $res = $this->_db->query($qry);
            if ($res->num_rows > 0){
                //$this->mailer->SetFrom('info@londonfencing.ca');
                $this->mailer->SetFrom("robertsonkaren@rogers.com");
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
            $this->mailer->addAddress("robertsonkaren@rogers.com");
            $this->mailer->Subject = "Email Sent:".$subject;
            $this->mailer->Body = '<p>You sent an email to the following recipients: <br /><br />'.implode('<br />',$addr).'<br /><br /></p>';
            $this->mailer->Body .= '<p>---START---</p>'.$send.'</p><p>---END---</p><p>&nbsp;<p>Note that if you selected the individual option that you 
                are viewing the last email sent. Content was personalized for each individual</p>';
            $this->mailer->Send();
            return true;
        }
        return false;
    }
}
