<?php $title = filter_var($title, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_ENCODE_HIGH); ?>

<div class="mediaContactSheetFile">
    <table>
        <tr>
            <td rowspan="3">
                <div class="i-am-lazy">
                    <img class="mediaThumb" src="<?php echo $src; ?>" alt="<?php echo $title; ?>" />
                </div>
                <?php
                if (isset($tagID[0])){
                    echo '<input type="radio" name="isCoverImg" id="isCoverImg_'.$id.'" value="'.$id.'" '.($thumbID == $id ? 'checked="checked" class="currentThumb"':'').' onchange="setTagCover('.$id.','.$tagID[0].',this)" />';
                }
                ?>
            </td>

            <td>
                <textarea class="mediaTitle" id="media_title_<?php echo $id; ?>"><?php echo $title; ?></textarea>
            </td>
        </tr>

        <tr>
            <td>
                <input type="text" class="mediaTags" id="media_tags_<?php echo $id; ?>" value="<?php echo implode(',', array_map(function($tag) { return filter_var($tag, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_ENCODE_HIGH); }, $tags)); ?>" />
            </td>
        </tr>

        <tr>
            <td>
                <?php
                if (isset($tagID[0])){
                    echo '<input type="button" class="btnStyle blue noPad" id="setCover_'.$id.'" onclick="javascript:changeTagCover('.$id.',this)" value="Set as Cover" />';
                }
                ?>
                <input class="btnStyle red noPad"  id="btnDelete_<?php echo $id; ?>" type="button" onclick="javascript:confirmDelete('?action=delete&id=<?php echo $id; ?>');" value="Delete" />
            </td>
        </tr>
    </table>
</div>