jQuery(document).ready(function($){
    
        $('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd'
        });
        $('.timepicker').timepicker({
            timeFormat: 'hh:mm TT',
            ampm: true
        });
        $('#RQvalDATEBirth_Date').datepicker({
                dateFormat : 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                yearRange: "-80:_0"
        });
        $('#emailAll').change(function(){
            if ($(this).is(':checked')){
                $('input[id*=eList_]').each(function(){
                    $(this).attr('checked',true);
                });
            }
            else{
                $('input[id*=eList_]').each(function(){
                    $(this).attr('checked',false);
                });
            }
        });
        
        $(':input[name=goFilter]').click(function(){
            $(this).parent().submit();
        });
        
        $('#history tr').each(function(){
                $(':input:eq(0)', this).change(function(){
                    var rID = $(this).attr('id').replace('editPayment_','');
                    $('#editDate_'+rID).attr('disabled',false);
                    $('#editAmount_'+rID).attr('disabled',false);
                    $('#resetPayment_'+rID).attr('checked', false);
                });
                $(':input:eq(1)', this).change(function(){
                    var rID = $(this).attr('id').replace('deletePayment_','');
                    $('#editDate_'+rID).attr('disabled',true);
                    $('#editAmount_'+rID).attr('disabled',true);
                     $('#resetPayment_'+rID).attr('checked', false);
                });
                $(':input:eq(2)', this).change(function(){
                    var rID = $(this).attr('id').replace("resetPayment_",'');
                    $('#editDate_'+rID).attr('disabled',true);
                    $('#editAmount_'+rID).attr('disabled',true);
                    $('#deletePayment_'+rID).attr('checked', false);
                    $('#editPayment_'+rID).attr('checked', false);
                });
        });
});

