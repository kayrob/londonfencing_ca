<?php
namespace LondonFencing\Blog\Admin;

use Quipp\Core;
use Quipp\Module\ModuleInterface;
use Quipp\Module\AppInterface;
use \Exception as Exception;
$root = dirname(dirname(dirname(dirname(__DIR__))));
require_once($root."/vendors/twitteroauth/twitteroauth.php");

class BlogAdmin{

    protected $_db;
    
    public function __construct($db){
        if (is_object($db)){
            $this->_db = $db;
        }
        else{
            throw new Exception("You are not connected to a database");
        }
    }
    
    protected function getDateTweeted($itemID){
        $qry = sprintf("SELECT `dateTweeted` FROM `tblNews` WHERE `itemID` = '%d'",
            (int)$itemID
        );
        
        $res = $this->_db->query($qry);
        if ($this->_db->valid($res)){
            $row = $this->_db->fetch_assoc($res);
            return trim($row["dateTweeted"]);
        }
        return true;
    }
    
    protected function setDateTweeted($itemID){
        $qry = sprintf("UPDATE `tblNews` SET `dateTweeted` = %d WHERE `itemID` = '%d'",
            date("U"),
            (int)$itemID
        );       
        $res = $this->_db->query($qry);

    }
    
    protected function getOAuthTokens($siteID){
        $qry = sprintf("SELECT * FROM `tblTwitterOAuth` WHERE `siteID` = %d",
            (int)$siteID
        );
        $res = $this->_db->query($qry);
        if ($this->_db->valid($res)){
            $twAuth = $this->_db->fetch_assoc($res);
            return array(trim($twAuth["consumerKey"]), trim($twAuth["consumerSecret"]), trim($twAuth["accessToken"]), trim($twAuth["accessTokenSecret"]));
        }
        return array(false,false,false,false);
    }
    
    public function autoTweet($itemID,$domains){
    
        if (is_numeric($itemID) && (int)$itemID > 0){
            if ((bool)$this->getDateTweeted($itemID) === false){
                $qry = sprintf("SELECT `title`, `siteID`, `autoTweet`, `slug` FROM `tblNews` WHERE `itemID` = %d",
                    (int)$itemID
                );
                $res = $this->_db->query($qry);
                if ($this->_db->valid($res)){
                    $row = $this->_db->fetch_assoc($res);
                    if (isset($domains[trim($row["siteID"])])){
                        
                        $share = '"'.trim($row["title"]).'" now on '.$domains[trim($row["siteID"])].": http://".$domains[trim($row["siteID"])]."/blog/".trim($row["slug"]);
                        
                        list($consumerKey, $consumerSecret, $oAuthToken, $oAuthSecret) = $this->getOAuthTokens(trim($row["siteID"]));
                        
                        if ($consumerKey !== false){
                            $tweet = new TwitterOAuth($consumerKey, $consumerSecret, $oAuthToken, $oAuthSecret);
                            if (is_object($tweet)){
                                try{
                                    $sent = $tweet->post('statuses/update',array('status' => $share));
                                    $this->setDateTweeted($itemID);
                                    return true;
                                }
                                catch (Exception $e){
                                    echo $e->getMessage();
                                    return false;
                                }
                            }
                        }
                    }
                }
            }
        }
        return true;
    }
    public function getSites($userID){
        if (is_numeric($userID) && (int)$userID > 0){
            $qry = sprintf("SELECT s.`itemID`, s.`title` FROM `sysSites` AS s INNER JOIN `sysUSites` AS us ON s.`itemID` = us.`siteID` WHERE us.`userID` = %d",
                (int)$userID
            );
            $res = $this->_db->query($qry);
            if ($this->_db->valid($res)){
                while ($row = $this->_db->fetch_assoc($res)){
                    $sites[trim($row["itemID"])] = trim($row["title"]);
                }
                return $sites;
            }
        }
        return false;
    }
}