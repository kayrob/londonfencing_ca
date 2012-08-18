jQuery(document).ready(function($){
    
    $('#emailAll').click(function(){
        ($(this).is(':checked')) ?  $(':input[type=checkbox]:gt(0)','#adminTableList_email').attr('checked',true): $(':input[type=checkbox]:gt(0)','#adminTableList_email').attr('checked',false);

    });
    
    $(':input[name=goFilter]').click(function(){
        $(this).parent().submit();
    });
    
    $('#OPvalDATEBirth_Date').datepicker({
        dateFormat : 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        yearRange: "-80:+0"
        
    });
    
    $('#adminTableList_email select').change(function(){
        $('option', this).each(function(){
                var parID = $(this).parent().attr('id').split("_");
                if ($(this).is(':selected')){
                     var opts = $(this).val().split("-");
                     if (opts.length == 2 && parID.length == 2){
                         var params = "season="+opts[0]+"&id="+opts[1]+"&uid="+parID[1];
                         window.location.href = "/admin/apps/members/index?view=edit&"+params;
                     }
                }
        });
    });
});

