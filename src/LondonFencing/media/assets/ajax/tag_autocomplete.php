<?php
require_once('../../../../../inc/init.php');
if (isset($_GET['term'])) {
    $qry = sprintf("SELECT tag FROM tblMediaTags WHERE sysStatus='active' AND sysOpen='1' AND tag LIKE '%s%%'",
        $db->escape($_GET['term']));
    $res = $db->query($qry);
    $tags = array();
    if ($db->valid($res)) {
        while ($t = $db->fetch_assoc($res)) {
            $tags[] = $t['tag'];
        }
    }
    print json_encode($tags);
}