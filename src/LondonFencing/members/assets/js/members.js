jQuery(document).ready(function($){
    
    $('#emailAll').click(function(){
        ($(this).is(':checked')) ?  $(':input[type=checkbox]:gt(0)','#adminTableList_email').attr('checked',true): $(':input[type=checkbox]:gt(0)','#adminTableList_email').attr('checked',false);

    });
    
    $(':input[name=goFilter]').click(function(){
        $(this).parent().submit();
    });
});

