<?php

header("content-type:image/jpeg");
function cropImageForThumb($nw,$nh,$path,$newPath){
        if (file_exists($path)){
                $iType = substr($path,strlen($path)-3);
                list($w,$h) = getimagesize($path);
                switch($iType) {
                        case "gif":
                                $simg = imagecreatefromgif($path);
                                break;
                        case "jpg":
                                $simg = imagecreatefromjpeg($path);
                                break;
                        case "png":
                                $simg = imagecreatefrompng($path);
                                break;
                        default:
                                break;
                }
                $dimg = imagecreatetruecolor($nw, $nh);

                imagecopyresampled($dimg,$simg,0,0,0,0,$nw,$nh,$w,$h);
                imagejpeg($dimg,null,100);
                imagedestroy($dimg);
        }
}

if (isset($_GET["jpeg"]) && isset($_GET['jpgw']) && isset($_GET['jpgh'])){ 
    cropImageForThumb((int)$_GET['jpgw'],(int)$_GET['jpgh'],dirname(dirname(dirname(__DIR__)))."/uploads/media/".urldecode($_GET['jpeg']),null);
}