<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require_once $root ."/inc/init.php";

require $root . '/admin/classes/Editor.php';
require dirname(dirname(__DIR__)) . '/media.php';

use LondonFencing\media\Apps as MED;

$meta['title'] = 'Media Manager';
$meta['title_append'] = ' &bull; Quipp CMS';

if (!isset($_GET['id'])) { $_GET['id'] = null; }

$te = new Editor();
$m  = new MED\adminMedia($db);

$quipp->css[] = "/src/LondonFencing/media/assets/vendors/tagit/css/tagit-simple-green.css";
$quipp->css[] = "/src/LondonFencing/media/assets/vendors/tagInput/jquery.tagsinput.css";
$quipp->js['footer'][] = "/js/jquery.infinitescroll.min.js";
//$quipp->js['footer'][] = '/js/jquery.json-2.2.min.js';
$quipp->js['footer'][] = "/src/LondonFencing/media/assets/vendors/tagInput/jquery.tagsinput.min.js";
$quipp->js['footer'][] = "/src/LondonFencing/media/assets/vendors/tagit/tagit.js";
$quipp->js['footer'][] = "/src/LondonFencing/media/assets/js/adminMedia.js";
//$quipp->js['footer'][] = "/admin/js/swfupload/swfupload.js";
//$quipp->js['footer'][] = "/admin/js/media/handlers.js";



//set the primary table name
$primaryTableName = "tblMedia";

//editable fields
$fields[] = array(
    'label'   => "Title/Caption",
    'dbColName'  => "title",
    'tooltip'   => "A short description of the media",
    'writeOnce'  => false,
    'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
    'valCode'   => "RQvalALPH",
    'dbValue'   => false,
    'stripTags'  => true
);

$fields[] = array(
    'label'   => "File",
    'dbColName'  => "fileItem",
    'tooltip'   => "Currently supports photos",
    'writeOnce'  => false,
    'widgetHTML' => '<input style="width:300px;" type="file" class="uniform" id="FIELD_ID" name="FIELD_ID" value="FIELD_VALUE" />',
    'valCode'   => "RQvalALPH",
    'dbValue'   => false,
    'fileUpload' => true,
    'stripTags'  => false
);


$fields[] = array(
    'label'   => "Active",
    'dbColName'  => "sysStatus",
    'tooltip'   => "Use this banner when it is linked to a page",
    'writeOnce'  => false,
    'widgetHTML' => '<input type="checkbox" id="FIELD_ID" name="FIELD_ID" class="uniform" value="active" FIELD_VALUE />',
    'valCode'   => "",
    'dbValue'   => false,
    'stripTags'  => false
);

/*

if($db->num_rows($db->query("SELECT itemID FROM tblBanners WHERE useAlways = '1' AND itemID = '$_GET[id]'")) > 0){ $checked = "checked='checked'"; }else{ $checked = ""; }

$fields[] = array(
    'label'   => "Default",
    'dbColName'  => "useAlways",
    'tooltip'   => "Use to this banner when no links are set",
    'writeOnce'  => false,
    'widgetHTML' => "<input type=\"checkbox\" id=\"FIELD_ID\" name=\"FIELD_ID\" class=\"uniform\" value=\"1\" $checked FIELD_VALUE />",
    'valCode'   => "",
    'dbValue'   => false,
    'stripTags'  => false
); */


//dbaction = database interactivity, these standard queries will do for most single table interactions, you may need to replace with your own

if (!isset($_POST['dbaction'])) {
    $_POST['dbaction'] = null;

    if (isset($_GET['action'])) {
        $_POST['dbaction'] = $_GET['action'];
    }
}

if (!empty($_POST) && validate_form($_POST)) {

    if (isset($_FILES['RQvalALPHFile'])) {
        $ALLOWED_MIME_TYPES = array(
            'image/jpeg'   => 'jpg',
            'image/pjpeg'  => 'jpg',
            'image/png'    => 'png',
            'image/x-png'  => 'png'
        );

        $thumbnails = array (
            'med'     => array(
                'l'        => 120,
                'w'        => 120,
                'adaptive' => true
            ),
            'large' => array(
                'l'        => 800, 
                'w'        => 800,
                'adaptive' => false
            )
        );

    $photo['RQvalALPHFile'] = upload_file('RQvalALPHFile', $_SERVER['DOCUMENT_ROOT'] . '/uploads/media/', $ALLOWED_MIME_TYPES, $thumbnails);

        if (stristr($photo['RQvalALPHFile'], '<strong>') && (isset($_POST['RQvalALPHFile_keep']) && $_POST['RQvalALPHFile_keep'] == 'no' || !isset($_POST['RQvalALPHFile_keep']))) {
             $message = $photo['RQvalALPHFile'];
         }
    }


if ($message == '') { 
        switch ($_POST['dbaction']) {
        case "insert":

            //this insert query will work for most single table interactions, you may need to cusomize your own

            //the following loop populates 2 strings with name value pairs
            //eg.  $fieldColNames = 'articleTitle','contentBody',
            //eg.  $fieldColValues = 'Test Article Title', 'This is my test article body copy',
            //yell($_GET);
            //yell($fields);
            $fieldColNames  = '';
            $fieldColValues = '';
            foreach ($fields as $dbField) {
                if ($dbField['dbColName'] != false) {
                    $requestFieldID = $dbField['valCode'] . str_replace(" ", "_", $dbField['label']);
                    if ($dbField['dbColName'] == 'sysStatus') {
                        if (!isset($_POST[$requestFieldID])) {
                            $_POST[$requestFieldID] = 'inactive';
                        }
                    }
                    if ($dbField['dbColName'] == 'isFlagged') {    
                        if (!isset($_POST[$requestFieldID])) {
                            $_POST[$requestFieldID] = '0';
                        }
                    }



                    if (isset($dbField['fileUpload']) && $dbField['fileUpload'] === true) { 

                        if (!stristr($photo['RQvalALPHFile'], '<strong>')) {
                            $fieldColNames .= "`" . $dbField['dbColName'] . "`,";
                            $fieldColValues .= "'" . $db->escape($photo[$requestFieldID], $dbField['stripTags']) . "',";
                        }
                    } else {
                        $fieldColNames .= "`" . $dbField['dbColName'] . "`,";
                        $fieldColValues .= "'" . $db->escape($_POST[$requestFieldID], $dbField['stripTags']) . "',";
                    }
                }
            }

            //trim the extra comma off the end of both of the above vars
            $fieldColNames = substr($fieldColNames, 0, strlen($fieldColNames) - 1);
            $fieldColValues = substr($fieldColValues, 0, strlen($fieldColValues) - 1);



            $qry = sprintf("INSERT INTO %s (%s, sysUserLastMod, sysDateLastMod, sysDateCreated) VALUES (%s, '%d', %s, %s)",
                (string) $primaryTableName,
                (string) $fieldColNames,
                (string) $fieldColValues,
                $user->id,
                $db->now,
                $db->now
            );

            print $te->commit_a_modify_action($qry, "Insert", true);
            break;


        case "update":

            //yell("updating..");
            //this default update query will work for most single table interactions, you may need to cusomize your own
            $fieldColNames  = '';
            $fieldColValues = '';
            foreach ($fields as $dbField) {
                if ($dbField['dbColName'] != false) {
                    $requestFieldID = $dbField['valCode'] . str_replace(" ", "_", $dbField['label']);

                    //if the new banner is going to be to the default, remove the old default
                    if($dbField['dbColName'] == "useAlways" && $_POST[$requestFieldID] == 1){
                        $db->query("UPDATE tblBanners SET useAlways = '0' WHERE useAlways = '1'");
                    }

                    if ($dbField['dbColName'] == 'sysStatus') {    
                        if (!isset($_POST[$requestFieldID])) {
                            $_POST[$requestFieldID] = 'inactive';
                        }
                    }

                    if ($dbField['dbColName'] == 'isFlagged') {    
                        if (!isset($_POST[$requestFieldID])) {
                            $_POST[$requestFieldID] = '0';
                        }
                    }

                    if (isset($dbField['fileUpload']) && $dbField['fileUpload'] === true) { 

                        if (!stristr($photo['RQvalALPHFile'], '<strong>')) {
                            $fieldColValue = "'" . $db->escape($photo[$requestFieldID], $dbField['stripTags']) . "',";
                            $fieldColNames .= "" . $dbField['dbColName'] . " = " . $fieldColValue;
                        }
                    } else {
                        $fieldColValue = "'" . $db->escape($_POST[$requestFieldID], $dbField['stripTags']) . "',";
                        $fieldColNames .= "" . $dbField['dbColName'] . " = " . $fieldColValue;
                    }

                }
            }

            //trim the extra comma off the end of the above var
            $fieldColNames = substr($fieldColNames, 0, strlen($fieldColNames) - 1);

            $qry = sprintf("UPDATE %s SET %s, sysUserLastMod='%d', sysDateLastMod=NOW() WHERE itemID = '%s'", (string) $primaryTableName, (string) $fieldColNames, $user->id, (int) $_POST['id']);
            yell($qry);

            print $te->commit_a_modify_action($qry, "Update", true);
            break;

        case "delete":
            $m->remove_media($_GET['id']);
            header('Location:' . $_SERVER['PHP_SELF'] . '?delete=true');
            break;
        }
    }
} else {
    $_GET['view'] = 'edit';
}

include $root. "/admin/templates/header.php";

    // Logic
    $page = (isset($_GET['page']) ? $_GET['page'] : 1);
    $tags = (isset($_GET['tag']) ? Array($_GET['tag']) : Array());
    $media_items = $m->get_media_list(false, $page, $tags);
    print_r($tags);
?>

<h1>Media Manager</h1>
<p>This allows the ability to post photos and other media on the public front end, and place collections of them on specific pages of your website.</p>

<div class="boxStyle">
    <div class="boxStyleContent">
        <div class="boxStyleHeading">
            <h2>Edit</h2>
            <div id="divFileProgressContainer">&nbsp;</div>
            <div class="boxStyleHeadingRight">
                <?php if (!isset($_GET['view'])): ?>
                <div id="upload_tags">
                    <input type="text" class="mediaTags" id="upload_tags_input" class="upload_tags_input" />
                </div>
                <?php endif; ?>

                <table><tr> 
                <td> <span style="display:block; padding:5px; margin:5px; width:200px; height:40px;" id="newItemMulti">Upload Batch</span> </td>
                <td> 
                <?php print "<input type=\"button\" class=\"btnStyle blue\" name=\"newItem\" id=\"newItem\" onclick=\"javascript:window.location.href='" . $_SERVER['PHP_SELF'] . "?view=edit';\" value=\"New (Single Upload)\" />"; ?>
                </td></tr></table>



            </div>
        </div>
        <div class="clearfix">&nbsp;</div>

        <?php if (!isset($_GET['view'])): ?>
        <div class="boxStyleHeading">
            <h2>Info</h2>
            <div id="count_info">
                Displaying <span id="currently"><?php echo $media_items->count(); ?></span> of <span id="total"><?php echo $media_items->getTotal(); ?>
            </div>

            <ul id="tag_cloud">
                <?php array_map(function($tag_info) {
                    $tag    = filter_var($tag_info['tag'], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_ENCODE_HIGH);

                    $point  = 5;
                    $weight = round($tag_info['tag_count'] / $point, 0) * $point;

                    $active = (isset($_GET['tag']) && $_GET['tag'] == $tag_info['tagID'] ? ' active' : '');

                    echo <<<EOL
                <li class="tag_weight-{$weight}{$active}"><a href="/admin/apps/media/index.php?tag={$tag_info['tagID']}" title="{$tag_info['tag_count']} photos tagged">{$tag}</a></li>
EOL;
                }, $m->get_tag_cloud()); ?>
            </ul>
        </div>
        <div class="clearfix">&nbsp;</div>
        <?php endif; ?>

        <div id="template">


<?php
//display logic





//view = view state, these standard views will do for most single table interactions, you may need to replace with your own
if (!isset($_GET['view'])) { $_GET['view'] = null; }

switch ($_GET['view']) {
case "edit": //show an editor for a row (existing or new)

    //determine if we are editing an existing record, otherwise this will be a 'new'

    $dbaction = "insert";

    //$_GET['id'] = base_convert($_GET['id'], 36, 10);

    if (is_numeric($_GET['id'])) { //if an ID is provided, we assume this is an edit and try to fetch that row from the single table


        $qry = sprintf("SELECT * FROM $primaryTableName WHERE itemID = '%d' AND sysOpen = '1';",
            (int) $_GET['id']);

        $res = $db->query($qry);


        if ($db->valid($res)) {
            $fieldValue = $db->fetch_assoc($res);
            foreach ($fields as &$itemField) {
                //if (is_string($itemField['dbColName'])) {
                    $itemField['dbValue'] = $fieldValue[$itemField['dbColName']];
                //}
            }

            $dbaction = "update";
        }


    } else {
        yell($_GET);
    }


    if ($message != '') {
        print $message;
    }

    $formBuffer = "
                    <form enctype=\"multipart/form-data\" name=\"tableEditorForm\" id=\"tableEditorForm\" method=\"post\" action=\"" . $_SERVER['REQUEST_URI'] .  "\">
                    <table>
                ";




    //print the base fields
    $f=0;

    foreach ($fields as $field) {

        $formBuffer .= "<tr>";
        //prepare an ID and Name string with a validation string in it

        if ($field['dbColName'] != false) {

            $newFieldIDSeed = str_replace(" ", "_", $field['label']);
            $newFieldID = $field['valCode'] . $newFieldIDSeed;

            $field['widgetHTML'] = str_replace("FIELD_ID", $newFieldID, $field['widgetHTML']);

            //set value if one exists
            if ($field['dbColName'] == 'sysStatus') {
                if ($field['dbValue'] == 'active') {
                    $field['widgetHTML'] = str_replace("FIELD_VALUE", 'checked="checked"', $field['widgetHTML']);
                } else {
                    $field['widgetHTML'] = str_replace("FIELD_VALUE", '', $field['widgetHTML']);
                }
            } else {
                if (isset($_POST[$newFieldID]) && $message != '') {
                    $field['dbValue'] = $_POST[$newFieldID];
                }
                $field['widgetHTML'] = str_replace("FIELD_VALUE", $field['dbValue'], $field['widgetHTML']);

                if (isset($field['fileUpload']) && $field['fileUpload'] === true && $field['dbValue'] != '') {

                    $field['widgetHTML'] = '<div class="photoInput"><input type="radio" name="' . $newFieldID . '_keep" id="' . $newFieldID . '_keepYes" value="yes" checked="checked" /> <label for="' . $newFieldID . '_keepYes">Keep existing file</label><br /> <input type="radio" name="' . $newFieldID . '_keep" id="' . $newFieldID . '_keepNo" value="no" />' . $field['widgetHTML'] . '</div> ';
                    $jsFooter .= "\$('#" . $newFieldID . "').click(function() { \$('#" . $newFieldID . "_keepNo').click();});";
                } else if (isset($field['fileUpload']) && $field['fileUpload'] === true) {
                    $field['widgetHTML'] = '<div class="photoInput">' . $field['widgetHTML'] . '</div>';
                } 
            }

        }
        //write in the html
        $formBuffer .= "<td valign=\"top\"><label for=\"".$newFieldID."\">" . $field['label'] . "</label></td><td>" . $field['widgetHTML'] . " <p>" . $field['tooltip'] . "</p></td>";
        $formBuffer .= "</tr>";
    }

    //temp
    $id = null;
    $formAction = null;
    //end temp

    $formBuffer .= "<tr><td colspan=\"2\">
                    <fieldset>
                    <input type=\"hidden\" name=\"dbaction\" id=\"dbaction\" value=\"$dbaction\" />";

    if ($dbaction == "update") { //add in the id to pass back for queries if this is an edit/update form
        $formBuffer .= "<input type=\"hidden\" name=\"id\" id=\"id\" value=\"".$_GET['id']."\" />";
    }

    $formBuffer .= "</fieldset>";

    $formBuffer .= "</table>";
    $formBuffer .= "<div class=\"clearfix\" style=\"margin-top: 10px; height:10px; border-top: 1px dotted #B1B1B1;\">&nbsp;</div>";
    $formBuffer .= "
                    <input type=\"button\" class=\"btnStyle\" name=\"cancelUserForm\" id=\"cancelUserForm\" onclick=\"javascript:window.location.href='" . $_SERVER['PHP_SELF'] . "';\" value=\"Cancel\" />
                    <input class=\"btnStyle green\" type=\"submit\" name=\"submitUserForm\" id=\"submitUserForm\" value=\"Save Changes\" />
                    </fieldset>
                    </td></tr>";                
    $formBuffer .= "</form>";

    //$jsFooter .= "CKEDITOR.replace( 'RQvalALPHTestimonial', {toolbar : 'Basic',uiColor : '#ddd',  width : '600', height : '200', filebrowserUploadUrl : '/js/ckeditor/upload.php'});";
    //print the form
    print $formBuffer;
    break;
default: //(list)

    //list table query:
    //$listqry = "SELECT itemID, title FROM $primaryTableName WHERE sysOpen = '1'";
    //list table field titles

    //$titles[0] = "Title";
    //$titles[1] = "Image File";
    //$titles[2] = "Link";
    //$titles[3] = "In Rotation (1 = yes)";
    //$titles[4] = "Use Exclusively (1 = yes)";

    //print an editor with basic controls
    //print $te->package_editor_list_data($listqry, $titles);

    print "<div id=\"contactSheet\">";
    print $m->getContactSheetMarkup($media_items);
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

    if ($media_items->getTotal() > $media_items->count()):
?>
    <ul id="pagination" style="clear: both;">
        <li><a href="#">Prev</a></li>
        <li><a id="next" href="<?php echo filter_input(INPUT_SERVER, 'SCRIPT_URL', FILTER_UNSAFE_RAW) . '?' . http_build_query(Array('page' => $page + 1) + $query_data, null, '&'); ?>">Next</a></li>
    </ul>
<?php
    endif;


    //to pass more advanced controls, you'll need to create your own $fields array and pass it directly to $te->display_editor_list($fields);
    break;
}


?>
</div>

        <div class="clearfix">&nbsp;</div>

    </div>

</div>

<?php


//end of display logic


include $root . "/admin/templates/footer.php";
