<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require_once $root ."/inc/init.php";


require $root . '/admin/classes/Editor.php';
require dirname(dirname(__DIR__)) . '/media.php';

use LondonFencing\media\Apps as MED;

if ($auth->has_permission('canEditMedia')){

$meta['title'] = 'Media Manager';
$meta['title_append'] = ' &bull; Quipp CMS';

if (!isset($_GET['id'])) { $_GET['id'] = null; }

$te = new Editor();
$m  = new MED\adminMedia($db);

$quipp->css[] = "/src/LondonFencing/media/assets/vendors/tagInput/jquery.tagsinput.css";
$quipp->css[] = "/src/LondonFencing/media/assets/css/media.css";
$quipp->js['footer'][] = "/js/jquery.infinitescroll.min.js";
$quipp->js['footer'][] = "/src/LondonFencing/media/assets/vendors/tagInput/jquery.tagsinput.min.js";
$quipp->js['footer'][] = "/src/LondonFencing/media/assets/js/adminMedia.js";

//set the primary table name
$primaryTableName = "tblMedia";

$fields['img'] = array(
    'label'   => "Upload",
    'dbColName'  => "fileItem",
    'tooltip'   => "Currently supports photos (.jpg, .png)",
    'writeOnce'  => false,
    'widgetHTML' => '<input style="width:300px;" type="file" class="uniform" id="FIELD_ID" name="FIELD_ID" value="FIELD_VALUE" />',
    'valCode'   => "RQvalFILE",
    'dbValue'   => false,
    'fileUpload' => true,
    'stripTags'  => false
);

$fields['tag'] = array(
    'label'   => "Edit Tag",
    'dbColName'  => "tag",
    'tooltip'   => "",
    'writeOnce'  => false,
    'widgetHTML' => '<input style="width:300px;" type="text" class="uniform" id="FIELD_ID" name="FIELD_ID" value="FIELD_VALUE" />',
    'valCode'   => "RQvalALPH",
    'dbValue'   => false,
    'stripTags'  => true
);

$_POST['dbaction'] = (isset($_POST['dbaction']))? trim($_POST['dbaction']):null;
if (isset($_GET['action'])) {
        $_POST['dbaction'] = $_GET['action'];
}

if (!empty($_POST)){
    global $message;
    switch($_POST['dbaction']){
            
        case "img":
            
                $ALLOWED_MIME_TYPES = array(
                    'image/jpeg'   => 'jpg',
                    'image/pjpeg'  => 'jpg',
                    'image/png'    => 'png',
                    'image/x-png'  => 'png'
                );

                $thumbnails = array (
                    'med'     => array(
                        'l'        => 100,
                        'w'        => 100,
                        'adaptive' => true
                    ),
                    'home' => array(
                        'l'        => 310, 
                        'w'        => 960,
                        'adaptive' => true
                    ),
                    'banner' => array(
                        'l'        => 800, 
                        'w'        => 800,
                        'adaptive' => false
                    )
                );
                if (!empty($_FILES) && isset($_FILES['RQvalFILEUpload'])){
                    foreach($_FILES['RQvalFILEUpload']['name'] as $index => $fileName){
                        if ($_FILES['RQvalFILEUpload']['error'][$index] == 0){
                            $_FILES['upload_'.$index]['name'] = $fileName;
                            $_FILES['upload_'.$index]['error'] = 0;
                            $_FILES['upload_'.$index]['tmp_name'] = $_FILES['RQvalFILEUpload']['tmp_name'][$index];
                            $_FILES['upload_'.$index]['size'] = $_FILES['RQvalFILEUpload']['size'][$index];
                            $_FILES['upload_'.$index]['type'] = $_FILES['RQvalFILEUpload']['type'][$index];
                            $photo['upload'][] = upload_file('upload_'.$index, $root. '/uploads/media/', $ALLOWED_MIME_TYPES, $thumbnails);
                        } 
                    }
                    if (isset($photo['upload'])){
                        $message = "";
                        $insQry = "";
                        foreach ($photo['upload'] as $photoPath){
                            if (strstr($photoPath, '<strong>Error') === false){
                                $insQry = sprintf("INSERT INTO `tblMedia` (`sysOpen`,`sysStatus`,`sysDateCreated`,`sysDateLastMod`,`sysUserLastMod`,`fileItem`,`title`) 
                                    VALUES ('1','active',NOW(),NOW(),%d, '%s','%s');",
                                    $user->id,
                                        $photoPath,
                                        $photoPath
                                    );
                                $res = $db->query($insQry);
                                if (isset($_POST['OPvalALPHTag_Name']) && trim($_POST['OPvalALPHTag_Name']) != ''){
                                    $m->add_tag_to_media($db->insert_id(), $db->escape($_POST['OPvalALPHTag_Name']));
                                }
                                $db->free_result();
                            }
                            else{
                                $message .= $photoPath . '<br />';
                            }
                        }
                    }
                    else{
                        $message = "<h3>Your photos could not be uploaded. Please check the size and file type</h3>";
                    }
                }
            break;
        
        case "uTag":
            if (validate_form($_POST) && (int)$_POST['OPvalNUMBTag_ID'] > 0){
                $upTagQry = sprintf("UPDATE `tblMediaTags` SET `tag` = '%s' WHERE `itemID` = '%d'",   
                        (string)$db->escape($_POST['RQvalALPHEdit_Tag'],true),
                        (int)$db->escape($_POST['OPvalNUMBTag_ID'])
                        );
                $res = $db->query($upTagQry);
                $db->free_result();
            }
            else{
                $message = "<h3>You can not submit an empty tag. <em>Text required</em></h3>";
            }
            break;
        
       case "deleteTag":
            if (is_numeric($_GET['id']) && (int)$_GET['id'] > 0){
                ($m->delete_tag((int)$_GET['id']) == 1) ? header('location:/admin/apps/media/index') : $message = "Your tag could not be deleted";
            }
            else{
                $message = "<h3>You must select a tag to delete it</h3>";
            }
           break;
       
        case "delete":
            if (is_numeric($_GET['id']) && (int)$_GET['id'] > 0){
                ($m->delete_media_item((int)$_GET['id']) == 1) ? header('location:/admin/apps/media/index') : $message = "Your photo could not be deleted";  
            }
            else{
                $message = "<h3>You must select an image to delete it</h3>";
            }
            break;
        default:
            
            break;
        
        
    }
    
    
}


include $root. "/admin/templates/header.php";
?>

<h1>Media Manager<?php if (isset($_GET['tag'])){ echo ' : Edit Tag';}?></h1>
<p>This allows the ability to post photos on the public front end, and place collections of them on specific pages of your website.</p>
<?php

$page = (isset($_GET['page']) ? $_GET['page'] : 1);
$tags = (isset($_GET['tag']) ? Array($_GET['tag']) : Array());
$tagRa = $m->get_tags();
$media_items = $m->get_media_list(false, $page, $tags);
if (isset($message) && $message != ""){
    echo $message;
}

?>
<div class="boxStyle">
    <div class="boxStyleContent">
    <div class="boxStyleHeading">
            <h2><?php echo (!empty($tags) ? 'Tag: '.$tagRa[$tags[0]] : 'All Media'); ?></h2>
    </div>
        <div class="clearfix">&nbsp;</div>
        <div id="template">
<?php

if (!empty($tags)){
?>
        
            <form action="/admin/apps/media/index?tag=<?php echo $_GET['tag']; ?>" method="post" name="frmEditTag" id="frmEditTag" class="tableEditorForm"enctype="multipart/form-data">
                <table>
                    <tr>
                     <td style="width:30%"><label for="RQvalALPHEdit_Tag"> Edit Tag </label></td>
                     <td style="width:60%"><input type="text" id="RQvalALPHEdit_Tag" class="uniform" name="RQvalALPHEdit_Tag" value="<?php echo $tagRa[$tags[0]]; ?>" /></td>
                    <td><input type="submit" class="btnStyle blue" id="updateTag" name="updateTag" value="Update" /></td>
                    </tr>
                </table>
                
            <input type="hidden" name="nonce" value="<?php echo Quipp()->config('security.nonce');?>" />
            <input type="hidden" name="OPvalNUMBTag_ID" value="<?php echo (isset($tags[0])? $_GET['tag'] : 0); ?>" />
            <input type="hidden" name="dbaction" value="uTag" />
            </form>
<?php        
        }
?>
            <form action="/admin/apps/media/index<?php echo (isset($tags[0])? "?tag=".$_GET['tag'] : ''); ?>" method="post" name="frmUploadMedia" id="frmUploadMedia" class="tableEditorForm" enctype="multipart/form-data">
            <table>
                    <tr>
                     <td style="width:30%"><label for="RQvalFILEUpload">Upload Photos</label><p>(max 10 x 2MB in a single upload. Use .png or .jpg. Home images should be optimized for 960 x 310)</p></td>
                     <td style="width:60%"><input type="file" name="RQvalFILEUpload[]" id="RQvalFILEUpload"  multiple size="250" class="btnStyle" style="padding-top:2px"/></td>
                    <td><input type="submit" class="btnStyle" name="submitUpload" id="submitUpload" value="Upload" /></td>
                    </tr>  
            </table>
            <input type="hidden" name="nonce" value="<?php echo Quipp()->config('security.nonce');?>" />
            <input type="hidden" name="OPvalALPHTag_Name" value="<?php echo (isset($tags[0])? $tagRa[$tags[0]] : ''); ?>" />
            <input type="hidden" name="dbaction" value="img" />
            </form>
<?php
        if (empty($tags)){
  ?>          
            <form onsubmit="return false;" class="tableEditorForm" id="frmNewTags">
                <table>
                    <tr>
                     <td style="width:30%"><label for="upload_tags_input">Create New Tags</label></td>
                     <td style="width:60%"><input type="text" class="mediaTags" id="upload_tags_input" class="upload_tags_input" name="newTags"/></td>
                    <td><input type="button" class="btnStyle blue" id="submitTags" name="submitTags" value="Add Tags" /></td>
                    </tr>
                </table>
            <input type="hidden" name="nonce" value="<?php echo Quipp()->config('security.nonce');?>" />
            <input type="hidden" name="dbaction" value="tag" />
            </form>
            <div class="tableEditorForm" id="dvTagCloud">
                <table>
                    <tr>
                     <td style="width:30%"><label>Current Tags</label><p>Select a Tag to filter</p></td>
                     <td>
                         <ul id="tag_cloud">
                <?php 
                    array_map(function($tag_info) {
                    $tag    = filter_var($tag_info['tag'], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_ENCODE_HIGH);

                    $active = (isset($_GET['tag']) && $_GET['tag'] == $tag_info['tagID'] ? ' active' : '');

                    echo <<<EOL
                <li><a href="/admin/apps/media/index?tag={$tag_info['tagID']}" title="{$tag_info['tag_count']} photos tagged">{$tag}</a></li>
EOL;
                }, $m->get_tag_cloud()); ?>
            </ul>
                     </td>
                    </tr>
                </table>
                    
            </div>
            <div class="clearfix">&nbsp;</div>
<?php
        }
?>        
            <p style="padding: 5px 0 15px 0"><span class="contactSheetP">Photos</span> Displaying <span id="currently"> <?php echo $media_items->count(); ?></span> of <span id="total"><?php echo $media_items->getTotal(); ?></p>
              <?php print "<div id=\"contactSheet\">";
            print $m->getContactSheetMarkup($media_items, $tags);
            print "</div>";

            $url_parts  = parse_url(filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_UNSAFE_RAW));
            $query_data = Array();
            if (isset($url_parts['query'])) {
                $query_vars = explode('&', $url_parts['query']);

                foreach ($query_vars as $var) {
                    list($key, $val)  = explode('=', $var);
                    $query_data[$key] = $val;
                }
            }
?>
            <div class="clearfix">&nbsp;</div>
<?php 
         if (!empty($tags)){
            echo '<div class="boxReturn">';
            echo '<input type="button" class="btnStyle green" id="cancel" name="cancel" value="Return"  onclick="window.location=\'/admin/apps/media/index\'"/>';
            echo '<input type="button" class="btnStyle red" id="deleteTag" name="deleteTag" value="Delete Tag"  onclick="javascript:confirmDelete(\'?action=deleteTag&id='.$_GET['tag'].'\')"/>';
             echo '</div>';
             echo '<div class="clearfix">&nbsp;</div>';
         }
?>
</div>
</div>
<?php

include $root . "/admin/templates/footer.php";
}
else{
    $auth->boot_em_out(1);
}