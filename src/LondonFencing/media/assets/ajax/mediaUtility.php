<?php
require_once('../../../../../inc/init.php');
require_once dirname(dirname(__DIR__)).'/media.php';
require_once dirname(dirname(__DIR__)).'/Apps/adminMedia.php';

use LondonFencing\media as MED;
use LondonFencing\media\Apps as aMED;

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' || (isset($_GET['isAjax']) && trim($_GET['isAjax']) == 'y') && count($_POST) > 0) {
    
   $m = new aMED\adminMedia($db);
    
    switch ($_POST['operation']) {

        case "fetch_contact_item":

                echo $m->contact_sheet($_POST['mediaID']);

                break;
        
        case "newTag":
            
            echo $m->add_new_tags($_POST['value']);
            break;

        case "add_tag":
                header("Content-Type: application/json; charset=utf-8");

                if(!is_numeric($_POST['mediaID'])) {
                        $mediaID = explode("_", $_POST['mediaID']);
                        $_POST['mediaID'] = end($mediaID);
                } 

                if ($m->add_tag_to_media($_POST['mediaID'], $_POST['value'])) {
                        $r = array("status" => 1, "message" => "Tag was successfully added.");
                        print json_encode($r);
                } else {
                        $r = array("status" => 0, "message" => "Tag could not be added.");
                        print json_encode($r);
                }
                break;
       case "newCover":
                
                if (isset($_POST['value']) && (int)$_POST['value'] > 0 && isset($_POST['tag']) && (int)$_POST['tag'] > 0){
                    
                    echo $m->set_cover_image($_POST['value'],$_POST['tag']);
                }
                echo 'false';
                break;

        case "remove_tag":
                header("Content-Type: application/json; charset=utf-8");

                if(!is_numeric($_POST['mediaID'])) {
                        $mediaID = explode("_", $_POST['mediaID']);
                        $_POST['mediaID'] = end($mediaID);
                } 

                if ($m->remove_tag_from_media($_POST['mediaID'], $_POST['value'])) {
                        $r = array("status" => 1, "message" => "Tag was successfully removed.");
                        print json_encode($r);
                } else {
                        $r = array("status" => 0, "message" => "Tag could not be removed.");
                        print json_encode($r);
                }

                break;

        case "update_title":
                header("Content-Type: application/json; charset=utf-8");

                if(!is_numeric($_POST['mediaID'])) {
                        $mediaID = explode("_", $_POST['mediaID']);
                        $_POST['mediaID'] = end($mediaID);
                } 

                if ($m->update_media_property($_POST['mediaID'], "title",  $_POST['value'])) {
                        $r = array("status" => 1, "message" => "Caption was successfully updated.");
                        print json_encode($r);
                } else {
                        $r = array("status" => 0, "message" => "Caption could not be updated.");
                        print json_encode($r);
                }

        break;
    
}

//brendan@resolutionim.com (Jun 2011)
$db->close();
}
else{
    header('location:http://'.$_SERVER['SERVER_NAME']);
}