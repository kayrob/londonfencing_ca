jQuery(document).ready(function($){
    
        $('#RQvalDATEStart_Date').datepicker({
                dateFormat : 'yy-mm-dd'
        });
        $('#RQvalDATEEnd_Date').datepicker({
                dateFormat : 'yy-mm-dd'
        });
        $('#RQvalTIMEStart_Time').timepicker({
                timeFormat: 'hh:mm TT',
                ampm: true
        });
        $('#RQvalTIMEEnd_Time').timepicker({
                timeFormat: 'hh:mm TT',
                ampm: true
        });
        $('#RQvalDATERegistration_Open').datepicker({
                dateFormat : 'yy-mm-dd'
        });
        $('#RQvalDATERegistration_Close').datepicker({
                dateFormat : 'yy-mm-dd'
        });
        $('#OPvalDATEPayment_Date').datepicker({
                dateFormat : 'yy-mm-dd'
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
        })
});

