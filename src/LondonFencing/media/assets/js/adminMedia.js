var oMedDefTags = {};
var swfu;

var ajaxRoot = "/admin/assets/media/ajax";

var confirmFiles = function(usrFiles){
    if (usrFiles.length > 0){
        for (var f = 0; f < usrFiles.length; f++){
            if (usrFiles[f]['size'] > 2000000){
                alert("File "+usrFiles[f]['name']+" is too large. Size: "+(usrFiles[f]['size']/1000000)+" MB");
            }
        }
        if (usrFiles.length > 10){
            alert("You have selected too many files (max 10)");
        }
    }
}

var updateTag = function(mediaID, operation, value) {
//    console.log(mediaID + " " + operation + " " + value);

    $.post(ajaxRoot+"/mediaUtility.php", {
        "operation": operation
      , "value":     value
      , "mediaID":   mediaID
    }, function (r) {
        if (!r.status) {
            feedback(r.message, "Problem Saving Change", 2);
        } else {
           feedback(r.message, "Change Saved", 1);
        }
    });
}

var addTagToCloud = function(data){
    var tags = $.parseJSON(data);
    for (var t = 0; t < tags.length; t++){
         var liEl = document.createElement('li');
         var aLiEl = document.createElement('a');
         var aText = document.createTextNode(tags[t][1])+' ';
         aLiEl.href = "/admin/apps/media/index?tag="+tags[t][0];
         aLiEl.appendChild(aText);
         liEl.className = 'tag_weight-0';
         liEl.appendChild(aLiEl);
         document.getElementById('tag_cloud').appendChild(liEl);
        
    }
    console.log(tags);
}

var clearInputList = function(){
    if ($('#upload_tags span').length > 0){
        $('#upload_tags a:eq(0)').click();
        clearInputList();
    }
}

var initMediaAjax = function() {
    $('.mediaContactSheetFile input.mediaTags').each(function(i, el) {
        if(!$(el).hasClass("initializedForTagging")) {

            $(el).addClass("initializedForTagging");

            $(el).tagsInput({
                autocomplete_url : ajaxRoot+'/tag_autocomplete.php'
              , onAddTag: function(tag) {
                    updateTag(this.id, "add_tag", tag);
                }
              , onRemoveTag: function(tag) {
                    updateTag(this.id, "remove_tag", tag);
                }
            });
        }
    });

    $('textarea.mediaTitle').each(function () {
        if(!$(this).hasClass("initializedForTagging")) {
            $(this).addClass("initializedForTagging");
            $(this).change(function() {updateTag(this.id,"update_title", $(this).val());});
        }
    });

    $('#currently').text($('#contactSheet div.mediaContactSheetFile').length);
}

$(document).ready( function() {
    initMediaAjax();

    if (document.getElementById('pagination')) {
        $('#contactSheet').infinitescroll({
            navSelector  : '#pagination'
          , nextSelector : '#next'
          , itemSelector : '#contactSheet div.mediaContactSheetFile'
          , loadingImg   : '/images/ajax-loader.gif'
          , loadingText  : 'Loading Pictures...'
          , donetext     : ''
        }, initMediaAjax);
    }

    $('#upload_tags_input').tagsInput({
        defaultText: 'new tags'
    });
    
    $('#submitTags').click(function(){
        console.log($('#upload_tags_input').val());
        $.post(ajaxRoot+"/mediaUtility.php?isAjax=y", {
            "operation": 'newTag'
            , "value":     $('#upload_tags_input').val()
        }, function (data) {
            if (data == 'false') {
                feedback('Tags could not be added', "Problem Saving Change", 2);
            } 
            else{
                addTagToCloud(data);
                clearInputList();
                feedback('Tags were successfully added', "Change Saved", 1);
            }
        });
    });
    
    document.getElementById('RQvalFILEUpload').onchange = function(){confirmFiles(this.files);}
});